@props(['record'])

@if(!$record)
    <div style="font-size: 14px; color: #6b7280; font-style: italic;">No record loaded</div>
@else
    @php
        $analyses = $record->analyses()->with(['results.node', 'media'])->orderBy('created_at', 'desc')->get();
    @endphp

    @if($analyses->isEmpty())
        <div style="font-size: 14px; color: #6b7280; font-style: italic;">No analysis runs found for this submission. Once you save or upload a file, the background queue will process it automatically.</div>
    @else
        @php
            $hasActiveRuns = $analyses->contains(fn ($a) => in_array($a->status->value, ['pending', 'queued', 'processing']));
        @endphp
        <div @if($hasActiveRuns) wire:poll.3s @endif style="display: flex; flex-direction: column; gap: 24px; width: 100%;">
            @foreach($analyses as $analysis)
                <div style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; background-color: #ffffff; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                        <div style="text-align: left;">
                            <span style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Analysis Run</span>
                            <h4 style="font-size: 14px; font-weight: 700; color: #111827; margin: 2px 0 0 0;">ID: {{ $analysis->id }}</h4>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @include('filament.components.status-badge', ['status' => $analysis->status])
                        </div>
                    </div>

                    @php
                        $pdfReport = $analysis->media->where('mime', 'application/pdf')->first();
                    @endphp

                    @if($pdfReport)
                        @include('filament.components.report-card', ['report' => $pdfReport])
                    @endif

                    @if($analysis->status->value === 'failed')
                        <div style="border-radius: 8px; background-color: #fef2f2; border: 1px solid #fee2e2; padding: 12px 16px; margin-top: 12px; text-align: left;">
                            <span style="font-size: 12px; font-weight: 600; color: #991b1b; display: block;">Execution Error</span>
                            <p style="font-size: 13px; color: #b91c1c; margin: 4px 0 0 0;">{{ $analysis->error }}</p>
                        </div>
                    @endif

                    <!-- Style definitions for loaders -->
                    <style>
                        @keyframes pipeline-spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                        @keyframes pipeline-pulse {
                            0%, 100% { opacity: 1; }
                            50% { opacity: 0.5; }
                        }
                        .pipeline-animate-spin {
                            animation: pipeline-spin 1.2s linear infinite;
                        }
                        .pipeline-animate-pulse {
                            animation: pipeline-pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                        }
                    </style>

                    <!-- Realtime Progress Timeline -->
                    @if(in_array($analysis->status->value, ['pending', 'queued', 'processing']))
                        @php
                            $stagesInfo = [
                                'summary' => 'Summarizing Manuscript',
                                'grammar' => 'Academic Style & Grammar Audit',
                                'references' => 'Citation & Reference Verification',
                                'similarity' => 'Plagiarism & Overlap Check',
                                'reviewer' => 'Double-Blind Review Referee Scoring',
                            ];
                            $completedStages = $analysis->results->pluck('stage.value')->toArray();
                            $totalStagesCount = count($stagesInfo);
                            $completedCount = 0;
                            foreach (array_keys($stagesInfo) as $s) {
                                if (in_array($s, $completedStages)) {
                                    $completedCount++;
                                }
                            }
                            $progressPercent = ($completedCount / $totalStagesCount) * 100;
                        @endphp
                        <div style="margin-top: 20px; border: 1px dashed #3b82f6; border-radius: 8px; padding: 16px; background-color: #eff6ff; text-align: left;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <span style="font-size: 13px; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                    <svg class="pipeline-animate-spin" style="width: 16px; height: 16px; color: #3b82f6;" fill="none" viewBox="0 0 24 24">
                                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    AI Pipeline Processing...
                                </span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e40af;">{{ round($progressPercent) }}%</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div style="width: 100%; height: 6px; background-color: #dbeafe; border-radius: 9999px; overflow: hidden; margin-bottom: 16px;">
                                <div style="width: {{ $progressPercent }}%; height: 100%; background-color: #3b82f6; border-radius: 9999px; transition: width 0.5s ease-in-out;"></div>
                            </div>

                            <!-- Stage List -->
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @php
                                    $foundActive = false;
                                @endphp
                                @foreach($stagesInfo as $stageKey => $stageLabel)
                                    @php
                                        $isCompleted = in_array($stageKey, $completedStages);
                                        $isActive = !$isCompleted && !$foundActive;
                                        if ($isActive) {
                                            $foundActive = true;
                                        }
                                    @endphp
                                    
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if($isCompleted)
                                                <div style="display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 9999px; background-color: #10b981; color: #ffffff;">
                                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                                    </svg>
                                                </div>
                                                <span style="color: #059669; font-weight: 500;">{{ $stageLabel }}</span>
                                            @elseif($isActive)
                                                <div style="display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 9999px; background-color: #3b82f6; color: #ffffff;" class="pipeline-animate-pulse">
                                                    <span style="width: 6px; height: 6px; border-radius: 9999px; background-color: #ffffff;"></span>
                                                </div>
                                                <span style="color: #2563eb; font-weight: 700;" class="pipeline-animate-pulse">{{ $stageLabel }} (Processing...)</span>
                                            @else
                                                <div style="display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 9999px; border: 2px solid #d1d5db; background-color: #ffffff;"></div>
                                                <span style="color: #9ca3af;">{{ $stageLabel }} (Pending)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($analysis->results->isNotEmpty())
                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px; text-align: left;">
                            @foreach($analysis->results as $res)
                                @if($res->stage->value === 'extract')
                                    @continue
                                @endif
                                <div style="border: 1px solid #f3f4f6; border-radius: 8px; padding: 16px; background-color: #fafafa;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                        <h5 style="font-size: 13px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; margin: 0;">{{ $res->stage->value }}</h5>
                                        <span style="font-size: 11px; color: #9ca3af;">
                                            Tokens: {{ $res->tokens }} • {{ $res->execution_time }}ms
                                            @if($res->node)
                                                • Node: {{ $res->node->name }}
                                            @elseif($res->driver)
                                                • Driver: {{ ucfirst($res->driver) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div style="font-size: 13px; color: #374151; white-space: pre-wrap; line-height: 1.6; max-height: 250px; overflow-y: auto; padding-right: 8px;">{{ $res->payload['text'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endif
