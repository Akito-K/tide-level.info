<!DOCTYPE html>
<html lang="ja" class="page-admin">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{!! $pagemeta->description !!}：{!! $pagemeta->title !!}</title>

    <!-- Styles -->
    <link href="/dist/css/style.css" rel="stylesheet">
    <link href="/dist/css/style_{{ $skin }}.css" rel="stylesheet" id="bulletChangeSkin">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PPBTZX');</script>
    <!-- End Google Tag Manager -->

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

</head>
<body class="{!! $pagemeta->body_class !!}">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PPBTZX"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

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

<script src="/dist/js/script.js"></script>

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
