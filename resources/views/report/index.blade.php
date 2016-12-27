@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Report', 'pageDescription' => 'List of Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
            <div class="box-body" id="form-body">
                <form role="form" action="" method="POST" accept-charset="utf-8">
                    <div class="box-body" id="form-body">
                        <div class="text-right">
                            <div class="btn-group">
                                <a class="btn btn-primary btn-sm" id="filters" href="{{ admin_route_url('report.filters') }}?attributes=@for($i = 0; $i <= $dataAttributesCount; $i ++){!! $dataAttributes[$i] !!},@endfor" data-toggle="modal" data-target="#" title="Filters"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" id="format" href="{{ admin_route_url('report.formatPdf') }}?attributes=@for($i = 0; $i <= $dataAttributesCount; $i ++){!! $dataAttributes[$i] !!},@endfor" title="Formats"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                        </div>
                        <table class="table table-striped">
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
                </form>
            </div>
        </div>
    </section>
@endsection
@section('footer_scripts')
    <script>
        $(".dropdown dt a").on('click', function() {
            $(".dropdown dd ul").slideToggle('fast');
        });
        $(".dropdown dd ul li a").on('click', function() {
            $(".dropdown dd ul").hide();
        });
        function getSelectedValue(id) {
            return $("#" + id).find("dt a span.value").html();
        }
        $(document).bind('click', function(e) {
            var $clicked = $(e.target);
            if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
        });
        $('.mutliSelect input[type="checkbox"]').on('click', function() {
            var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),
            title = $(this).val() + ",";
            if ($(this).is(':checked')) {
                var html = '<span title="' + title + '">' + title + '</span>';
                $('.multiSel').append(html);
                $(".hida").hide();
            } else {
                $('span[title="' + title + '"]').remove();
                var ret = $(".hida");
                $('.dropdown dt a').append(ret);
            }
        });
        $('#submitType').on('click', function() {
            var favorite = [];
            $.each($("input[name='attributes']:checked"), function(){            
                favorite.push($(this).val());
            });
            $('#loadType').html('<table border="1"><tr><td>' + favorite.join(" ") + '</td></tr></table>');
        });
    </script>
@endsection