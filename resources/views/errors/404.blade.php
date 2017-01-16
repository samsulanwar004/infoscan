@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => '404 Error Page', 'pageDescription' => 'Page not found', 'breadcrumbs' => ['404 error' => false]])

    <!-- Main content -->
    <section class="content">

        <div class="error-page">
            <h2 class="headline text-yellow"> 404</h2>

            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found ...</h3>
                <p>
                    We could not find the page you were looking for . 
                </p>

                <div class="input-group">
                    <a href="javascript: history.go(-1)" class="btn btn-default"><i class="fa fa-long-arrow-left"></i>  Go back</a>
                    <a href="/" class="btn btn-default"><i class="fa fa-home"></i> Dashboard</a>
                </div>
            </div>
            <!-- /.error-content -->
        </div>
        <!-- /.error-page -->
    </section>
    <!-- /.content -->
@endsection