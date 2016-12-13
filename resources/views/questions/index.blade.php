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
                    @php
                        setlocale(LC_MONETARY, 'en_US');
                    @endphp
                    @forelse(questionnaire as $item)
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
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    <a href="{{ admin_route_url('questionnaire.show', ['id' => $item->id]) }}"
                                       class="btn btn-default"><i class="fa fa-search"></i> </a>
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
                                       data-message="Are you sure you want to delete this SES ?"
                                    > <i class="fa fa-trash"></i> </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for socio economic status data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
