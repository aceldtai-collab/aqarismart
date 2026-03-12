@extends('layouts.app')

@section('title', __('My Profile - :tenant', ['tenant' => app(\App\Services\Tenancy\TenantManager::class)->tenant()?->name ?? config('app.name')]))

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6">
        <div class="grid grid-cols-12 gap-6">
            <aside class="col-span-12 md:col-span-3">
                <nav class="rounded-lg bg-white shadow">
                    <a href="{{ route('resident.profile') }}" class="block px-4 py-3 text-sm font-medium text-indigo-700 border-l-4 border-indigo-600 bg-indigo-50">{{ __('My Profile') }}</a>
                </nav>
            </aside>

            <div class="col-span-12 md:col-span-9 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('resident.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
