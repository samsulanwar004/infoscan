@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Crowdsource Account', 'pageDescription' => 'List of crowdsource account', 'breadcrumbs' => ['Crowdsource Account' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Username & Email</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($crowdsources as $crowdsource)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $crowdsource->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td>
                                {{ $crowdsource->name }} <br>
                                <small>{{ $crowdsource->email }}</small>
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <a href="{{ admin_route_url('crowdsource.show', ['id' => $crowdsource->id]) }}" class="btn btn-primary">
                                        <i class="fa fa-eye"> </i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for crowdsource data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script type="text/javascript">

    </script>
@endsection
