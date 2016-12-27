<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <form role="form" action="{{ admin_route_url('reports.filterStore') }}" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                {!! csrf_field() !!}
                <h3>Customizable Attributes</h3>
                <div class="box-body" id="form-body">
                    <div id="multiCheck">
                        @foreach ($dataAttributes as $data => $value)
                            <?php 
                                if($value > 1) {
                                    $checked = 'checked'; 
                                } else {
                                    $checked = '';                                     
                                }
                            ?>
                            <input style="cursor:pointer" type="checkbox" id="attributess" <?php echo $checked; ?> name="attributes[]" class="attributes" value="{!! $data !!}" /> {!! $data !!}
                            <br>
                        @endforeach                                        
                    </div>
                    <div class="modal-footer">
                        <i class="fa fa-floppy-ofa-btn"></i>
                        <button class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Submit</span>
                        </button>
                        <a class="btn btn-modal-close" data-dismiss="modal">Close</a>
                    </div>
                    <div id="check"></div>
                </div>
            </form>
        </div>
    </div>
</section>
@section('footer_scripts')
    <script>
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
        }); 
    </script>
@endsection