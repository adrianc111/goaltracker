<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="index, follow" />
        <meta name="description" content="Set personal goals, build productive habits, and plan your week with our easy-to-use goal-setting templates and habit trackers. Achieve your personal development and productivity goals." />
        <meta name="keywords" content="goals, personal goals, goal setting, habits, weekly plans, habit tracking, productivity, weekly view" />
        <meta property="og:title" content="Oneway | Goal Setting and Weekly Planning">

        <link rel="canonical" href="https://goaltracker.co"/>

        <title inertia>{{ config('app.name', 'Oneway') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        @cookieconsentscripts
    </head>
    <body class="font-sans antialiased">
        @inertia

        @cookieconsentview
    </body>
</html>
