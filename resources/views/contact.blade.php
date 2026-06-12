<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact — {{ config('app.name', 'Project Manager') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|space-grotesk:500,600,700&display=swap" rel="stylesheet">
    <script>
        (function () {
            const stored = window.localStorage.getItem('pm-theme');
            const theme = (stored === 'dark' || stored === 'light')
                ? stored
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="min-h-screen font-sans antialiased app-shell">
    <div id="projectmanager-contact"></div>
</body>
</html>
