<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <form role="form" action="{{ admin_route_url('report.filterStore') }}" method="" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                <h3>Customizable Attributes</h3>
                <div class="box-body" id="form-body">
                    <div id="multiCheck">
                        @for ($i = 1; $i <= $dataAttributesCount; $i ++)
                            <input type="checkbox" id="attributes" name="attributes[]" class="attributes" value="{!! $dataAttributes[$i] !!}" /> {!! $dataAttributes[$i] !!}
                            <br>
                        @endfor                                        
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
        i = 0;
        $('#submit').click(function() {
            var arr = [];
            $('.attributes:checked').each(function() {
                arr[i++] = $(this).val();
            });
            console.log(arr);
        });        
        function getChecked() { 
            var chkArray = [];
            $("#multiCheck input:checked").each(function() {
                chkArray.push($(this).val());
            });
            var selected;
            selected = chkArray.join(',') + ",";
            if(selected.length > 1){
                alert("You have selected " + selected); 
            } else {
                alert("Please at least one of the checkbox");   
            }
        }
    </script>
@endsection