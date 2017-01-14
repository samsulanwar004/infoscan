@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Questions',
        'pageDescription' => 'Edit Question',
        'breadcrumbs' => [
            'Questions' => admin_route_url('questions.index'),
            'Edit' => false]
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
                    <a href="{{ admin_route_url('questions.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('questions.update', ['id' => $question->id]) }}"
                      method="POST" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body" id="form-body">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Enter description"
                                   value="{{ old('description', $question->description) }}" autofocus>
                        </div>
                        <div class="form-group">
                            <label>Answer type: </label>&nbsp;&nbsp;&nbsp;&nbsp;
                            @if($question->type == 'single')
                                <label>
                                    <input type="radio" id="single" class="question_type" name="type" value="single"
                                           checked> Single
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="multiple" class="question_type" name="type"
                                           value="multiple"> Multiple
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="input" class="question_type" name="type" value="input">
                                    Input
                                </label>
                            @elseif($question->type == 'multiple')
                                <label>
                                    <input type="radio" id="single" class="question_type" name="type" value="single">
                                    Single
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="multiple" class="question_type" name="type" value="multiple"
                                           checked> Multiple
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="input" class="question_type" name="type" value="input">
                                    Input
                                </label>
                            @elseif($question->type == 'input')
                                <label>
                                    <input type="radio" id="single" class="question_type" name="type" value="single">
                                    Single
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="multiple" class="question_type" name="type"
                                           value="multiple"> Multiple
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="radio" id="input" class="question_type" name="type" value="input"
                                           checked> Input
                                </label>
                            @endif
                        </div>
                        <div id="answers">
                            @if($question->type != 'input')
                                @foreach($question->answer as $answer)
                                    <div class="form-group answer">
                                        <label for="answer">Answer</label>
                                        <button class="btn btn-box-tool removeanswer">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                        <input type="text" class="form-control" name="answer[]"
                                               value="{{ old('answer', $answer->description) }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="form-group answer">
                                    <label for="answer">Answer</label>
                                    <button class="btn btn-box-tool removeanswer">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                    <input type="text" class="form-control" name="answer[]" value="{{ old('answer') }}">
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <div class="pull-left">
                            <button class="btn btn-primary" id="addanswer">
                                <i class="fa fa-plus fa-btn"></i>Add Answer
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save Question
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
    <script>

        $(document).ready(function () {

            var answercounter = {{ count($answers)+1 }};
            console.log('answercounter', answercounter);
            var value = $('input[name=type]:checked').val();

            updateAnswerCounter(true);

            if (value == 'input') {
                updateAnswerCounter(false);
            }

            checkType();

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

            $(document).on('change', 'input[type=radio]', function (e) {
                value = $('input[name=type]:checked').val();
                checkType();
            });

            function checkType() {
                console.log(value);
                if (value == 'input') {
                    console.log('input nih');
                    $('div#answers').hide();
                    $('button#addanswer').prop('disabled', true);
                } else {
                    $('div#answers').show();
                    $('button#addanswer').prop('disabled', false);
                }
            }

            function updateAnswerCounter(isRemove) {
                if (isRemove) {
                    answercounter = answercounter - 1;
                } else {
                    answercounter = answercounter + 1;
                }
                console.log('answercounter-', answercounter);
                if (answercounter > 1) {
                    $("button.removeanswer").prop("disabled", false);
                } else {
                    $("button.removeanswer").prop("disabled", true);
                }
            }
        });


    </script>

@endsection