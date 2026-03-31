@extends('mobile.layouts.app', ['title' => 'Aqari Smart Mobile', 'subtitle' => 'Android + iOS shell'])

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white shadow-xl">
            <div class="text-sm uppercase tracking-[0.3em] text-white/70">NativePHP</div>
            <h1 class="mt-3 text-3xl font-bold">Manage real estate on mobile</h1>
            <p class="mt-3 max-w-2xl text-sm text-white/85">Use the drawer to move between marketplace, tenant search, dashboard, units, login, registration, and the public tenant pages.</p>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="{{ route('mobile.marketplace') }}" class="rounded-2xl bg-white text-emerald-700 px-4 py-3 text-center text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">Marketplace</a>
                <a href="{{ route('mobile.dashboard') }}" class="rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 py-3 text-center text-sm font-semibold text-white hover:bg-white/20">Dashboard</a>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2">
            <a href="{{ route('mobile.register') }}" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30 transition hover:shadow-xl hover:ring-emerald-300/50">
                <div class="text-lg font-semibold text-slate-800">Register real estate</div>
                <div class="mt-2 text-sm text-white/70">Create a tenant and get mobile authentication instantly.</div>
            </a>
            <a href="{{ route('mobile.login') }}" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30 transition hover:shadow-xl hover:ring-emerald-300/50">
                <div class="text-lg font-semibold text-slate-800">Login</div>
                <div class="mt-2 text-sm text-white/70">Sign in as staff or resident using the mobile API.</div>
            </a>
            <a href="{{ route('mobile.units.index') }}" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30 transition hover:shadow-xl hover:ring-emerald-300/50">
                <div class="text-lg font-semibold text-slate-800">Units</div>
                <div class="mt-2 text-sm text-white/70">Browse, create, edit, and inspect tenant units.</div>
            </a>
            <a href="{{ route('mobile.tenants.index') }}" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30 transition hover:shadow-xl hover:ring-emerald-300/50">
                <div class="text-lg font-semibold text-slate-800">Tenant search</div>
                <div class="mt-2 text-sm text-white/70">Explore full tenant public summaries and tenant home pages.</div>
            </a>
        </section>
    </div>
@endsection
