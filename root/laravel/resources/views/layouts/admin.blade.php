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
    <link href="/css/AdminLTE/AdminLTE.min.css" rel="stylesheet">
    <link href="/css/AdminLTE/all_skins.min.css" rel="stylesheet">
    <link href="/dist/css/admin.css?{!! time() !!}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script type="text/javascript" src="https://ajaxzip3.github.io/ajaxzip3.js" charset="utf-8"></script>

</head>
<body class="hold-transition sidebar-mini {!! $pagemeta->body_class !!} skin-blue">

@include('include.admin.ajaxing')

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

                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        @include('include.admin.user_account')
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel (optional) -->
                @include('include.admin.user_panel')

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
                    @if (isset($pagemeta->title)) {!! $pagemeta->title !!} @endif
                    <small>
                        @if (isset($pagemeta->description)) {!! $pagemeta->description !!} @endif
                    </small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ url('') }}/admin/dashboard"><i class="fa fa-dashboard"></i> 管理ページ</a></li>
                    @if (isset($pagemeta->breadcrumbs)) {!! $pagemeta->breadcrumbs !!} @endif
                    @if (isset($pagemeta->title)) <li class="active">{!! $pagemeta->title !!}</li> @endif
                </ol>
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

    <!-- jQuery 2.1.4 -->
    <script src="/js/plugins/jquery-2.1.4.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="/js/plugins/jquery-ui.min.js"></script>

    <!-- Bootstrap 3.3.5 -->
    <script src="/js/AdminLTE/bootstrap.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/js/AdminLTE/app.min.js"></script>
    <script src="/dist/js/admin.js?{!! time() !!}"></script>

    <script>
        $('.draggable').draggable();
    </script>

</body>
</html>
