@props(['record'])

@if(!$record)
    <div style="font-size: 14px; color: #6b7280; font-style: italic;">No record loaded</div>
@else
    @php
        $analyses = $record->analyses()->with(['results', 'media'])->orderBy('created_at', 'desc')->get();
    @endphp

    @if($analyses->isEmpty())
        <div style="font-size: 14px; color: #6b7280; font-style: italic;">No analysis runs found for this submission. Once you save or upload a file, the background queue will process it automatically.</div>
    @else
        <div style="display: flex; flex-direction: column; gap: 24px; width: 100%;">
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

                    @if($analysis->results->isNotEmpty())
                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-top: 16px; text-align: left;">
                            @foreach($analysis->results as $res)
                                @if($res->stage->value === 'extract')
                                    @continue
                                @endif
                                <div style="border: 1px solid #f3f4f6; border-radius: 8px; padding: 16px; background-color: #fafafa;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                        <h5 style="font-size: 13px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; margin: 0;">{{ $res->stage->value }}</h5>
                                        <span style="font-size: 11px; color: #9ca3af;">Tokens: {{ $res->tokens }} • {{ $res->execution_time }}ms</span>
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
