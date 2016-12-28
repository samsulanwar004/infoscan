<form id="modalForm" action="{{ admin_route_url('permissions.store') }}" class="form-horizontal" method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-lock fa-btn"></i> <span class="action-title">Add </span> Permission</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="group" class="col-sm-2 control-label">Permissions Group</label>
            <div class="col-sm-8">
                <select class="form-control group-select" name="group" id="group">
                    @foreach($groupOfPermissions as $group)
                        <option value="{{ $group->permission_group }}">{{ $group->permission_group }}</option>
                    @endforeach
                </select>

                <input type="text" class="form-control group-text" name="group_text" style="display: none;">
                <input type="checkbox" name="is_new" style="display: none;">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-default btn-group-type"><i class="fa fa-plus-circle"></i></button>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Permissions Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="Permission name">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Permission</span>
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

        $('.btn-group-type').on('click', function (e) {
            e.preventDefault();

            var checkBoxes = $('input[name="is_new"]');
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));

            if ($('.group-text').css('display').toLowerCase() == 'none') {
                $('.group-select').hide();
                $('.group-text').css('display', 'block');

                var _action = $('.btn-default');
                _action.toggleClass('btn-danger');
                _action.find('i.fa-plus-circle')
                    .removeClass('fa-plus-circle')
                    .addClass('fa-times').css('color', '#fff');
                $('.group-select, .group-text').focus();

            } else {
                $('.group-select').show();
                $('.group-text').css('display', 'none');

                var _action = $('.btn-danger');
                _action.removeClass('btn-danger');
                _action.find('i.fa-times')
                    .removeClass('fa-times')
                    .addClass('fa-plus-circle').css('color', 'inherit');
                $('.group-select, .group-text').focus();
            }
        });
    })
</script>
