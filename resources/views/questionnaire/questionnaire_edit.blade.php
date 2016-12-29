@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Questionnaire',
        'pageDescription' => 'Edit Questionnaire',
        'breadcrumbs' => [
            'Questionnaire' => admin_route_url('questionnaire.index'),
            'Edit' => false]
        ]
    )

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <!-- <a href="/users" class="btn btn-link btn-sm">
                        <i class="fa fa-plus fa-arrow-circle-left"></i> back to user list</a> -->
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('questionnaire.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('questionnaire.update', ['id' => $questionnaire->id]) }}"
                      method="POST" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback ">
                            <label for="descripton">Description</label>
                            <input type="text" class="form-control" name="description" id="description"
                                   placeholder="Enter description"
                                   value="{{ old('description', $questionnaire->description) }}" required>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="period">Period</label>
                            <input class="form-control" type="text" name="period" id="datepicker"
                                   value="{{ $questionnaire->start_at }} - {{ $questionnaire->end_at }}"/>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="total_point">Point</label>
                            <input type="number" class="form-control" name="total_point" id="total_point"
                                   value="{{ old('total_point', $questionnaire->total_point) }}"
                                   placeholder="Enter point" required>
                        </div>
                        <br>
                        {{--{{ dd($questionnaire->questions) }}--}}
                        <div id="questions">
                            <h2>Questions</h2>
                            <hr>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach($questionnaire->questions as $item)
                                @php
                                    $counter++;
                                @endphp
                                <div class="question">
                                    <h4 class="questiontitle">Question {{ $counter }}</h4>
                                    <select name="question[]" class="form-control">
                                        @foreach($questions as $question)
                                            @if($item->id == $question->id)
                                                <option value="{{ $question->id }}"
                                                        selected>{{ $question->description }}</option>
                                            @else
                                                <option value="{{ $question->id }}">{{ $question->description }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="show"></div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <div class="pull-left">
                            <button class="btn btn-primary" id="addquestion">
                                <i class="fa fa-plus fa-btn"></i>Add Question
                            </button>
                            <button class="btn btn-danger" id="removequestion">
                                <i class="fa fa-minus fa-btn"></i>Remove Last Question
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save
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
    <script type="text/javascript">

        var questioncounter = {{ count($questionnaire->questions) }} +1;

        $(document).ready(function () {
            $(document).on('focus', 'input#datepicker', function (e) {
                e.preventDefault();
                console.log('datepicker');
                $('input[name="period"]').daterangepicker({
                    timePicker: true,
                    timePicker24Hour: true,
                    locale: {
                        format: 'YYYY/MM/DD HH:mm:ss'
                    }
                });
            });
            console.log(questioncounter);

            updateQuestionCounter(true);
            console.log(questioncounter);
            $(document).on('click', 'button#addquestion', function (e) {
                e.preventDefault();
                var counter = questioncounter + 1;
                $('div.question').last().clone().appendTo('div#questions');
                $('div.question h4').last().replaceWith('<h4 class="questiontitle">Question ' + counter + '</h4>');
                updateQuestionCounter(false);
                window.location.href = '#latest';
            });

            $(document).on('click', 'button#removequestion', function (e) {
                e.preventDefault();
                if (confirm('Are you sure want to delete question?')) {
                    $('div.question').last().remove();
                    updateQuestionCounter(true);
                }
            });
        });

        function updateQuestionCounter(isRemove) {
            if (isRemove) {
                questioncounter = questioncounter - 1;
            } else {
                questioncounter = questioncounter + 1;
            }

            if (questioncounter > 1) {
                $("button#removequestion").prop("disabled", false);
            } else {
                $("button#removequestion").prop("disabled", true);
            }
        }

    </script>
@endsection