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
                                   placeholder="Enter description" value="{{ old('description') }}" autofocus required>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="period">Period</label>
                            <input class="form-control" type="text" name="period" id="datepicker"
                                   value="{{ old('period') }}" readonly>
                        </div>
                        @cando('Questionnaire.Point')
                        <div class="form-group has-feedback">
                            <label for="total_point">Point</label>
                            <input type="number" class="form-control" name="total_point" id="total_point"
                                   value="{{ old('total_point') }}" placeholder="Enter point" min="0" required>
                        </div>
                        @endcando
                        @cando('Questionnaire.Publish')
                        <div class="form-group has-feedback">
                            <label for="status">Status</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="status" value="publish"> Publish</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="status" value="unpublish"> Unpublish</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="status" value="new" checked> New</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="status" value="void"> Void</label>
                        </div>
                        @endcando
                        <br>
                        <div id="questions">
                            <div id="question_head">
                                <h2 style="display: inline-block;">Questions</h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button style="display: inline-block;margin-bottom: 10px;" type="button"
                                        class="btn btn-default btn-xs create_que" data-toggle="modal"
                                        data-target="#question_form">
                                    Create new question
                                </button>
                            </div>
                            <div class="col-md-11">
                                <select name="question" class="input-lg form-control que">
                                    <option id="selectdisable" disabled selected>Select question</option>
                                    @if(count($questions) > 0)
                                        @foreach($questions as $question)
                                            <option value="{{ $question->id }}">{{ $question->description .' ('. $question->type.')' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-1">
                                <a href="#selected-questions" class="btn btn-primary disabled" id="add-item">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <hr>
                        <h5 style="padding-top: 20px;">Selected Questions</h5>
                        <div id="selected-questions" class="well well-sm"></div>
                        <!-- /.box-body -->

                        <div class="box-footer text-right">
                            <button type="submit" class="btn btn-primary" id="submit">
                                <i class="fa fa-save fa-btn"></i> Save
                            </button>
                        </div>
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
                    <button id="buttonCloseSubmitted" type="button" class="btn btn-default" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
        <!-- /.modal -->

    </section>

    <!-- /.content -->

@endsection

@section('footer_scripts')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">

        var questioncounter = 1;
        var answercounter = 2;
        var questionsIn = [];

        $(document).ready(function () {

            checkInput();

            $("select").select2();

            updateQuestionCounter(true);
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

            $('select[name=question]').on('change', function (e) {
                if ($('select[name=question]').val() != null) {
                    $('a#add-item').removeClass('disabled');
                } else {
                    $('a#add-item').addClass('disabled');
                }
            });

            $('input').on('change', function () {
                checkInput();
            });

            $('a#add-item').on('click', function () {
                que_value = $('select[name=question]').val();
                que_text = $('.select2-selection__rendered').attr('title');
                updateQuestionCounter(false);
                fill = '<div class="input-group selected-items" style="margin-top: 10px; margin-bottom: 10px;">' +
                    '<input type="text" value="' + que_text + '" class="form-control" readonly>' +
                    '<input type="hidden" name="question[]" value="' + que_value + '">' +
                    '<a class="input-group-addon remove-item btn btn-danger" href="#"><i class="fa fa-remove"></i></a>' +
                    '</div>';
                $('#selected-questions').append(fill);
                questionsIn.push(que_value);
                $('select[name=question] option:selected').prop('disabled', 'disabled').trigger('change');
                $('select[name=question] option#selectdisable').prop('selected', true).trigger('change');
                $('select[name=question]').select2();
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

            $(document).on('click', 'button.create_que', function (e) {
                $('#question_form input[type=text]').val('');
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
                    $('select[name=question]').append('<option value="' + data['id'] + '">' + data['description'] + ' (' + data['type'] + ')</option>').trigger('change');
                    $('select[name=question] option:last-child').prop('selected', true).trigger('change');
                    $('#question_form').modal('toggle');
                    $('#question_success').modal('toggle');
                });
            });

            $('#question_form input[type=radio]').change(function () {
                var value = $('input[name=type]:checked').val();
                if (value == 'input') {
                    $('div#answers').hide();
                    $('button#addanswer').prop('disabled', true);
                } else {
                    $('div#answers').show();
                    $('button#addanswer').prop('disabled', false);
                }
            });

            $('button#buttonCloseSubmitted').click(function () {
                answercounter = 1;
                $('div.answer').slice(1).remove();
            });

            $(document).on('click', 'a.remove-item', function (e) {
                e.preventDefault();
                if (confirm('Are you sure to remove this question?')) {
                    updateQuestionCounter(true);
                    rem_val = $(this).parent().find('input[type=hidden]').val();
                    questionsIn = jQuery.grep(questionsIn, function (value) {
                        return value != rem_val;
                    });

                    $(this).closest('.selected-items').remove();
                    $('select option[value=' + rem_val + ']').attr('disabled', false);
                    $('select').select2();
                    checkInput();
                }
            });
        });

        function updateQuestionCounter(isRemove) {
            if (isRemove) {
                questioncounter = questioncounter - 1;
            } else {
                questioncounter = questioncounter + 1;
            }

            if (questioncounter == 0) {
                $('#selected-questions').append('<h5 id="empty">Empty.</h5>');
            } else {
                $('h5#empty').remove();
            }

            checkInput();
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

        function checkInput() {
            if ($('input[name=description]').val() == '' || $('input[name=period]').val() == '' || $('input[name=total_point]').val() == '' || questioncounter < 1) {
                $('button#submit').attr('disabled', true);
            } else {
                $('button#submit').attr('disabled', false);
            }
        }
    </script>
@endsection