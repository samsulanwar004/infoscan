@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Leaderboard', 'pageDescription' => 'List of leaderboard', 'breadcrumbs' => ['Leaderboard' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <h3 class="box-title">
                </h3>
                <div class="box-tools">
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Point</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($leader as $item)
                        <tr>
                            <td class="vertical-middle">
                                {{ $item->rank }}
                            </td>
                            <td class="vertical-middle">
                                {{ ucfirst($item->name) }}
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($item->score, 0,0,'.') }} Pts
                            </td>
                        </tr>
                    @empty
                        <td colspan="8"> There is no record for leaderboard data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection