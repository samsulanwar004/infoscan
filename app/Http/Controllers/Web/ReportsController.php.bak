<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Reports;
    use League\Flysystem\Exception;
    use PDF;

    class ReportsController extends AdminController {

        const PER_PAGE_PAGINATION = 25;
        
        public function index() {
            $dataReports1 = Reports::
                            select('outlet_name')->
                            groupBy('outlet_name')->
                            get();
            $dataReports2 = Reports::
                            select('products')->
                            groupBy('products')->
                            get();
            $dataReports3 = Reports::
                            select('users_city')->
                            groupBy('users_city')->
                            get();
            $dataReports4 = Reports::
                            select('age')->
                            groupBy('age')->
                            get();
            $dataReports5 = Reports::
                            select('outlet_area')->
                            groupBy('outlet_area')->
                            get();
            $dataReports6 = Reports::
                            select('province')->
                            groupBy('province')->
                            get();
            $dataReports7 = Reports::
                            select('gender')->
                            groupBy('gender')->
                            get();
            $dataReports8 = Reports::
                            select('usership')->
                            groupBy('usership')->
                            get();
            $dataReports9 = Reports::
                            select('sec')->
                            groupBy('sec')->
                            get();
            $dataReports10 = Reports::
                             select('outlet_type')->
                             groupBy('outlet_type')->
                             get();
            if(isset($_GET['startDate']) && isset($_GET['endDate'])) {
                $startDate = date("Y-m-d", strtotime($_GET['startDate']));
                $endDate = date("Y-m-d", strtotime($_GET['endDate']));
                $reports = Reports::
                           where('snap_at', '>=', $startDate)->
                           where('snap_at', '<=', $endDate)->
                           orderBy('id')->
                           paginate(25);
            } else {
                $startDate = date("Y-m-d");                
                $endDate = date("Y-m-d");                
                $reports = Reports::
                           orderBy('id')->
                           paginate(25);
            }            
            $this->isAllowed('Reports.List');
            $startDate = date("d-m-Y", strtotime($startDate));
            $endDate = date("d-m-Y", strtotime($endDate));
            return view('reports.index', compact('reports', 'startDate', 'endDate', 'dataReports1', 'dataReports2', 'dataReports3', 'dataReports4', 'dataReports5', 'dataReports6', 'dataReports7', 'dataReports8', 'dataReports9', 'dataReports10'));
        }

        public function store(Request $request) {
            $snapDate = $request->all();
            $startDate = $snapDate['startDate'];
            $endDate = $snapDate['endDate'];
            return redirect()->action('Web\ReportsController@index', compact('startDate', 'endDate'));
        }

        public function formatPdf(Request $request) { 
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $this->isAllowed('Reports.List');
            $attributes = $request->all();
            $attributes = $attributes['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            if(isset($_GET['startDate']) && isset($_GET['endDate'])) {
                $startDate = date("Y-m-d", strtotime($_GET['startDate']));
                $endDate = date("Y-m-d", strtotime($_GET['endDate']));
                $attributesValues = Reports::
                                    where('snap_at', '>=', $startDate)->
                                    where('snap_at', '<=', $endDate)->
                                    orderBy('id')->
                                    paginate(25);
            } else {
                $startDate = date("Y-m-d");                
                $endDate = date("Y-m-d");                
                $attributesValues = Reports::
                                    orderBy('id')->
                                    paginate(25);
            }
            $view = \View::make('reports.pdf', compact('attributesKeys', 'attributesCounts', 'attributesValues'));
            $html = $view->render();
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('SnapReportTable.pdf');        
        }

        public function setContentFile($keys, $counts, $values) {
            $content  = '<html>
                         <head>
                         <meta charset="utf-8">
                         </head>
                         <body>
                         <table class="table table-striped" border="1">
                         <thead>';
            for($i=0; $i<$counts; $i++) {
                $content .= '<th class="'.$keys[$i]->name.'" name="'.$keys[$i]->name.'">'.$keys[$i]->value.'</th>';
            }
            $content .= '</thead>
                         <tbody>';
            foreach($values as $item) {
                $content .= '<tr align="center">';
                for($i=0; $i<$counts; $i++) {
                    $name = $keys[$i]->name;
                    $content .= '<td>'.$item->$name.'</td>';
                }
                $content .= '</tr>';
            }
            $content .= '</tbody>
                         </table>
                         </body>
                         </html>';
            return $content;
        }

        public function formatExcel(Request $request) {
            $this->isAllowed('Reports.List');
            $attributes = $request->all();
            $attributes = $attributes['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            if(isset($_GET['startDate']) && isset($_GET['endDate'])) {
                $startDate = date("Y-m-d", strtotime($_GET['startDate']));
                $endDate = date("Y-m-d", strtotime($_GET['endDate']));
                $attributesValues = Reports::
                                    where('snap_at', '>=', $startDate)->
                                    where('snap_at', '<=', $endDate)->
                                    orderBy('id')->
                                    paginate(25);
            } else {
                $startDate = date("Y-m-d");                
                $endDate = date("Y-m-d");                
                $attributesValues = Reports::
                                    orderBy('id')->
                                    paginate(25);
            }
            $headers = array(
                                "Content-Type"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                "Content-Disposition"=>"attachment;filename=SnapReportTable.xlsx",
                                "Cache-Control"=>"max-age=0"
                            );
            $content = $this->setContentFile($attributesKeys, $attributesCounts, $attributesValues);
            return \Response::make(rtrim($content, "\n"), 200, $headers);        
        }        

        public function formatWord(Request $request) {                           
            $this->isAllowed('Reports.List');
            $attributes = $request->all();
            $attributes = $attributes['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            if(isset($_GET['startDate']) && isset($_GET['endDate'])) {
                $startDate = date("Y-m-d", strtotime($_GET['startDate']));
                $endDate = date("Y-m-d", strtotime($_GET['endDate']));
                $attributesValues = Reports::
                                    where('snap_at', '>=', $startDate)->
                                    where('snap_at', '<=', $endDate)->
                                    orderBy('id')->
                                    paginate(25);
            } else {
                $startDate = date("Y-m-d");                
                $endDate = date("Y-m-d");                
                $attributesValues = Reports::
                                    orderBy('id')->
                                    paginate(25);
            }
            $headers = array(
                                "Content-type"=>"text/html",
                                "Content-Disposition"=>"attachment;Filename=SnapReportTable.doc"
                            );
            $content = $this->setContentFile($attributesKeys, $attributesCounts, $attributesValues);
            return \Response::make($content, 200, $headers);        
        }

        public function maps() {
            $this->isAllowed('Reports.List');
            $reports = Reports::
                       select('users_city', 'longitude', 'latitude')->
                       distinct('users_city')->
                       orderBy('users_city', 'ASC')->
                       get();
            return view('reports.maps', compact('reports'));
        }

    }