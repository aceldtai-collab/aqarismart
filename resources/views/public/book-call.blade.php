<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __("Book a Call") }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-4">{{ __("Book a Call") }}</h2>
  <p class="text-center text-muted mb-5">{{ __("We'd love to show you around. Send us your details and we'll reach out.") }}</p>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <form method="POST" action="">
        {{-- {{ route('book.call.submit') }} --}}
        @csrf
        <div class="mb-3">
          <label for="name" class="form-label">{{ __("Name") }}</label>
          <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">{{ __("Email") }}</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">{{ __("Message") }}</label>
          <textarea name="message" id="message" rows="4" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __("Send Request") }}</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
