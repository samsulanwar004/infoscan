<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <div class="box-body" id="form-body">
                <table class="table table-striped" border="1">
                    <thead>
                        <tr>
                            @for ($i = 0; $i <= $dataAttributesCount; $i ++)
                                <th>{!! $dataAttributes[$i] !!}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @for ($j = 0; $j <= $dataAttributesCount; $j ++)
                                <td>&nbsp;</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>