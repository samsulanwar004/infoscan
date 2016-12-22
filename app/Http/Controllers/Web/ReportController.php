<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Report;
    use League\Flysystem\Exception;
    use \Crypt;
    use PDF;

    class ReportController extends AdminController {

        public function index() {
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
            $dataAttributesCount = count($dataAttributes)-1;                           
            $dataFormat = [
                            "0" => "PDF",
                            "1" => "Word",
                            "2" => "Excel",
                            "3" => "Image"
                          ];    
            $dataFormatCount = count($dataFormat)-1;                           
            return view('report.index', compact('dataAttributes', 'dataAttributesCount'));
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
                $dataAttributes = explode(',', $getAttributes);
                //dd(array($dataAttributes));
            } else {
                $dataAttributes = $dataAttributesAs;    
            }
            $dataAttributesCountAs = count($dataAttributesAs)-1;                           
            $dataAttributesCount = count($dataAttributes)-1;                           
            return view('report.filters', compact('dataAttributesAs', 'dataAttributesCountAs', 'dataAttributes', 'dataAttributesCount'));
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
            /*if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                //$getAttributes = Crypt::encrypt($getAttributes);  
                //$getAttributes = Crypt::decrypt($getAttributes);  
                $dataAttributes = $getAttributes;
            } else {*/
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
            //}
            $dataAttributesCount = count($dataAttributes)-1;                           
            $view = \View::make('report.pdf', compact('dataAttributes', 'dataAttributesCount'));
            $html = $view->render();        
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('snapReportTable.pdf');        
        }

    }