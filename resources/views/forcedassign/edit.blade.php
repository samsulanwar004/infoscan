@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Forced Assign',
        'pageDescription' => 'Edit forced assign',
        'breadcrumbs' => [
            'Forced assign' => admin_route_url('forced-assign.index'),
            'Edit' => false]
        ]
    )

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('forced-assign.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('forced-assign.update', ['id' => $id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Username & Email</th>
                                <th class="text-right">Check to Assigned</th>
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
                                        <div class="checkbox">
                                            <label>
                                                <input name="assign[]" checked type="checkbox" value="{{ $crowdsource->id }}">
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="6"> There is no record for crowdsource data!</td>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save Forced Assign
                        </button>
                    </div>
                </form>
            </div>
            <div id="loading"></div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<script>
    $(document).ready(function () {
    });

    function myLoading() {
        $('#loading').addClass('overlay');
        document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
    }
</script>
@endsection
