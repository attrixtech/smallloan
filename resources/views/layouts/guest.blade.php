<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - thrumble.smallloan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 text-xs antialiased">
    <div class="w-full max-w-sm bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
</body>
</html>
