@extends('mobile.layouts.app', ['title' => $tenant->name, 'subtitle' => 'Public tenant summary'])

@section('content')
    <div class="space-y-4">
        <div class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-6 shadow-lg ring-1 ring-emerald-300/30">
            <div class="text-xs uppercase tracking-[0.2em] text-emerald-300">{{ $tenant->slug }}</div>
            <h2 class="mt-2 text-2xl font-bold text-white">{{ $tenant->name }}</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('mobile.marketplace') }}" class="rounded-2xl bg-white text-emerald-700 px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">{{ app()->getLocale() === 'ar' ? 'السوق' : 'Marketplace' }}</a>
                <a href="/api/mobile/tenants/{{ $tenant->slug }}/home" class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-sm font-semibold text-white hover:bg-white/20">{{ app()->getLocale() === 'ar' ? 'JSON الرئيسية للمنشأة' : 'Tenant home JSON' }}</a>
            </div>
        </div>
    </div>
@endsection
