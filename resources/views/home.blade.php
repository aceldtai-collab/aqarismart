@include('layouts.public-site', [
    'landing' => $landing,
    'categories' => $categories ?? collect(),
])
