<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta id="token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">
    <!--[if lt IE 9]>
    <script src="{{ elixir('js/ie-support.js') }}"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
@include('partials.alert')
<!-- Site wrapper -->
<div class="wrapper">

@include('partials.header')

@include('partials.menu')
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.7
        </div>
        <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
        reserved.
    </footer>

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<script src="{{ elixir('js/vendor.js') }}"></script>
<script src="{{ elixir('js/admin.js') }}"></script>

<script>
    $(document).ready(function () {
        if ($('div[class^="alert-"]:not(".alert-danger")')) {
            setTimeout(function () {
                REBEL.removeAllMessageAlert();
            }, 3000);
        }
    });
</script>

@section('footer_scripts')
@show
</body>
</html>
