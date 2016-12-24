<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <div class="box-body" id="form-body">
                <table class="table table-striped" border="1">
                    <thead>
                        <tr>
                            @foreach ($dataAttributes as $data)
                                <th align="center">{!! $data !!}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($dataAttributes as $data)
                                <th>&nbsp;</th>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>