<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Report;
    use League\Flysystem\Exception;
    use \Crypt;
    use PDF;

    class ReportController extends AdminController {

        public function index() {
            dd('here');

            if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                $getAttributes = Crypt::decrypt($getAttributes);  
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = [
                                    "0" => "Outlet Name",
                                    "1" => "Outlet Area",
                                    "2" => "Products",
                                    "3" => "User's City",
                                    "4" => "Province",
                                    "5" => "Age",
                                    "6" => "Gender",
                                    "7" => "Usership",
                                    "8" => "SEC",
                                    "9" => "Outlet Type"
                                  ];    
            }
            $dataFormat = [
                            "0" => "PDF",
                            "1" => "Word",
                            "2" => "Excel",
                            "3" => "Image"
                          ];    
            return view('report.index', compact('dataAttributes'));
        }

        public function filters() {
            $dataAttributesAs = [
                                    "0" => "Outlet Name",
                                    "1" => "Outlet Area",
                                    "2" => "Products",
                                    "3" => "User's City",
                                    "4" => "Province",
                                    "5" => "Age",
                                    "6" => "Gender",
                                    "7" => "Usership",
                                    "8" => "SEC",
                                    "9" => "Outlet Type"
                                ];    
            if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                $dataAttributes2 = explode(',', $getAttributes);
            } else {
                $dataAttributes2 = $dataAttributesAs;    
            }
            $dataAttributes = array_merge($dataAttributes2, $dataAttributesAs);
            $dataAttributes = array_filter($dataAttributes, 'strlen');
            dd($dataAttributes);
            $dataAttributes = array_count_values($dataAttributes);
            return view('report.filters', compact('dataAttributes'));
        }

        public function filterStore(Request $request) {
            $input = [
                        'attributes' => $request->input(trim('attributes'))
                     ];
            $attributes = $input['attributes'];
            $attributes = Crypt::encrypt($attributes);      

            return redirect()->route('report.index', ['attributes' => $attributes]);
        }

        public function formatPdf() {                           
            if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                $getAttributes = explode(',', $getAttributes);
                $getAttributes = array_filter($getAttributes, 'strlen');
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = [
                                    "0" => "Outlet Name",
                                    "1" => "Outlet Area",
                                    "2" => "Products",
                                    "3" => "User's City",
                                    "4" => "Province",
                                    "5" => "Age",
                                    "6" => "Gender",
                                    "7" => "Usership",
                                    "8" => "SEC",
                                    "9" => "Outlet Type"
                                  ];    
            }
            $view = \View::make('report.pdf', compact('dataAttributes'));
            $html = $view->render();        
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('snapReportTable.pdf');        
        }

        public function formatExcel() {                           
            if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                $getAttributes = explode(',', $getAttributes);
                $getAttributes = array_filter($getAttributes, 'strlen');
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = [
                                    "0" => "Outlet Name",
                                    "1" => "Outlet Area",
                                    "2" => "Products",
                                    "3" => "User's City",
                                    "4" => "Province",
                                    "5" => "Age",
                                    "6" => "Gender",
                                    "7" => "Usership",
                                    "8" => "SEC",
                                    "9" => "Outlet Type"
                                  ];    
            }
            Excel::create('snapReportTable', function($excel) use($dataAttributes) {
                $excel->sheet('Sheet 1', function($dataAttributes) use($dataAttributes) {
                    $sheet->fromArray($dataAttributes);
                });
            })->export('xls');        
        }

    }