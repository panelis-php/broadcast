<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('broadcast::broadcast.unsubscribed.title') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.08); padding: 40px; max-width: 420px; text-align: center; }
        h1 { margin-top: 0; font-size: 22px; }
        p { color: #555; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ __('broadcast::broadcast.unsubscribed.title') }}</h1>
        <p>{{ __('broadcast::broadcast.unsubscribed.message') }}</p>
    </div>
</body>
</html>
