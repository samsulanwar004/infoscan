<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Reports;
    use League\Flysystem\Exception;
    use PDF;
    use Excel;
    use Session;

    class ReportsController extends AdminController {

        public function index() {
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id')->paginate(25);
            return view('reports.index', compact('reports'));
        }

        public function store(Request $request) {
            //$input = $request->all();
            //dd($input);
            $input = ['attributes' => $request->input('attributes')];
            $attributes = $input['attributes'];
            dd($attributes);
            $value = $request->session()->put('attributes', $attributes);            
            $attributes = $request->session()->get('attributes', $value);

            return redirect()->route('reports.index');
        }

        public function formatPdf() {                           
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id')->paginate(25);
            $view = \View::make('reports.pdf', compact('reports'));
            $html = $view->render();        
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('snapReportTable.pdf');        
        }

        public function formatExcel() {                           
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id');
            //dd($reports);
            Excel::create('SnapReportTable', function($excel) use ($reports) {
                $excel->sheet('Snap Report Table', function($sheet) use ($reports) {
                    $sheet->fromArray($reports);
                });
            })->export('xls');
        }

        public function formatWord() {                           
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id')->paginate(25);
            $headers = array(
                                "Content-type"=>"text/html",
                                "Content-Disposition"=>"attachment;Filename=SnapReportTable.doc"
                            );
            $content  = '<html>
                            <head>
                                <meta charset="utf-8">
                            </head>
                            <body>
                                <table class="table table-striped" border="1">
                                    <thead>
                                        <tr align="center">
                                            <th class="outlet_name" name="outlet_name">Outlet Name</th>
                                            <th class="products" name="products">Products</th>
                                            <th class="users_city" name="users_city">Users City</th>
                                            <th class="age" name="age">Age</th>
                                            <th class="outlet_area" name="outlet_area">Outlet Area</th>
                                            <th class="province" name="province">Province</th>
                                            <th class="gender" name="gender">Gender</th>
                                            <th class="usership" name="usership">Usership</th>
                                            <th class="sec" name="sec">SEC</th>
                                            <th class="outlet_type" name="outlet_type">Outlet Type</th>
                                        </tr>
                                    </thead>
                                <tbody>';
            foreach($reports as $item) {
                                            $content .= '<tr align="center">
                                                            <td>'.$item->outlet_name.'</td>
                                                            <td>'.$item->products.'</td>
                                                            <td>'.$item->users_city.'</td>
                                                            <td>'.$item->age.'</td>
                                                            <td>'.$item->outlet_area.'</td>
                                                            <td>'.$item->province.'</td>
                                                            <td>'.$item->gender.'</td>
                                                            <td>'.$item->usership.'</td>
                                                            <td>'.$item->sec.'</td>
                                                            <td>'.$item->outlet_type.'</td>
                                                         </tr>';
            }
            $content .= '           </tbody>
                                </table>
                            </body>
                         </html>';
            return \Response::make($content,200, $headers);        
        }
    
    }