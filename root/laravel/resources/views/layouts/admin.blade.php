<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', env('APP_NAME'))</title>

    <!-- Styles -->
    <script src="https://kit.fontawesome.com/a6878564e6.js" crossorigin="anonymous"></script>
    <link href="/shared/css/AdminLTE/AdminLTE.min.css" rel="stylesheet">
    <link href="/shared//css/AdminLTE/all_skins.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('shared/css/dest/admin.css') }}">
</head>

<body class="hold-transition sidebar-mini skin-blue">

@include('include.ajaxing')

<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">
        <!-- Logo -->
        <a href="/" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>日</b>潮</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>日本の</b>潮位</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">MENU</li>

                <!-- Optionally, you can add icons to the links -->
                @include('include.admin.navi')

            </ul><!-- /.sidebar-menu -->
        </section>
          <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('title', env('APP_NAME'))
                <small>
                    @yield('description', '...')
                </small>
            </h1>
        </section>

      <!-- Main content -->
        <section class="content">
            <div class="content__body">

                @yield('content')

            </div>
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">Powered by AdminLTE</div>

        <!-- Default to the left -->
        <strong>Copyright &copy; 2018- <a href="#">A2PUBLIC</a>.</strong> All rights reserved.
    </footer>

</div><!-- ./wrapper -->

<!-- AdminLTE App -->
<script src="/shared/js/AdminLTE/app.min.js"></script>

<script src="{{ asset('shared/js/dest/admin.js') }}" defer></script>

</body>
</html>
