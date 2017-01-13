@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Questionnaire',
        'pageDescription' => 'Create a new questionnaire',
        'breadcrumbs' => [
            'Questionnaire' => admin_route_url('questionnaire.index'),
            'Create' => false]
        ]
    )

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('questionnaire.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip"
                       title="Back"> <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('questionnaire.store') }}" method="POST" class="form"
                      accept-charset="utf-8" id="questionnaire_form">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback ">
                            <label for="descripton">Description</label>
                            <input type="text" class="form-control" name="description" id="description"
                                   placeholder="Enter description" value="{{ old('description') }}" required>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="period">Period</label>
                            <input class="form-control" type="text" name="period" id="datepicker"
                                   value="{{ old('period') }}"/>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="total_point">Point</label>
                            <input type="number" class="form-control" name="total_point" id="total_point"
                                   value="{{ old('total_point') }}" placeholder="Enter point" required>
                        </div>
                        <br>
                        <div id="questions">
                            <div id="question_head">
                                <h2 style="display: inline-block;">Questions</h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button style="display: inline-block;margin-bottom: 10px;" type="button" class="btn btn-default btn-xs create_que" data-toggle="modal"
                                        data-target="#question_form">
                                    Create new question
                                </button>
                            </div><hr>
                            <div class="question">
                                @if(count($questions) > 0)
                                    <h4 class="questiontitle">Question 1</h4>
                                    <select name="question[]" class="form-control">
                                        @foreach($questions as $question)
                                            <option value="{{ $question->id }}">{{ $question->description }}
                                                ({{ $question->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="show"></div>
                                @else

                                    <p>No questions data, please
                                        <button type="button" class="btn btn-default btn-xs create_que"
                                                data-toggle="modal" data-target="#question_form">
                                            Create new question
                                        </button>
                                    </p>
                                @endif
                                <hr>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <div class="pull-left">
                            <button type="button" class="btn btn-primary" id="addquestion">
                                <i class="fa fa-plus fa-btn"></i>Add Question
                            </button>
                            <button type="button" class="btn btn-danger" id="removequestion">
                                <i class="fa fa-minus fa-btn"></i>Remove Last Question
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

        <!-- modal -->
        <div class="modal fade" id="question_form" role="form">
            <div class="modal-dialog" style="background-color: white;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create new question</h4>
                </div>
                <div class="modal-body">
                    @include('questionnaire.question_form_create', ['from' => 'questionnaire'])
                </div>
            </div>
        </div>

        <div class="modal fade" id="question_success" role="form">
            <div class="modal-dialog" style="background-color: white;">
                <div class="modal-body">
                    <h4 style="text-align: center;">Question Submitted.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        <!-- /.modal -->

    </section>

    <!-- /.content -->

@endsection

@section('footer_scripts')
    <script type="text/javascript">

        var questioncounter = 2;
        var answercounter = 2;
        var max = {{ count($questions) }};

        $(document).ready(function () {

            updateAnswerCounter(true);

            $(document).on('click', 'button#addanswer', function (e) {
                e.preventDefault();
                $('div.answer').last().clone().appendTo('div#answers');
                $('div.answer').last().find('input[type=text]').val('');
                updateAnswerCounter(false);
            });

            $(document).on('click', 'button.removeanswer', function (e) {
                e.preventDefault();
                if (confirm('Are you sure want to delete answer?')) {
                    $(this).closest('.answer').remove();
                    updateAnswerCounter(true);
                }
            });

            $(document).on('focus', 'input#datepicker', function (e) {
                e.preventDefault();
                $('input[name="period"]').daterangepicker({
                    timePicker: true,
                    timePicker24Hour: true,
                    locale: {
                        format: 'YYYY/MM/DD HH:mm:ss'
                    }
                });
            });

            updateQuestionCounter(true);

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

            $(document).on('click', 'button.create_que', function (e) {
                $('#question_form input[type=text]').val('');
                $('#question_form input[type=radio]').attr('checked', false);
            });

            $(document).on('click', 'button#submit_question', function (e) {
                e.preventDefault();

                var answers_field = $('input:text.input_answer').serializeArray();
                var answers = {};
                $.each(answers_field, function (key, value) {
                    answers[key] = (value.value);
                });

                var formData = {
                    'description': $('input#answer_desc').val(),
                    'type': $('input[name=type]:checked').val(),
                    'answer': answers,
                    '_from': $('input[name=_from]').val(),
                    '_token': $('input[name=_token]').val()
                };

                $.ajax({
                    type: 'POST',
                    url: '{{ url('questions') }}',
                    data: formData,
                    dataType: 'json',
                    encode: true
                }).done(function (data) {
                    $('.question select').append('<option value="' + data['id'] + '">' + data['description'] + ' (' + data['type'] + ')</option>');
                    $('#question_form').modal('toggle');
                    $('#question_success').modal('toggle');
                });
            });

            $('#question_form input[type=radio]').change(function () {
                var value = $('input[name=type]:checked').val();
                if (value == 'input') {
                    $('div#answers').hide();
                    $('.answer input[type=text]').val('');
                } else {
                    $('div#answers').show();

                }
            });
        });

        function updateQuestionCounter(isRemove) {
            if (isRemove) {
                questioncounter = questioncounter - 1;
            } else {
                questioncounter = questioncounter + 1;
            }

            if (questioncounter > 1 || questioncounter == max) {
                $("button#removequestion").prop("disabled", false);
            } else {
                $("button#removequestion").prop("disabled", true);
            }
        }

        function updateAnswerCounter(isRemove) {
            if (isRemove) {
                answercounter = answercounter - 1;
            } else {
                answercounter = answercounter + 1;
            }

            if (answercounter > 1) {
                $("button.removeanswer").prop("disabled", false);
            } else {
                $("button.removeanswer").prop("disabled", true);
            }
        }
    </script>
@endsection