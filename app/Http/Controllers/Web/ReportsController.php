<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Reports;
    use League\Flysystem\Exception;
    use PDF;
    use Excel;
    use Session;
    use Crypt;

    class ReportsController extends AdminController {

        public function index() {
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id')->paginate(25);
            return view('reports.index', compact('reports'));
        }

        public function formatPdf() {
            $this->isAllowed('Reports.List');
            $attributes = $_GET['attributes'];
            //$attributes = Crypt::decrypt($attributes);
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            $attributesValues = Reports::orderBy('id')->paginate(25);
            $view = \View::make('reports.pdf', compact('attributesKeys', 'attributesCounts', 'attributesValues'));
            $html = $view->render();
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('snapReportTable.pdf');
        }

        public function formatWord() {
            $this->isAllowed('Reports.List');
            $attributes = $_GET['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            $attributesValues = Reports::orderBy('id')->paginate(25);
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
                                    <thead>';
            for($i=0; $i<$attributesCounts; $i++) {
                $content .= '<th class="'.$attributesKeys[$i]->name.'" name="'.$attributesKeys[$i]->name.'">'.$attributesKeys[$i]->value.'</th>';
            }
            $content .= '</thead>
                                <tbody>';
            foreach($attributesValues as $item) {
                $content .= '<tr align="center">';
                for($i=0; $i< $attributesCounts;$i++) {
                    $name = $attributesKeys[$i]->name;
                    $content .= '<td>'.$item->$name.'</td>';
                }
                $content .= '</tr>';
            }
            $content .= '           </tbody>
                                </table>
                            </body>
                         </html>';
            return \Response::make($content,200, $headers);
        }

        public function formatExcel() {
            $this->isAllowed('Reports.List');
            $attributes = $_GET['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            $attributesValues = Reports::orderBy('id')->paginate(25);
            $headers = array(
                                "Content-Type"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                "Content-Disposition"=>"attachment;filename=SnapReportTable.xlsx",
                                "Cache-Control"=>"max-age=0"
                            );
            $content  = '<html>
                            <head>
                                <meta charset="utf-8">
                            </head>
                            <body>
                                <table class="table table-striped" border="1">
                                    <thead>';
            for($i=0; $i<$attributesCounts; $i++) {
                $content .= '<th class="'.$attributesKeys[$i]->name.'" name="'.$attributesKeys[$i]->name.'">'.$attributesKeys[$i]->value.'</th>';
            }
            $content .= '</thead>
                                <tbody>';
            foreach($attributesValues as $item) {
                $content .= '<tr align="center">';
                for($i=0; $i< $attributesCounts;$i++) {
                    $name = $attributesKeys[$i]->name;
                    $content .= '<td>'.$item->$name.'</td>';
                }
                $content .= '</tr>';
            }
            $content .= '           </tbody>
                                </table>
                            </body>
                         </html>';
            return \Response::make(rtrim($content, "\n"), 200, $headers);
            /*$this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id');
            Excel::create('SnapReportTable', function($excel) use ($reports) {
                $excel->sheet('Snap Report Table', function($sheet) use ($reports) {
                    $sheet->fromArray($reports);
                });
            })->export('xls');*/
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

    }