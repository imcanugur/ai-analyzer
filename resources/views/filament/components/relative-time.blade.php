@props(['time'])

@if(!$time)
    -
@else
    @php
        $date = $time instanceof \Carbon\Carbon ? $time : \Carbon\Carbon::parse($time);
    @endphp

    <div style="display: flex; align-items: center; font-size: 13px; color: #4b5563; padding-top: 4px;">
        <svg style="width: 16px; height: 16px; margin-right: 6px; color: #9ca3af; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0 Z" />
        </svg>
        <span>{{ $date->diffForHumans() }} <span style="color: #9ca3af; font-size: 11px; margin-left: 4px;">({{ $date->format('Y-m-d H:i') }})</span></span>
    </div>
@endif
