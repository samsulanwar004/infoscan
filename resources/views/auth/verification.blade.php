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
        <a href="#">
            <img src="<?php echo env('CDN_URL', ''); ?>img/lte/logo.png" width="150">
        </a>
    </div>
    <div class="login-box-body" style="text-align: center;">
        @if($founded)
            <h3>Selamat, Akun anda telah terverifikasi di aplikasi gojago</h3>
            <p>Silahkan menggunakan applikasi. Foto terus belanjaanmu untuk mengumpulkan poin!</p>
        @else
            <h3>Token Expired!</h3>
        @endif
    </div>
</div>
</body>
</html>

