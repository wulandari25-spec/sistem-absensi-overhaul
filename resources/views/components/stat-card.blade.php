@props(['title', 'value' => '0', 'icon' => '', 'color' => 'brand', 'subtitle' => '', 'id' => '', 'animate' => false])

@php
    $colorMap = ['brand' => 'from-brand-500 to-brand-700', 'emerald' => 'from-emerald-500 to-emerald-700', 'amber' => 'from-amber-500 to-amber-700', 'rose' => 'from-rose-500 to-rose-700', 'violet' => 'from-violet-500 to-violet-700', 'cyan' => 'from-cyan-500 to-cyan-700'];
    $bgColor = ['brand' => 'bg-brand-50 dark:bg-brand-950/30', 'emerald' => 'bg-emerald-50 dark:bg-emerald-950/30', 'amber' => 'bg-amber-50 dark:bg-amber-950/30', 'rose' => 'bg-rose-50 dark:bg-rose-950/30', 'violet' => 'bg-violet-50 dark:bg-violet-950/30', 'cyan' => 'bg-cyan-50 dark:bg-cyan-950/30'];
    $textColor = ['brand' => 'text-brand-600 dark:text-brand-400', 'emerald' => 'text-emerald-600 dark:text-emerald-400', 'amber' => 'text-amber-600 dark:text-amber-400', 'rose' => 'text-rose-600 dark:text-rose-400', 'violet' => 'text-violet-600 dark:text-violet-400', 'cyan' => 'text-cyan-600 dark:text-cyan-400'];
    $shadowColor = ['brand' => 'shadow-brand-500/10', 'emerald' => 'shadow-emerald-500/10', 'amber' => 'shadow-amber-500/10', 'rose' => 'shadow-rose-500/10', 'violet' => 'shadow-violet-500/10', 'cyan' => 'shadow-cyan-500/10'];
@endphp

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-lg {{ $shadowColor[$color] ?? '' }} hover:shadow-xl transition-shadow duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</p>
            <p class="text-4xl font-extrabold mt-2 {{ $textColor[$color] ?? 'text-slate-800 dark:text-white' }}" @if($id) id="{{ $id }}" @endif>{{ $value }}</p>
            @if($subtitle)<p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">{{ $subtitle }}</p>@endif
        </div>
        <div class="w-12 h-12 rounded-xl {{ $bgColor[$color] ?? 'bg-slate-100' }} flex items-center justify-center">{!! $icon !!}</div>
    </div>
</div>
