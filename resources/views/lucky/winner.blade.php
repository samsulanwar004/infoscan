@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Lucky Draw Winners', 'pageDescription' => 'List of lucky draw winners', 'breadcrumbs' => ['Lucky draws' => admin_route_url('lucky.index'),'Lucky draw winners' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('lucky.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Lucky Draw Code</th>
                        <th>Title of Lucky Draw</th>
                        <th>Description</th>
                        <th>Winner & Email</th>
                        <th>Random Number</th>
                        <th>Date Win</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($winners as $winner)

                        <tr>
                            <td class="vertical-middle">{{ $winner->luckydraw->luckydraw_code }}</td>
                            <td>{{ $winner->luckydraw->title }}</td>
                            <td>{{ $winner->luckydraw->description }}</td>
                            <td>
                                {{ $winner->member->name }} <br>
                                <small>{{ $winner->member->email }}</small>
                            </td>
                            <td>{{ $winner->description['random_number'] }}</td>
                            <td class="vertical-middle">{{ date_format(date_create($winner->created_at), 'M, d Y') }}</td>
                        </tr>
                    @empty
                        <td colspan="8"> There is no record for lucky draw winners data!</td>
                    @endforelse
                    </tbody>
                </table>
                {{ $winners->links() }}
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection