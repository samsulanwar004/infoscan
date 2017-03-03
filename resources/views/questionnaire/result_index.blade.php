@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Questionnaire Results', 'pageDescription' => 'List of questionnaire results', 'breadcrumbs' => ['Questionnaires' => admin_route_url('questionnaire.index'), 'Results' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    @if($results->count() > 0)
                        {{ $results->first()->template->description }}
                    @endif
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('questionnaire.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Member</th>
                        <th>Submitted At</th>
                        <th>Point</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $result->member->name }}</td>
                            <td>{{ date_format(date_create($result->created_at), 'd M Y, H:i:s') }}</td>
                            <td class="point">{{ $result->total_point }}</td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <a href="{{ admin_route_url('questionnaire.resultDetail', ['id' => $result->id]) }}"
                                       class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="9"> There is no record for questionnaire result data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection
