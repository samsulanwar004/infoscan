<form id="modalForm" action="{{ admin_route_url('points.store') }}"  method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Add </span> Task</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Task Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Task Name" required="required">
                </div>
            </div>

            @foreach($levels as $level)
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">{{ $level->name }}</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="levels[{{ $level->id }}]" placeholder="Point {{ $level->name }}" required="required">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save</span>
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
                if(responseData.status == 'ok') {
                    $('#modalForm')[0].reset();
                }
                setTimeout(function () {
                    REBEL.removeAllMessageAlert();
                }, 2000)
            });
        });

        $('form').on('focus', 'input[type=number]', function (e) {
          $(this).on('mousewheel.disableScroll', function (e) {
            e.preventDefault()
          })
        });
        $('form').on('blur', 'input[type=number]', function (e) {
          $(this).off('mousewheel.disableScroll')
        });
    });
</script>
