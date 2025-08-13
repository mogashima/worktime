<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', '勤怠管理システム')</title>

    @vite(['resources/js/app.ts', 'resources/sass/app.scss'])

    <!-- 必要に応じて追加のCSSやメタ情報 -->
</head>

<body>
    <div id="app">
        @yield('content')
    </div>
</body>

</html>
