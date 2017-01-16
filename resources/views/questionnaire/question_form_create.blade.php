@if($from == 'question')
    <form role="form" action="{{ admin_route_url('questions.store') }}" method="POST" class="form" id="question_form">
        @elseif($from == 'questionnaire')
            <form role="form" action="#" class="form">
                @endif
                {{ csrf_field() }}
                <input type="hidden" name="_from" value="{{ $from }}">
                <div class="box-body" id="form-body">
                    <div class="form-group">
                        <label for="description">Question</label>
                        <input type="text" name="description" class="form-control" id="answer_desc" placeholder="Enter description"
                               value="{{ old('description') }}" autofocus>
                    </div>
                    <div class="form-group">
                        <label>Answer type: </label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" id="single" class="question_type" name="type" value="single" checked> Single</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" id="multiple" class="question_type" name="type" value="multiple"> Multiple</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="radio" id="input" class="question_type" name="type" value="input"> Input</label>
                    </div>
                    <div id="answers">
                        <div class="form-group answer">
                            <label for="answer">Answer</label>
                            <button class="btn btn-box-tool removeanswer">
                                <i class="fa fa-remove"></i>
                            </button>
                            <input type="text" class="form-control input_answer" name="answer[]" value="{{ old('answer') }}">
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-right">
                    <div class="pull-left">
                        <button class="btn btn-primary" id="addanswer">
                            <i class="fa fa-plus fa-btn"></i>Add Answer
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit_question">
                        <i class="fa fa-save fa-btn"></i> Save Question
                    </button>
                </div>
            </form>