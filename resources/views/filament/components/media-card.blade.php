@php use App\Support\FilamentUI; @endphp
@props(['media'])

@if(!$media)
    <div style="font-size: 14px; color: #6b7280; font-style: italic;">No file attached</div>
@else
    @php
        $extension = strtolower($media->extension);
        $previewHtml = '';

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            $previewHtml = '<img :src="blobUrl" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" />';
        } elseif (in_array($extension, ['pdf', 'txt', 'html', 'json', 'xml', 'sql', 'css', 'js', 'py', 'php'])) {
            $previewHtml = '<iframe :src="blobUrl" style="width: 100%; height: 100%; border: none; border-radius: 8px; background-color: #ffffff;"></iframe>';
        } elseif (in_array($extension, ['mp4', 'webm', 'ogv'])) {
            $previewHtml = '<video controls :src="blobUrl" style="max-width: 100%; max-height: 100%; border-radius: 8px;"></video>';
        } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'])) {
            $previewHtml = '<audio controls :src="blobUrl" style="width: 100%; max-width: 500px;"></audio>';
        } else {
            $previewHtml = '<div style="text-align: center;"><div style="font-size: 48px; margin-bottom: 12px;">📦</div><h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Preview not available for this format</h4><p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Please download the file or open it externally to view.</p></div>';
        }

        $sizeLabel = number_format($media->size / 1024, 2).' KB';
        $base64Data = FilamentUI::getBase64DataUrl($media);
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
    }">
        <style>
            .modal-btn-text {
                display: inline-block;
            }

            @media (max-width: 640px) {
                .modal-btn-text {
                    display: none !important;
                }

                .modal-header-actions {
                    gap: 6px !important;
                }

                .modal-header-title {
                    font-size: 13px !important;
                    max-width: 140px !important;
                }

                .modal-header-container {
                    padding: 12px 16px !important;
                }

                .modal-body-container {
                    padding: 12px !important;
                }

                .modal-header-icon {
                    margin-right: 0 !important;
                }
            }
        </style>
        <div style="display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; background-color: #f9fafb; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <div style="flex-grow: 1; text-align: left; min-width: 0;">
                <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                    title="{{ $media->original_name }}">{{ $media->original_name }}</h4>
                <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ strtoupper($media->extension) }}
                    • {{ $sizeLabel }}</p>
            </div>
            <button @click="open = true; initBlob('{{ $base64Data }}'); $event.preventDefault();"
                    style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; font-weight: 600; color: #2563eb; background-color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transition: background-color 0.2s; flex-shrink: 0;"
                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                    onmouseout="this.style.backgroundColor='#ffffff'">
                <svg style="width: 14px; height: 14px; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Preview
            </button>
        </div>

        @include('filament.components.preview-modal', [
            'title' => $media->original_name,
            'sizeLabel' => $sizeLabel,
            'extension' => $media->extension,
            'downloadFileName' => $media->original_name,
            'previewHtml' => $previewHtml
        ])
    </div>
@endif
