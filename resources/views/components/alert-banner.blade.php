@props(['type' => 'warning', 'count' => 0, 'message' => '', 'id' => 'alert-banner', 'dismissible' => true])

@php
    $styles = [
        'danger' => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-800 dark:text-red-300', 'icon_bg' => 'bg-red-100 dark:bg-red-900/50', 'icon_color' => 'text-red-600 dark:text-red-400'],
        'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-950/30', 'border' => 'border-amber-200 dark:border-amber-800', 'text' => 'text-amber-800 dark:text-amber-300', 'icon_bg' => 'bg-amber-100 dark:bg-amber-900/50', 'icon_color' => 'text-amber-600 dark:text-amber-400'],
        'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'border' => 'border-emerald-200 dark:border-emerald-800', 'text' => 'text-emerald-800 dark:text-emerald-300', 'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/50', 'icon_color' => 'text-emerald-600 dark:text-emerald-400'],
    ];
    $s = $styles[$type] ?? $styles['warning'];
@endphp

<div id="{{ $id }}" class="{{ $s['bg'] }} {{ $s['border'] }} border rounded-2xl p-4 {{ $count > 0 || !empty($message) ? '' : 'hidden' }}" x-data="{ visible: true }" x-show="visible" x-transition>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $s['icon_bg'] }} flex items-center justify-center flex-shrink-0">
            @if($type === 'danger')
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            @elseif($type === 'warning')
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold {{ $s['text'] }}">@if($count > 0)⚠️ {{ $count }} akses mencurigakan terdeteksi hari ini!@else{{ $message }}@endif</p>
            <p class="text-xs {{ $s['text'] }} opacity-75 mt-0.5">Periksa log aktivitas untuk detail lebih lanjut.</p>
        </div>
        @if($dismissible)
        <button @click="visible = false" class="p-1 rounded-lg {{ $s['text'] }} opacity-50 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        @endif
    </div>
</div>
