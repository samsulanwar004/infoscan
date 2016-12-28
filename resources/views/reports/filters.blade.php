<form role="form" action="{{ admin_route_url('reports.filterStore') }}" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tag fa-btn"></i> Customizable Attributes</h4>
    </div>
    <div class="modal-body">                    
        {!! csrf_field() !!}
        <div class="form-group">
            @foreach ($dataAttributes as $data => $value)
                <?php 
                    if($value > 1) {
                        $checked = 'checked'; 
                    } else {
                        $checked = '';                                     
                    }
                ?>
                <input style="cursor:pointer" type="checkbox" id="attributes" <?php echo $checked; ?> name="attributes[]" class="attributes" value="{!! $data !!}" /> {!! $data !!}
                <br>
            @endforeach                                        
            <fieldset>
                <input type="checkbox">
                <span class="myClass">
                    <p>This is the text.</p>
                </span>        
            </fieldset>
        </div>
        <div class="modal-footer">
            <a class="btn btn-modal-close" data-dismiss="modal">Close</a>
        </div>                
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $('#submit').click(function() {            
            $.ajax({
                url: '/reports',
                type: "post",
                data: { 'attributes':$('input[name=attributes]').val(), '_token': $('input[name=_token]').val() },
                success: function(data) {
                    alert(data);
                }
            });      
        }); 
        $('input[type="checkbox"]').on('change', function() {
            $(this).closest('fieldset').find('.myClass').toggle(!this.checked);
            alert("tes");
        });    
    }); 
</script>