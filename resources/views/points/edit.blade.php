<form id="modalForm" action="{{ admin_route_url('points.update', ['id' => $task->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Edit </span> Task</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-12">
                <div class="form-group name">
                    <label for="name">Task Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $task->name }}" placeholder="Task Name" readonly="readonly" required="required">
                </div>
                <?php
                    $code = explode('|', $task->code);
                    $types = config('common.tasks.types.'.$code[0]);
                    $modes = config('common.tasks.select_mode.'.$code[0].'.'.$code[1].'.label');
                    $modeType = strtolower($modes);
                ?>
                <div class="form-group task_type">
                    <label for="name">Task Type</label>
                    <select name="task_type" class="form-control" id="task-type">
                        @foreach(config('common.tasks.types') as $key => $type)
                        <option value="{{ $key }}" nameValue="{{ $type }}" @if($types == $type) selected @endif>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="receipt" @if($types != 'Receipt') style="display: none;" @endif>
                    <div class="form-group range_start">
                        <label for="range_start">Start Range</label>
                        <input type="number" name="range_start" value="{{ $point->range_start }}" id="range-start" class="form-control" placeholder="Enter Start range">
                    </div>

                    <div class="form-group range_end">
                        <label for="range_end">End Range</label>
                        <input type="number" name="range_end" value="{{ $point->range_end }}" id="range-end" class="form-control" placeholder="Enter End range">
                    </div>
                </div>
                <div id="general" @if($types == 'Receipt') style="display: none;" @endif>
                    <div class="form-group mode_type">
                        <label for="name">Task Mode</label>
                        <select name="task_mode" class="form-control" id="task-mode" readonly required>
                            <option value="{{ $code[1] }}">{{ $modes }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="point">Point</label>
                    <input type="number" name="point" value="{{ $point->point }}" id="point" class="form-control" placeholder="Enter Point" required="required">
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container pull-left">
            <a class="btn btn-danger" id="delete-task">Delete Task</a>
        </div>
        <div class="button-container">
            <a class="btn btn-link btn-modal-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Task</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>
<form action="{{ route('points.destroy', ['id' => $task->id]) }}" method="POST" id="submit-delete">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>
<script type="text/javascript">

    $("modalForm").ready(function () {

        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');

                    setTimeout(function(){
                        window.location.href = 'points';
                    },3000);
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

        $('#task-type').on('change', function(e) {
            var type = $(this).find(':selected').attr('nameValue');
            if (this.value == 'a') {
                $('#range-start').removeAttr('disabled');
                $('#range-end').removeAttr('disabled');
                $('#task-mode').attr('disabled', 'disabled');
                $('#receipt').show();
                $('#general').hide();
                $('#name').val(type);
            } else if (this.value == 'b') {
                $('#general').show();
                $('#receipt').hide();
                $('#task-mode').removeAttr('disabled');
                $('#range-start').attr('disabled', 'disabled');
                $('#range-end').attr('disabled', 'disabled');
                $('#name').val(type);
                $("#task-mode").html('<select name="task_mode" class="form-control" id="task-mode" required><option value="">Select Task Mode</option>@foreach(config("common.tasks.select_mode.b") as $key => $mode)<option value="{{ $key }}" nameValue="{{ $mode["label"] }}">{{ $mode["label"] }}</option>@endforeach</select>');
            } else if (this.value == 'c') {
                $('#general').show();
                $('#receipt').hide();
                $('#task-mode').removeAttr('disabled');
                $('#range-start').attr('disabled', 'disabled');
                $('#range-end').attr('disabled', 'disabled');
                $('#name').val(type);
                $("#task-mode").html('<select name="task_mode" class="form-control" id="task-mode" required><option value="">Select Task Mode</option>@foreach(config("common.tasks.select_mode.c") as $key => $mode)<option value="{{ $key }}" nameValue="{{ $mode["label"] }}">{{ $mode["label"] }}</option>@endforeach</select>');
            }

        });

        $('#range-start').on('keyup', function() {
            var type = $('#task-type').find(':selected').attr('nameValue');
            var end = $('#range-end').val();
            $('#name').val(type+' '+this.value+' - '+end);
        });

        $('#range-end').on('keyup', function() {
            var type = $('#task-type').find(':selected').attr('nameValue');
            var start = $('#range-start').val();
            $('#name').val(type+' '+start+' - '+this.value);
        });

        $('#task-mode').on('change', function(e) {
            var type = $('#task-type').find(':selected').attr('nameValue');
            var mode = $(this).find(':selected').attr('nameValue');
            $('#name').val(type+' '+mode);
        });

        $('#delete-task').on('click', function () {
            if (confirm('Are you sure you want to delete this task point ?')) {
                $('#submit-delete').submit();
            } else {
                return false;
            }
        });
    });
</script>

