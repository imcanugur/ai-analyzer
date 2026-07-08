@props(['report'])

@if(!$report || !isset($report->metadata['path']))
    <div style="font-size: 14px; color: #6b7280; font-style: italic;">No report available</div>
@else
    @php
        $externalUrl = $report->metadata['url'] ?? \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($report->metadata['path']);
        $downloadUrl = $externalUrl;
        $size = $report->metadata['size'] ?? 0;
        $fileName = basename($report->metadata['path']);
        $previewHtml = '<iframe :src="blobUrl" style="width: 100%; height: 100%; border: none; border-radius: 8px; background-color: #ffffff;"></iframe>';
        $sizeLabel = number_format($size / 1024, 2).' KB';
        $base64Data = \App\Support\FilamentUI::getReportBase64DataUrl($report);
    @endphp

    <div x-data="{ 
        open: false, 
        blobUrl: '', 
        initBlob(base64Data) { 
            if (!base64Data) return; 
            try { 
                const parts = base64Data.split(','); 
                const mime = parts[0].split(':')[1].split(';')[0]; 
                const raw = window.atob(parts[1]); 
                const rawLength = raw.length; 
                const uInt8Array = new Uint8Array(rawLength); 
                for (let i = 0; i < rawLength; ++i) { 
                    uInt8Array[i] = raw.charCodeAt(i); 
                } 
                const blob = new Blob([uInt8Array], { type: mime }); 
                this.blobUrl = URL.createObjectURL(blob); 
            } catch (e) { console.error(e); } 
        }, 
        closeModal() { 
            this.open = false; 
            if (this.blobUrl) { 
                URL.revokeObjectURL(this.blobUrl); 
                this.blobUrl = ''; 
            } 
        } 
    }" style="margin-top: 8px;">
        <style>
            .modal-btn-text { display: inline-block; }
            @media (max-width: 640px) {
                .modal-btn-text { display: none !important; }
                .modal-header-actions { gap: 6px !important; }
                .modal-header-title { font-size: 13px !important; max-width: 140px !important; }
                .modal-header-container { padding: 12px 16px !important; }
                .modal-body-container { padding: 12px !important; }
                .modal-header-icon { margin-right: 0 !important; }
            }
        </style>
        <div style="display: flex; align-items: center; border: 1px solid #10b981; border-radius: 12px; padding: 12px 16px; background-color: #ecfdf5; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); gap: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #d1fae5; color: #059669; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div style="flex-grow: 1; text-align: left; min-width: 0;">
                <h4 style="font-size: 13px; font-weight: 700; color: #065f46; margin: 0 0 2px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $fileName }}">{{ $fileName }}</h4>
                <p style="font-size: 11px; color: #047857; margin: 0;">{{ $sizeLabel }}</p>
            </div>
            <div style="display: flex; gap: 8px; flex-shrink: 0;">
                <button @click="open = true; initBlob('{{ $base64Data }}'); $event.preventDefault();" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border: 1px solid #68d391; border-radius: 8px; font-size: 12px; font-weight: 600; color: #047857; background-color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f0fff4'" onmouseout="this.style.backgroundColor='#ffffff'">
                    <svg style="width: 14px; height: 14px; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Preview
                </button>
                <a href="{{ $downloadUrl }}" download rel="noreferrer" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border: 1px solid #10b981; border-radius: 8px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #10b981; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                    <svg style="width: 14px; height: 14px; margin-right: 6px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download
                </a>
            </div>
        </div>

        @include('filament.components.preview-modal', [
            'title' => $fileName,
            'sizeLabel' => $sizeLabel,
            'extension' => 'pdf',
            'downloadFileName' => $fileName,
            'previewHtml' => $previewHtml
        ])
    </div>
@endif
