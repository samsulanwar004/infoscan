<template>
    <div class="box box-success">
        <div class="box-header with-border">
          <div class="col-md-6">
            <h3 class="box-title"> {{ chartTitle }} </h3>
          </div>
          <div class="col-md-3">
            <select id="period" v-model="timerange" name="period" class="form-control">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
          </div>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="chart">
            <canvas class="chart-area" style="height: 230px; width: 511px;" width="511" height="230"></canvas>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
</template>

<script>
    export default {
        props: ['chartTitle', 'resourceUrl', 'legends'],
        data: function() {
            return {
              timerange: 'daily',
              barChartOptions: {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: 'rgba(0,0,0,.05)',
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
                //String - A legend template
                legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
                //Boolean - whether to make the chart responsive
                responsive: true,
                maintainAspectRatio: true,
                datasetFill: false
              },
              defaultColors: [{
                "fill": false,
                "backgroundColor": "rgba(255, 99, 132, 0.5)",
                "borderColor": "rgb(255, 99, 132)",
                "borderWidth": 1,
              }, {
                "fill": false,
                "backgroundColor": "rgba(255, 159, 64, 0.5)",
                "borderColor": "rgb(255, 159, 64)",
                "borderWidth": 1,
              }, {
                "fill": false,
                "backgroundColor": "rgba(255, 205, 86, 0.2)",
                "borderColor": "rgb(255, 205, 86)",
                "borderWidth": 1,
              }, {
                "fill": false,
                "backgroundColor": "rgba(75, 192, 192, 0.2)",
                "borderColor": "rgb(75, 192, 192)",
                "borderWidth": 1,
              }, {
                "fill": false,
                "backgroundColor": "rgba(153, 102, 255, 0.2)",
                "borderColor": "rgb(54, 162, 235)",
                "borderWidth": 1,
              }],
              periodLabels: {
                "daily": {
                  "1": "Monday",
                  "2": "Tuesday",
                  "3": "Wednesday",
                  "4": "Thursday",
                  "5": "Friday",
                  "6": "Thursday",
                  "7": "Sunday",
                },
                "weekly": {
                  "1": "Week 1",
                  "2": "Week 2",
                  "3": "Week 3",
                  "4": "Week 4",
                  "5": "Week 5",
                },
                "monthly": {
                  "1": "January",
                  "2": "February",
                  "3": "March",
                  "4": "April",
                  "5": "May",
                  "6": "June",
                  "7": "July",
                  "8": "August",
                  "9": "September",
                  "10": "October",
                  "11": "November",
                  "12": "December",
                }
              },
              chartArea: {},
              chartData: {},
              chartLabels: [],
              chartInstance: null,
            }
          },
          computed: {
            _clegends: function () {
                return this.legends.split(", ")
              },
          },
          created: function() {

          },
          mounted: function() {
            this.chartArea = $(this.$el).find('.chart-area').get(0).getContext('2d')
            this.refreshChart()
          },
          methods: {
            /**
             * Loading chart resource data
             * @return $.ajax
             */
            loadChart: function() {
              return $.ajax({
                url: this.resourceUrl + '/' + this.timerange
              })
            },
            rebuildChartDatasets: function(responseData) {
              var self = this;
              var periodLabel = this.periodLabels[this.timerange];
              self.chartData = [];

              $.each(this._clegends, function(key, legend) {
                var responseItem = responseData[self.slugify(legend)];
                var dots = [];
                for (var i = 0; i <= Object.keys(periodLabel).length - 1; i++) {
                  if (typeof responseItem[i] !== 'undefined') {
                    dots[i] = responseItem[i];
                  } else {
                    dots[i] = 0;
                  }
                }
                self.chartData[key] = {
                  label: legend,
                  fill: self.defaultColors[key].fill,
                  backgroundColor: self.defaultColors[key].backgroundColor,
                  borderColor: self.defaultColors[key].borderColor,
                  borderWidth: self.defaultColors[key].borderWidth,
                  data: dots
                }
              });
            },
            rebuildChartLabels: function() {
              var self = this
              self.chartLabels = [];
              $.each(this.periodLabels[this.timerange], function(key, item) {
                self.chartLabels.push(item);
              });
            },
            refreshChart: function() {
              var self = this
              this.loadChart().done(function(response) {
                self.rebuildChartDatasets(response)
                self.rebuildChartLabels()
                if (self.chartInstance !== null) {
                  self.chartInstance.destroy();
                }
                self.chartInstance = new Chart(self.chartArea, {
                  type: 'bar',
                  data: {
                    labels: self.chartLabels,
                    datasets: self.chartData,
                  },
                  options: self.barChartOptions
                })
              }).fail(function() {
                console.error('Failed to load chart')
              });
            },
            slugify: function(text) {
              return text.toString().toLowerCase().replace(/\s+/g, '-') // Replace spaces with -
                .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, ''); // Trim - from end of text
            }
          },
          watch: {
            timerange: function() {
              this.refreshChart()
              // console.log(this.chartData)
            }
          }
    }
</script>

