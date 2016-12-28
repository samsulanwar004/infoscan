<form id="modalForm" action="{{ admin_route_url('points.update', ['id' => $task->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Add </span> Task</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-8">
                <div class="form-group name">
                    <label for="name">Task Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $task->name }}" placeholder="Task Name" required="required">
                </div>
            </div>
            <div class="col-md-4" style="overflow-y:scroll;max-height: 250px;">
                <div id="levels" style="overflow: hidden;padding-top: 25px;">
                    @foreach($levels as $level)
                        <div id="level">
                            <?php
                                $levelArray = explode(' ', $level->name);
                            ?>
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
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Task</span>
            </button>
            <a class="btn btn-primary" id="add">
                <i class="fa fa-plus fa-btn"></i>Add Level
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

    $(document).ready(function () {
        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
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
        
        $('a#add').on('click', function (e) {
            e.preventDefault();
            var countOfTextbox = $('.level-name').length; console.log(countOfTextbox);
            var nextLevel = countOfTextbox + 1;

            if(countOfTextbox >= 25) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            $('div#levels').append('<div class="level"><div class="form-group"><label for="name" class="col-sm-4 control-label">Level '+nextLevel+'</label><div class="col-sm-8"><input type="number" class="form-control level-name" name="levels['+nextLevel+']" placeholder="Point Level '+nextLevel+'" required="required"></div></div></div>');
        });
    });
</script>
