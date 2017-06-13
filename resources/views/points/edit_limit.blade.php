<form id="modalForm" action="{{ admin_route_url('task.limit.update', ['id' => $limit->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Update</span> Limit</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-12">
                <div class="form-group name">
                    <label for="name">Task Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $limit->task_category }}" placeholder="Task Name" readonly="readonly" required="required">
                </div>

{{--                 <div class="form-group task_type">
                    <label for="name">Task Type</label>
                    <select name="task_type" class="form-control" id="task-type">
                        <option value="0" nameValue="">Select Task</option>
                        @foreach(config('common.tasks.types') as $key => $type)
                        <option value="{{ $key }}" nameValue="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="row">
                    <div class="form-group col-sm-2" style="margin-right: 0px;">
                        <label for="daily">Daily</label>
                        <input type="text" class="form-control input-sm" name="limit_daily" value="{{ $limit->limit_daily }}" placeholder="Limit" required="required">
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="weekly">Weekly</label>
                        <input type="text" class="form-control input-sm" name="limit_weekly" value="{{ $limit->limit_weekly}}" placeholder="Limit" required="required">
                    </div>
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
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function () {

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
                $('#name').val(type);
            } else if (this.value == 'b') {
                $('#name').val(type);
            } else if (this.value == 'c') {
                $('#name').val(type);
            }

        });


    });
</script>
