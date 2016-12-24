<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <form role="form" action="{{ admin_route_url('report.filterStore') }}" method="" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                <h3>Customizable Attributes</h3>
                <div class="box-body" id="form-body">
                    <div id="multiCheck">
                        @for ($i = 1; $i <= $dataAttributesCountAs; $i ++)
                            <input style="cursor:pointer" type="checkbox" checked id="attributes" name="attributes[]" class="attributes" value="{!! $dataAttributesAs[$i] !!}" /> {!! $dataAttributesAs[$i] !!}
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
        alert('tes');
    </script>
@endsection