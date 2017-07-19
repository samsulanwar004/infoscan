<template>
    <div class="box">
        <div class="box-header with-border">
          <div class="col-md-6">
            <h3 class="box-title"> Top 10s</h3>
            <p>of <strong>{{ timeRangeInfoText }}</strong></p>
          </div>
          <div class="col-md-3">
            <label for="">&nbsp;</label>
            <select id="category"v-model="category" name="category" class="form-control">
                <!-- <option value="claims-reason">Claims(reason)</option> -->
                <option value="user-approved-claims">User who have more claims</option>
                <option value="rejections">Rejections</option>
                <option value="user-rejected-claims">Users who have more rejections</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="">Period</label>
            <select id="period" name="period" v-model="period" class="form-control">
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
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered">
            <thead>
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Detail</th>
                  <th>Count</th>
                </tr>
            </thead>
            <template v-if="tableRows.length">
                <tbody>

                    <tr v-for="(row, key, index) in tableRows">
                        <td>{{ key + 1   }}</td>
                        <td>{{ row.label }}</td>
                        <td>{{ row.total }}</td>
                    </tr>
                </tbody>
            </template>
            <template v-else>
                <tbody>
                    <tr>
                        <td colspan="3">
                            Empty Result
                        </td>
                    </tr>
                </tbody>
            </template>
        </table>
        <!-- loader -->
        <div class="overlay loader">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        <!-- /.loader -->

        </div>

    </div>
</template>

<script>
 import moment from 'moment'
 export default {
        props: [
            'resourceUrl'
        ],
        data: function() {
            return {
                period: 'daily',
                category: 'user-approved-claims',
                responseData: [],
                tableRows: [],
                timeRangeInfo: {
                    daily: moment().startOf('week').add(1, 'days').format('DD-MM-YYYY') + ' - ' + moment().endOf('week').add(1, 'days').format('DD-MM-YYYY'), // ex: 20/07/2017 - 27/07/2017
                    weekly: moment().format('MMMM YYYY'), // ex: July 2017
                    monthly: moment().format('YYYY'), // ex: 2017
                    yearly: 'All periods',
                },
                loaderAnimation: '',
            }
        },
        created: function () {
        },
        mounted: function () {
            this.loaderAnimation = $(this.$el).find('.loader')
            this.refreshTable();
        },
        computed: {
            timeRangeInfoText: function () {
                return this.timeRangeInfo[this.period];
            }
        },
        methods: {
            loadResource: function () {
                var url = this.resourceUrl + '/' + this.period;
                return $.ajax({
                    url: url
                });
            },
            refreshTable: function () {
                var self = this;

                this.loaderAnimation.show()

                this.loadResource()
                    .done(function(response) {
                        self.responseData = response;

                        self.refreshTableData();

                        self.loaderAnimation.hide()
                    })
                    .fail(function () {
                        console.error('Failed to load report table data');
                    })
            },
            refreshTableData: function () {
                this.tableRows = this.responseData[this.category];
            }
        },
        watch: {
            period: function() {
                console.log('Period changed')
                this.refreshTable();
            },
            category: function () {
                console.log('Category changed')
                this.refreshTableData();
            }
        }
    }
</script>
