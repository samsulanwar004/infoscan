<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snapFile->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="mode" value="{{ $snapFile->mode_type }}">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> File</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-6">
                <img src="{{ config('filesystems.s3url') . $snapFile->file_path }}" alt="" class="margin img-responsive">
            </div>
            <div class="col-md-6" style="overflow-y:scroll;max-height: 300px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50"></th>
                            <th width="300">Product Item</th>
                            <th width="100">Qty</th>
                            <th width="200" class="text-right">Price</th>
                        </tr>
                    </thead>

                    <tbody id="inputs">
                        @foreach($snapFile->tag as $tag)
                            <tr id="input">
                                <td>
                                    <a class="btn btn-box-tool" id="remove">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                                <td width="300"><input type="text" name="tag[name][]" class="form-control input-sm tag-name" value="{{ $tag->name }}" placeholder="Product Name" required="required"></td>
                                <td width="100"><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}" placeholder="QTY" required="required"></td>
                                <td width="200" class="text-right"><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ $tag->total_price }}" placeholder="Total Price" required="required"></td>
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
                <a class="btn btn-default" id="add">
                    <i class="fa fa-plus fa-btn"></i>New Create
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
    $("modalForm").ready(function() {
        
        $('form').on('focus', 'input[type=number]', function(e) {
          $(this).on('mousewheel.disableScroll', function(e) {
            e.preventDefault()
          })
        });
        $('form').on('blur', 'input[type=number]', function(e) {
          $(this).off('mousewheel.disableScroll')
        });

        $('a#add').on('click', function(e) {
            e.preventDefault();
            var countOfTextbox = $('.tag-name').length;

            if(countOfTextbox >= 20) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            $('tbody#inputs').append('<tr id="input'+countOfTextbox+'"><td><a class="btn btn-box-tool" onclick="deleteTag('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td><td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name" placeholder="Product Name" required="required"></td><td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" placeholder="QTY" required="required"></td><td width="200" class="text-right"><input type="number" name="newtag[total][]" class="form-control input-sm" placeholder="Total Price" required="required"><input type="hidden" name="newtag[fileId][]" value="{{ $snapFile->id }}"></td></tr>');
        });

        $('a#remove').on('click', function(e) {
            e.preventDefault();
            if(confirm('Are you sure want to delete this item ?')) {
                $(e.target).closest('#input').remove();
            }
        });
        
    });

    function deleteTag(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input'+e).remove();
        }
    }

</script>
