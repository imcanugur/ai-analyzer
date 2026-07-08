@props([
    'title',
    'sizeLabel',
    'extension',
    'downloadFileName',
    'previewHtml'
])

@php
    $btnColor = (strtolower($extension) === 'pdf') ? '#10b981' : '#2563eb';
@endphp

<template x-teleport="body">
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         style="position: fixed; inset: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);" 
         @keydown.escape.window="closeModal()">
         <div @click.away="closeModal()" 
              style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #ffffff; width: calc(100% - 32px); max-width: 1000px; height: 85vh; border-radius: 16px; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">
              <div class="modal-header-container" style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; background-color: #ffffff; flex-shrink: 0; gap: 12px;">
                  <div style="min-width: 0; flex-grow: 1; text-align: left;">
                      <h3 class="modal-header-title" style="font-size: 16px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $title }}</h3>
                      <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $sizeLabel }} • {{ strtoupper($extension) }}</p>
                  </div>
                  <div class="modal-header-actions" style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                      <a :href="blobUrl" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; font-weight: 600; color: #374151; background-color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" title="Open in New Tab">
                          <svg class="modal-header-icon" style="width: 14px; height: 14px; margin-right: 6px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                          <span class="modal-btn-text">Open External</span>
                      </a>
                      <a :href="blobUrl" :download="blobUrl ? '{{ $downloadFileName }}' : ''" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border: 1px solid {{ $btnColor }}; border-radius: 8px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: {{ $btnColor }}; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" title="Download File">
                          <svg class="modal-header-icon" style="width: 14px; height: 14px; margin-right: 6px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                          <span class="modal-btn-text">Download</span>
                      </a>
                      <button @click="closeModal()" style="padding: 8px; border-radius: 8px; border: 1px solid #d1d5db; background-color: #f9fafb; color: #4b5563; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Close Modal">
                          <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                      </button>
                  </div>
              </div>
              <div class="modal-body-container" style="flex-grow: 1; padding: 24px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: auto; min-height: 0;">
                  {!! $previewHtml !!}
              </div>
         </div>
    </div>
</template>
