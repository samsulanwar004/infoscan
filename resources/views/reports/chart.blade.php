<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Chart</h3>

                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" id="download" 
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-download"></i> Download <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" id="link-img" download="chart"><i class="fa fa-btn fa-picture-o"></i> Save as Image</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box-body" style="padding-left: {{$size['padding']}}%;"> 
                <div class="chart-js" align="center" style="width: {{$size['width']}}%!important;">
                    <canvas id="myChart" width="400" height="400"></canvas>
                </div>                                  
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div> 
<script type="text/javascript">
    (function() {
        var ctx = document.getElementById("myChart").getContext("2d");
        var data = {!! json_encode($data) !!};
        var type = '{!! $type !!}';
        if (type == 'line' || type == 'bar' || type == 'radar' || type == 'horizontalBar') {
            var options = {
                scales:{
                    yAxes:[{
                        ticks:{
                            beginAtZero:true,                                              
                        },
                        gridLines:{
                            display:false
                        }
                    }]
                },
                title:{
                    display:true,
                    text:type.toUpperCase()+' CHART'
                }
            };
        } else if (type == 'pie' || type == 'doughnut' || type == 'polarArea') {
            if (type == 'pie' || type == 'doughnut') {
                var options = {
                    animation: {
                        duration: 500,
                        easing: "easeOutQuart",
                        onComplete: function () {
                          var ctx = this.chart.ctx;
                          ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
                          ctx.textAlign = 'center';
                          ctx.textBaseline = 'bottom';

                          this.data.datasets.forEach(function (dataset) {

                            for (var i = 0; i < dataset.data.length; i++) {
                              var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
                                  total = dataset._meta[Object.keys(dataset._meta)[0]].total,
                                  mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius)/2,
                                  start_angle = model.startAngle,
                                  end_angle = model.endAngle,
                                  mid_angle = start_angle + (end_angle - start_angle)/2;

                              var x = mid_radius * Math.cos(mid_angle);
                              var y = mid_radius * Math.sin(mid_angle);

                              ctx.fillStyle = '#fff';
                              if (i == 3){ // Darker text color for lighter background
                                ctx.fillStyle = '#444';
                              }
                              var percent = String(Math.round(dataset.data[i]/total*100)) + "%";
                              ctx.fillText(dataset.data[i], model.x + x, model.y + y);
                              // Display percent in another line, line break doesn't work for fillText
                              ctx.fillText(percent, model.x + x, model.y + y + 15);
                            }
                            });               
                        }
                    },
                    title : {
                        display : true,
                        text : type.toUpperCase()+' CHART'
                    },
                };
            } else {
                var options = {
                    animation : {
                        animateScale : true
                    },
                    title : {
                        display : true,
                        text : type.toUpperCase()+' CHART'
                    },
                };
            }
        }

        var chartInstance = new Chart(ctx, {
            type: type,
            data: data,
            options: options,
        });
    })();

    $('#download').on('click', function(){
        var canvas = document.getElementById("myChart");
        var img    = canvas.toDataURL("image/png");
        var link = $('#link-img');

        link.attr('href', img);
    });
</script>