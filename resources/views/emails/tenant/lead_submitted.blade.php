@component('mail::message')
# New Inquiry

You have a new inquiry from your public site.

@if($lead->unit)
@php
    $propertyLabel = $lead->unit->property?->name ?? ($lead->unit->translated_title ?: __('Standalone listing'));
@endphp
Unit: {{ $propertyLabel }} — {{ $lead->unit->code }}

@endif
Name: {{ $lead->name }}  
Email: {{ $lead->email }}  
@if($lead->phone)
Phone: {{ $lead->phone }}  
@endif

@if($lead->message)
Message:

> {{ $lead->message }}

@endif

Thanks,
{{ config('app.name') }}
@endcomponent
