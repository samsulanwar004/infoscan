<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Infoscan | Admin Panel</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ elixirCDN('css/app.css') }}">
    <!--[if lt IE 9]>
    <script src="{{ elixirCDN('js/ie-support.js') }}"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <!-- <a href="/"><b>Rekan</b>Raffi</a> -->
        <a href="#">
            <img src="<?php echo env('CDN_URL', ''); ?>/img/lte/logo.png" width="150">
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{{ admin_route_url('login') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control input-lg" placeholder="Email" required autofocus>
                <span class="fa fa-envelope form-control-feedback"></span>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password"
                       class="form-control input-lg" placeholder="Password">
                <span class="fa fa-key form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat btn-lg">
                        <i class="fa fa-sign-in fa-btn"></i>Sign In
                    </button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <!-- <a href="#">I forgot my password</a> --><br>

    </div>
</div>
<script src="{{ elixirCDN('js/vendor.js') }}"></script>
<script src="{{ elixirCDN('js/admin.js') }}"></script>
<script>
    $(document).ready(function () {
        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    })
</script>
</body>
</html>
