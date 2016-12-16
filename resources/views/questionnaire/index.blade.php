@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Questionnaire', 'pageDescription' => 'List of questionnaire', 'breadcrumbs' => ['Questionnaire' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('User.Create')
                    <a href="{{ admin_route_url('questionnaire.create') }}" class="btn btn-box-tool"
                       data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Period</th>
                        <th>Author</th>
                        <th>Point</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($questionnaire as $item)
                        <tr>
                            <td>
                                {{ $item->questionnaire_template_code }}
                            </td>
                            <td>
                                {{ $item->description }}
                            </td>
                            <td>
                                {{ $item->start_at }} - {{ $item->end_at }}
                            </td>
                            <td>
                                {{ $item->created_by }}
                            </td>
                            <td>
                                {{ $item->total_point }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <button class="btn btn-default" data-toggle="modal"
                                            data-target="#dataquestionnaire{{ $item->id }}"><i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ admin_route_url('questionnaire.edit', ['id' => $item->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('questionnaire.destroy', ['id' => $item->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this questionnaire ?"
                                    > <i class="fa fa-trash"></i> </a>
                                </div>
                            </td>
                        </tr>

                        <!-- show details -->
                        <div class="modal fade" id="dataquestionnaire{{ $item->id }}" tabindex="-1" role="dialog"
                             aria-labelledby="dataquestionnairelabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close"
                                                data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title dataquestionnairelabel">{{ $item->description }}</h4>
                                        <p>Period: {{ $item->start_at }} - {{ $item->end_at }}</p>
                                    </div>
                                    <div class="modal-body">
                                        <ol>
                                            @foreach($item->questions as $question)
                                                <li>
                                                    <h5>{{ $question->description }}</h5>
                                                    <ul>
                                                        @foreach($question->answer as $answer)
                                                            <li>
                                                                {{ $answer->description }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ol>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /show details -->

                    @empty
                        <td colspan="4"> There is no record for questionnaire data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection
