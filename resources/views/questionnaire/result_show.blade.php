@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Questionnaire Result Detail', 'pageDescription' => 'Detail of questionnaire results', 'breadcrumbs' => ['Questionnaires' => admin_route_url('questionnaire.index'), 'Results' => admin_route_url('questionnaire.result', ['id' => $details->template_id]), 'Detail' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{--                    {{ $details->description }}--}}
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('questionnaire.result', ['id' => $details->template_id]) }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <tr>
                        <th>Questionnaire</th>
                        <td>{{ $details->description }}</td>
                    </tr>
                    <tr>
                        <th>Member</th>
                        <td>{{ $details->member }}</td>
                    </tr>
                    <tr>
                        <th>Point</th>
                        <td>{{ $details->total_point }}</td>
                    </tr>
                    <tr>
                        <th>Submitted at</th>
                        <td>{{ date_format(date_create($details->created_at), 'd M Y, H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Details</th>
                        <td>
                            <ol class="list-group">
                                @foreach($details->questions as $question)
                                    <li class="list-group-item list-group-item-heading list-group-item-info">{{ $question->question .' (' . $question->question_type . ')' }}</li>
                                    <ul class="list-group">
                                        @foreach($question->answers as $answer)
                                            <li class="list-group-item">{{ $answer }}</li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection
