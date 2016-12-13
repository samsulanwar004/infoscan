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
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('questionnaire.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback ">
                            <label for="descripton">Description</label>
                            <input type="text" class="form-control" name="descripton" id="descripton"
                                   placeholder="Enter descripton" value="{{ old('descripton') }}" required>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="period">Period</label>
                            <input class="form-control" type="text" name="period" id="datepicker"
                                   value="{{ date("Y/m/d H:i:s") }} - {{ date("Y/m/d H:i:s") }}"/>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="total_point">Point</label>
                            <input type="number" class="form-control" name="total_point" id="total_point"
                                   value="{{ old('total_point') }}"
                                   placeholder="Enter point" required>
                        </div>
                        <br>
                        <div id="questions">
                            <h2>Questions</h2>
                            <hr>
                            <div class="question">
                                <div class="text-right">
                                    <button class="btn btn-box-tool" class="removequestion">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                </div>
                                <div class="exist">
                                    <label><input type="radio" name="quest" value="new" id="newquestion"> New Question
                                    </label> &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label><input type="radio" name="quest" value="existing" id="existingquestion">
                                        Existing Question
                                    </label>
                                </div>
                                <div class="new">
                                    <div class="form-group">
                                        <label for="descripton">Description</label>
                                        <input class="form-control" type="text" name="question[description][]"
                                               placeholder="Enter question description">
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Type</label>
                                        <div class="radio">
                                            <label><input type="radio" name="question[type][]" value="single"> Single
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label><input type="radio" name="question[type][]" value="multiple">
                                                Multiple
                                            </label></div>
                                    </div>
                                    <div id="answers">
                                        <div class="text-right">
                                            <button class="btn btn-flat" id="addanswer">
                                                <i class="fa fa-plus-circle"></i> Answer
                                            </button>
                                        </div>
                                        <div class="answer form-group">
                                            <label for="answer">Answer</label>
                                            <button class="btn btn-box-tool" class="removeanswer">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                            <input type="text" class="form-control" name="question[answer][]">
                                        </div>
                                    </div>
                                </div>
                                <div class="existing">
                                    <select name="question">
                                        <option value="A">AAA</option>
                                        <option value="B">BBB</option>
                                        <option value="C">CCC</option>
                                        <option value="D">DDD</option>
                                        <option value="E">EEE</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button class="btn btn-primary" id="addquestion">
                            <i class="fa fa-plus fa-btn"></i>Add Question
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script type="text/javascript">

        var questioncounter = 2;
        var answercounter = 2;

        $(document).ready(function () {
            $("div.new").hide();
            $("div.existing").hide();

            $(".exist").on('change', function (e) {
                e.preventDefault();
                if ($("#newquestion").is(":checked")) {
                    $("div.new").show();
                    $("div.existing").hide();
                } else if ($("#existingquestion").is(":checked")) {
                    $("div.existing").show();
                    $("div.new").hide();
                }
            });

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

            updateQuestionCounter(true);
            updateAnswerCounter(true);

            $(document).on('click', 'button#addquestion', function (e) {
                e.preventDefault();
                $('div.question').last().clone().appendTo('div#questions');
                updateQuestionCounter(false);
                window.location.href = '#latest';
            });

            $(document).on('click', 'button.removequestion', function (e) {
                e.preventDefault();
                if (confirm('Are you sure want to delete question?')) {
                    $(e.target).closest('.question').remove();
                    updateQuestionCounter(true);
                }
            });

            $(document).on('click', 'button#addanswer', function (e) {
                e.preventDefault();
                $('div.answer').last().clone().appendTo('div#answers');
                $('div.answer').last().find('input[type=text]').val('');
                updateAnswerCounter(false);
            });

            $(document).on('click', 'button.removeanswer', function (e) {
                e.preventDefault();
                if (confirm('Are you sure want to delete answer?')) {
                    $(e.target).closest('.answer').remove();
                    updateAnswerCounter(true);
                }
            })
        });

        function updateQuestionCounter(isRemove) {
            if (isRemove) {
                questioncounter = questioncounter - 1;
            } else {
                questioncounter = questioncounter + 1;
            }

            if (questioncounter > 1) {
                $("button.removequestion").prop("disabled", false);
            } else {
                $("button.removequestion").prop("disabled", true);
            }
        }

        function updateAnswerCounter(isRemove) {
            if (isRemove) {
                answercounter = answercounter - 1;
            } else {
                answercounter = answercounter + 1;
            }

            if (questioncounter > 2) {
                $("button.removeanswer").prop("disabled", false);
            } else {
                $("button.removeanswer").prop("disabled", true);
            }
        }
    </script>
@endsection