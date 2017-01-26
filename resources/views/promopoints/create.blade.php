<form id="modalForm" action="{{ admin_route_url('promo-points.store') }}"  method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Add </span> Promo Point</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-8">
                <div class="form-group name">
                    <label for="name">City Name</label>
                    <select class="form-control" name="name" id="select-city" multiple="multiple">
                        @foreach($provincies as $province)
                            <optgroup label="{{ $province->name }}">
                                @foreach($province->regencies as $regency)
                                    <option value="{{ $regency->name }}">{{ $regency->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="point_city">Point</label>
                    <input type="number" class="form-control" id="point-city" name="point_city" placeholder="Point" required="required">
                </div>

                <div class="form-group">
                    <label for="start_at">Start Date</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="start_at" name="start_at">
                    </div>
                </div>

            </div>
            <div class="col-md-4" style="overflow-y:scroll;max-height: 250px;">
                <div id="levels" style="overflow: hidden;padding-top: 25px;">
                    @forelse($levels as $level)
                        <?php
                            $levelArray = explode(' ', $level->name);
                        ?>
                        <div id="level{{ $levelArray[1] }}">
                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label">{{ $level->name }}</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control level-name" name="levels[{{ $levelArray[1] }}]" placeholder="Point {{ $level->name }}" required="required">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="level">
                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label">Level 1</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control level-name" name="levels[1]" placeholder="Point Level 1" required="required">
                                </div>
                            </div>
                        </div>
                    @endforelse
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

    $(document).ready(function () {

        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
                    $.get( '/promo-points/get-promo-level-table' , function(view){
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

            if(countOfTextbox >= 10) {
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

        $('.datepicker').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            minDate: "<?php echo \Carbon\Carbon::today()->toDateString(); ?>",
            maxDate: -0,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            }            
        });

        $("#select-city").select2({
            placeholder: "Select a City",
            maximumSelectionLength: 1,
        });

    });
</script>
