<template>
    <div class="box">
        <div class="box-header with-border">
          <div class="col-md-6">
            <h3 class="box-title"> Top 10s</h3>
          </div>
          <div class="col-md-3">
            <label for="">&nbsp;</label>
            <select id="category" name="category" class="form-control">
                <option value="claims-reason">Claims(reason)</option>
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
            <tbody>
                <tr v-for="row in tableRows">
                    <td></td>
                    <td>{{ row.label }}</td>
                    <td>{{ row.total }}</td>
                </tr>

            </tbody>
          </table>
        </div>
    </div>
</template>

<script>
 export default {
        props: [
            'resourceUrl'
        ],
        data: function() {
            return {
                period: 'daily',
                category: 'claims-reason',
                responseData: [],
                tableRows: []
            }
        },
        created: function () {
            this.refreshTable();
        },
        mounted: function () {

        },
        methods: {
            loadResource: function () {
                var url = this.resourceUrl + '/' + this.period;
                return $.ajax({
                    url: url
                });
            },
            refreshTable: function () {
                this.loadResource()
                    .done(function(response) {
                        this.responseData = response;
                        this.tableRows = this.responseData[this.category];
                    })
                    .fail(function () {
                        console.error('Failed to load report table data');
                    })
            }
        },
        watch: {
            period: function() {
                console.log('Period changed')
                this.refreshTable();
            },
            category: function () {
                console.log('Category changed')
                // this.refreshTable();
            }
        }
    }
</script>
