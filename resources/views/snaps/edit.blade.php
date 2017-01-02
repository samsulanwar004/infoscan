<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => 'tag']) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> File</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-6">
                <img src="{{$snapFile->file_path}}" alt="" class="margin img-responsive">
            </div>
            <div class="col-md-6" style="overflow-y:scroll;max-height: 500px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="300">Item</th>
                            <th width="100">Qty</th>
                            <th width="200" class="text-right">Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($snapFile->tag as $tag)
                            <tr>
                                <td>#</td>
                                <td width="300"><input type="text" name="tag[name][]" class="form-control input-sm" value="{{ $tag->name }}"></td>
                                <td width="100"><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}"></td>
                                <td width="200" class="text-right"><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ $tag->total_price }}"></td>
                                <input type="hidden" name="tag[id][]" value="{{ $tag->id }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Item</span>
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
    $("modalForm").ready(function () {
        
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
