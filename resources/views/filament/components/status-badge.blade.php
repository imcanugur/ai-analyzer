@props(['status'])

@if(!$status)
    -
@else
    @php
        $value = is_object($status) && isset($status->value) ? $status->value : (string) $status;
        $styles = [
            'pending' => ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'],
            'processing' => ['bg' => '#eff6ff', 'text' => '#1e40af', 'dot' => '#3b82f6'],
            'completed' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'dot' => '#10b981'],
            'failed' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'dot' => '#ef4444'],
            'cancelled' => ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'],
        ];
        $matched = $styles[strtolower($value)] ?? ['bg' => '#f3f4f6', 'text' => '#1f2937', 'dot' => '#4b5563'];
    @endphp

    <span style="display: inline-flex; align-items: center; border-radius: 9999px; font-size: 12px; font-weight: 600; padding: 4px 10px; background-color: {{ $matched['bg'] }}; color: {{ $matched['text'] }}; margin-top: 4px;">
        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $matched['dot'] }}; margin-right: 6px;"></span>
        {{ ucfirst($value) }}
    </span>
@endif
