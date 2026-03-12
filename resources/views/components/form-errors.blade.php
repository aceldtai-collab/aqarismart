@if ($errors->any())
    <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc ps-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

