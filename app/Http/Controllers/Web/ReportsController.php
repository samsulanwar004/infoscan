<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Report;
    use League\Flysystem\Exception;
    use \Crypt;
    use PDF;
    use Excel;
    use Anam\PhantomMagick\Converter;
    use Anam\PhantomMagick\Adapter;
    use Anam\PhantomMagick\Exception\FileFormatNotSupportedException;

    class ReportsController extends AdminController {

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
            $dataFormat = [
                            "0" => "PDF",
                            "1" => "Word",
                            "2" => "Excel",
                            "3" => "Image"
                          ];    
            return view('reports.index', compact('dataAttributes'));
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
            $dataAttributes = array_count_values($dataAttributes);
            return view('reports.filters', compact('dataAttributes'));
        }

        public function filterStore(Request $request) {
            $input = [
                        'attributes' => $request->input(trim('attributes'))
                     ];
            $attributes = $input['attributes'];
            $attributes = Crypt::encrypt($attributes);      

            return redirect()->route('reports.index', ['attributes' => $attributes]);
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
            $view = \View::make('reports.pdf', compact('dataAttributes'));
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
            Excel::create('SnapReportTable', function($excel) use ($dataAttributes) {
                $excel->sheet('Snap Report Table', function($sheet) use ($dataAttributes) {
                    $sheet->fromArray($dataAttributes);
                });
            })->export('xls');
        }

        public function formatWord() {                           
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
            $headers = array(
                                "Content-type"=>"text/html",
                                "Content-Disposition"=>"attachment;Filename=SnapReportTable.doc"
                            );

            $content  = '<html>
                            <head>
                                <meta charset="utf-8">
                            </head>
                            <body>
                                <section class="content">
                                    <div class="box">
                                        <div class="box-body" id="form-body">
                                            <div class="box-body" id="form-body">
                                                <table class="table table-striped" border="1">
                                                    <thead>
                                                        <tr>
                        ';
            foreach ($dataAttributes as $data) {
                $content .= '<th align="center">'.$data.'</th>';
            }
            $content .= '               
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                        ';
            foreach ($dataAttributes as $data) {
                $content .= '<th>&nbsp;</th>';
            }
            $content .= '
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </body>
                        </html>';
            return \Response::make($content,200, $headers);        
        }

        public function formatImage() {                           
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
        }
    
    }