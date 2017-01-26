<table id="point-pivot" style="width:700px;height:300px"></table>

<script type="text/javascript">
    $(function() {
         loadPointData();
    });

    function loadPointData() {
        $('#point-pivot').pivotgrid({
            //url:'/js/pivotgrid_data1.json',
            url:'/promo-points/get-promo-level-table',
            method:'get',
            width: '100%',
            height: '450',
            pivot:{
                rows:['City'],
                columns:['Level'],
                values:[
                    {field:'Point'},
                ]
            },
            forzenColumnTitle:'<span style="font-weight:bold; padding-left: 110px;">City</span>',
            forzenColumnWidth: 300,
            valuePrecision:0,
            valueStyler:function(value,row,index){
                if (/Point$/.test(this.field) && value<1){
                    return 'background:#dd4b39;color:#ffffff'
                }
            },
            onDblClickRow: function(row) {
                var link = $('#modal-edit');
                var mystr = row._tree_field;
                var myarr = mystr.split(" ");
                var nameLink = 'promo-points/'+myarr[0]+'/edit';
                link.attr('href', nameLink);

                link.trigger('click');
            }
        });
    }
</script>