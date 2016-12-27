@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Lucky Draw', 'pageDescription' => 'List of lucky draws', 'breadcrumbs' => ['Lucky draws' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('LuckyDraw.Create')
                    <a href="{{ route('lucky.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Lucky Draw Code</th>
                        <th>Title of Lucky Draw</th>
                        <th>Point</th>
                        <th>Multiple</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($luckys as $lucky)
                        <tr>
                            <td class="vertical-middle">{{ $lucky->luckydraw_code }}</td>
                            <td class="vertical-middle">
                                {{ $lucky->title }} <br>
                            </td>
                            <td class="vertical-middle">{{ $lucky->point }}
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $lucky->is_multiple == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">{{ date_format(date_create($lucky->start_at), 'M, d Y') }}</td>
                            <td class="vertical-middle">{{ date_format(date_create($lucky->end_at), 'M, d Y') }}</td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('LuckyDraw.Update')
                                    <a href="{{ route('lucky.edit', ['id' => $lucky->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('LuckyDraw.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ route('lucky.destroy', ['id' => $lucky->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this lucky draw ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="8"> There is no record for lucky draws data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection