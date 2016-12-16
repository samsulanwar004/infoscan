@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Questions', 'pageDescription' => 'List of questions', 'breadcrumbs' => ['Question' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('User.Create')
                    <a href="{{ admin_route_url('questions.create') }}" class="btn btn-box-tool"
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
                        <th>Created at</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        setlocale(LC_MONETARY, 'en_US');
                    @endphp
                    @forelse($questions as $question)
                        <tr>
                            <td>
                                {{ $question->questionnaire_question_code }}
                            </td>
                            <td>
                                {{ $question->description }}<br>
                                <span class="label label-info">{{ $question->type }}</span>
                            </td>
                            <td>
                                {{ $question->created_at }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <a href="{{ admin_route_url('questions.edit', ['id' => $question->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('questions.destroy', ['id' => $question->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this question ?"
                                    > <i class="fa fa-trash"></i> </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for questions data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
