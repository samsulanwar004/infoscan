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
                    @cando('Questionnaire.Create')
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
                        <th>Status</th>
                        <th>Description</th>
                        <th>Period</th>
                        <th>Author</th>
                        <th>Point</th>
                        <th width="150"></th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($questionnaire as $item)
                        <tr>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ date_format(date_create($item->start_at), 'd M Y H:i:s') . ' - '. date_format(date_create($item->end_at), 'd M Y H:i:s') }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td class="point">{{ $item->total_point }}</td>
                            <td><a href="{{ admin_route_url('questionnaire.result', ['id' => $item->id]) }}"><i class="fa fa-list"></i> See Results</a></td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <button class="btn btn-default" data-toggle="modal"
                                            data-target="#dataquestionnaire{{ $item->id }}"><i class="fa fa-eye"></i>
                                    </button>
                                    @cando('Questionnaire.Publish')
                                    <a href="#" data-id="{{ $item->id }}" class="btn btn-success publish">
                                        <i class="fa fa-check"> </i>
                                    </a>
                                    @endcando
                                    @cando('Questionnaire.Update')
                                    <a href="{{ admin_route_url('questionnaire.edit', ['id' => $item->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando
                                    @cando('Questionnaire.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('questionnaire.destroy', ['id' => $item->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this questionnaire ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
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
                                        <h4 class="modal-title dataquestionnairelabel"
                                            style="margin-bottom: 10px;">{{ $item->description .' (Point: '.$item->total_point.')' }}</h4>
                                        <p>
                                            <i class="fa fa-calendar"></i> {{ date_format(date_create($item->start_at), 'd M Y H:i:s') . ' - '. date_format(date_create($item->end_at), 'd M Y H:i:s') }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <i class="fa fa-user"></i> {{ $item->user->name }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        </p>
                                    </div>
                                    <div class="modal-body">
                                        <ol>
                                            @foreach($item->questions as $question)
                                                <li>
                                                    <h5>{{ $question->description }} ({{ $question->type }})</h5>
                                                    @if($question->type != 'input')
                                                        <ul>
                                                            @foreach($question->answer as $answer)
                                                                <li>
                                                                    {{ $answer->description }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ol>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /show details -->

                        <!-- show details -->
                        <div class="modal fade" id="point_{{ $item->id }}" tabindex="-1" role="dialog"
                             aria-labelledby="pointquestionnairelabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close"
                                                data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title dataquestionnairelabel"
                                            style="margin-bottom: 10px;">{{ $item->description }}</h4>
                                        <p>
                                            <i class="fa fa-calendar"></i> {{ date_format(date_create($item->start_at), 'd M Y H:i:s') . ' - '. date_format(date_create($item->end_at), 'd M Y H:i:s') }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <i class="fa fa-user"></i> {{ $item->user->name }}&nbsp;&nbsp;&nbsp;&nbsp;
                                        </p>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ admin_route_url('questionnaire.update', ['id' => $item->id]) }}"
                                              method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('put') }}
                                            <input class="form-control" type="number" name="total_point"
                                                   value="{{ $item->total_point }}" min="0">
                                            <input class="form-control" type="hidden" name="status" value="publish">
                                            <button class="btn btn-primary change-point" type="submit"
                                                    style="margin-top: 10px;"><i class="fa fa-save fa-btn"></i> Publish
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /show details -->

                    @empty
                        <td colspan="9"> There is no record for questionnaire data!</td>
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
        $(document).ready(function () {
            $('a.publish').on('click', function () {
                point = $(this).parent().parent().parent().find('td.point').html();
                id = $(this).data('id');
                if (point == 0) {
                    $('#point_' + id).modal('show');
                    $('#point_' + id).find('button[type=submit]').attr('disabled', true);
                    inputpoint = $('#point_' + id).find('input[name=total_point]');
                    inputpoint.on('change keyup', function () {
                        if (inputpoint.val() > 0) {
                            $('#point_' + id).find('button[type=submit]').attr('disabled', false);
                        } else {
                            $('#point_' + id).find('button[type=submit]').attr('disabled', true);
                        }
                    });
                } else {
                    location.href = '/questionnaire/publish/' + id;
                }
            });
        });
    </script>
@endsection
