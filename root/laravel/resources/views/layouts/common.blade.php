<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', env('APP_NAME'))</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('shared/css/dest/style.css') }}">
    <link rel="stylesheet" href="{{ asset('shared/css/dest/style_' . $skin . '.css') }}">
</head>

<body class="">


@include('include.calendar')

<div class="wrapper">

    <header class="header">
        <h1><a href="/">日本の潮位情報</a></h1>
        <ul class="lists navi__lists navi">
            <li class="list navi__list list--monospaced"><a class="navi__list__btn" href="/"><i class="fa fa-home"></i><span class="navi__text">HOME</span></a></li>
            <li class="list navi__list list--monospaced"><a class="navi__list__btn" href="#"><i class="fa fa-envelope-o"></i><span class="navi__text">CONTACT</span></a></li>

            <li class="list navi__list list--monospaced">
                <a class="navi__list__btn" href="#" id="trigChangeSkin" data-skin="{{ $skin }}">
                    @include('include.skin', ['skin' => $skin])
                </a>
            </li>
        </ul>
    </header>


    <article class="content">
        @yield('content')
    </article>


    <footer class="footer">
        <p class="footer__copyright">Copyright &copy; 2014- <a href="/">日本の潮位</a> All rights reserved.</p>
    </footer>

</div>

<script src="{{ asset('shared/js/dest/script.js') }}" defer></script>

<?php /*
<script src="/js/jquery-1.11.3.min.js"></script>
<script src="/js/jquery-ui.1.11.4.min.js"></script>
<script src="/js/jquery.easing.1.3.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="/js/infobox.js"></script>
<script src="/js/markerclusterer.js"></script>
<script src="/js/gglmap.js.php?p=AK"></script>
<script src="/js/gglmap.js"></script>
<script src="/js/script.js"></script>
*/ ?>

</body>
</html>
