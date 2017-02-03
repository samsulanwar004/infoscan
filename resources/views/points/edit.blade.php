<form id="modalForm" action="{{ admin_route_url('points.update', ['id' => $task->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Edit </span> Task</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-8">
                <div class="form-group name">
                    <label for="name">Task Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $task->name }}" placeholder="Task Name" readonly="readonly" required="required">
                </div>

                <div class="form-group task_type">
                    <label for="name">Task Type</label>
                    <select name="task_type" class="form-control" id="task-type">
                        <option value="0" nameValue="">Select Task</option>
                        @foreach(config('common.tasks.types') as $key => $type)
                        <option value="{{ $key }}" nameValue="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group task_type">
                    <label for="name">Task Mode</label>
                    <select name="task_mode" class="form-control" id="task-mode" disabled="disabled">
                        <option value="0" nameValue="">Select Mode</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" style="overflow-y:scroll;max-height: 250px;">
                <div id="levels" style="overflow: hidden;padding-top: 25px;">
                    @foreach($levels as $level)
                        <?php
                            $levelArray = explode(' ', $level->name);
                        ?>
                        <div id="level{{ $levelArray[1] }}">                            
                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label">{{ $level->name }}</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control level-name" name="levels[{{ $levelArray[1] }}]" value="{{ $level->pivot->point }}" placeholder="Point {{ $level->name }}" required="required">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link btn-modal-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Task</span>
            </button>
            <a class="btn btn-default" id="add">
                <i class="fa fa-plus fa-btn"></i>Add Level
            </a>
            <a class="btn btn-danger" id="remove">
                <i class="fa fa-minus fa-btn"></i>Remove Last Level
            </a>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $("modalForm").ready(function () {

        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
                    $.get( '/points/get-task-table' , function(view){ 
                        $(".box-body").html(view);
                    });
                }
            }, true);
        });

        $('form').on('focus', 'input[type=number]', function (e) {
          $(this).on('mousewheel.disableScroll', function (e) {
            e.preventDefault()
          })
        });
        $('form').on('blur', 'input[type=number]', function (e) {
          $(this).off('mousewheel.disableScroll')
        });
        
        $('a#add').on('click', function (e) {
            e.preventDefault();
            var countOfTextbox = $('.level-name').length;
            var nextLevel = countOfTextbox + 1;
            $("a#remove").removeAttr('disabled');

            if(countOfTextbox >= 25) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            $('div#levels').append('<div id="level'+nextLevel+'"><div class="form-group"><label for="name" class="col-sm-4 control-label">Level '+nextLevel+'</label><div class="col-sm-8"><input type="number" class="form-control level-name" name="levels['+nextLevel+']" placeholder="Point Level '+nextLevel+'" required="required"></div></div></div>');
        });

        $('a#remove').on('click', function (e) {
            e.preventDefault();
            var countOfTextbox = $('.level-name').length; 

            if(countOfTextbox <= 1) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            if (confirm('Are you sure want to delete this level?')) {
                $('#level'+countOfTextbox).remove();
            }
            
        });   

        $('#task-type').on('change', function() {
            var type = $(this).find(':selected').attr('nameValue');
            $('#task-mode').removeAttr('disabled');

            if (this.value == 'a') {
                $("#task-mode").html('<select name="task_mode" class="form-control" id="task-mode"><option value="0" nameValue="">Select Mode</option>@foreach(config("common.tasks.select_mode.a") as $key => $mode)<option value="{{ $key }}{{ $mode["value"] }}" nameValue="{{ $mode["label"] }}">{{ $mode["label"] }}</option>@endforeach</select>');
            } else if (this.value == 'b') {
                $("#task-mode").html('<select name="task_mode" class="form-control" id="task-mode"><option value="0" nameValue="">Select Mode</option>@foreach(config("common.tasks.select_mode.b") as $key => $mode)<option value="{{ $key }}{{ $mode["value"] }}" nameValue="{{ $mode["label"] }}">{{ $mode["label"] }}</option>@endforeach</select>');
            } else if (this.value == 'c') {
                $("#task-mode").html('<select name="task_mode" class="form-control" id="task-mode"><option value="0" nameValue="">Select Mode</option>@foreach(config("common.tasks.select_mode.c") as $key => $mode)<option value="{{ $key }}{{ $mode["value"] }}" nameValue="{{ $mode["label"] }}">{{ $mode["label"] }}</option>@endforeach</select>');
            } else {
                $("#task-mode").attr('disabled', 'disabled');
            }      
            $("#name").val(type);
        });

        $('#task-mode').on('change', function() {
            var type = $('#task-type').find(':selected').attr('nameValue');
            var mode = $(this).find(':selected').attr('nameValue');
            $("#name").val(type+' '+mode);
        });
    });
</script>
