@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => '403 Error Page', 'pageDescription' => 'Forbiden access', 'breadcrumbs' => ['403 error' => false]])

    <!-- Main content -->
    <section class="content">

        <div class="error-page">
            <h2 class="headline text-yellow"> 403</h2>

            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! Forbiden access ...</h3>
                <p>
                    You don't have permission to access this page .
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