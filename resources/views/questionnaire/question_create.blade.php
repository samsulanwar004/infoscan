@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Questionnaire Questions',
        'pageDescription' => 'Create a new question',
        'breadcrumbs' => [
            'Question' => admin_route_url('questions.index'),
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
                    <a href="{{ admin_route_url('questions.index') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Back"> <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                @include('questionnaire.question_form_create', ['from' => 'question'])
            </div>
            <div id="loading"></div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script>

        var answercounter = 2;

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

            $('input[type=radio]').change(function(){
                var value = $( 'input[name=type]:checked' ).val();
                if(value == 'input'){
                    $('div#answers').hide();
                    $('.answer input[type=text]').val('');
                } else {
                    $('div#answers').show();
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