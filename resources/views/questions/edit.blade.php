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
                            <label>Type</label>
                            @if($question->type == 'single')
                                <label><input type="radio" name="type" value="single" checked> Single</label>
                                <label><input type="radio" name="type" value="multiple"> Multiple</label>
                            @elseif($question->type == 'multiple')
                                <label><input type="radio" name="type" value="single"> Single</label>
                                <label><input type="radio" name="type" value="multiple" checked> Multiple</label>
                            @endif
                        </div>
                        <div id="answers">
                            <div class="text-right">
                                <button class="btn btn-flat" id="addanswer">
                                    <i class="fa fa-plus-circle"></i> Answer
                                </button>
                            </div>
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
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
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

        var answercounter = {{ count($answers)+1 }};

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
        });

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