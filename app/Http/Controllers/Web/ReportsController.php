<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Report;
    use League\Flysystem\Exception;
    use \Crypt;
    use PDF;
    use Excel;
    use Session;

    class ReportsController extends AdminController {

        public function index() {
            dd($this->dataValueAttributes());
            $getAttributes = Session::get('attributes');
            if(isset($getAttributes)) {
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = $this->dataAttributes();
            }
            return view('reports.index', compact('dataAttributes'));
        }

        public function dataAttributes() {
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
            return $dataAttributes;
        }

        public function dataValueAttributes() {
            $dataValueAttributes = [
                                        "0" => [
                                                "0" => "Indomaret",
                                                "1" => "",
                                                "2" => "Chitos",
                                                "3" => "Bekasi",
                                                "4" => "Jawa Barat",
                                                "5" => "18",
                                                "6" => "Male",
                                                "7" => "",
                                                "8" => "",
                                                "9" => "Convenience"
                                               ],     
                                        "1" => 
                                               [
                                                "0" => "Alfamart",
                                                "1" => "",
                                                "2" => "Bimoli",
                                                "3" => "Jakarta Selatan",
                                                "4" => "DKI Jakarta",
                                                "5" => "18",
                                                "6" => "Male",
                                                "7" => "",
                                                "8" => "",
                                                "9" => ""
                                               ]                                                    
                                   ];    
            return $dataValueAttributes;
        }

        public function filters() {
            $dataAttributesAs = $this->dataAttributes();
            $getAttributes = Session::get('attributes');
            if(isset($getAttributes)) {
                $dataAttributes2 = $getAttributes;
            } else {
                $dataAttributes2 = $dataAttributesAs;    
            }
            $dataAttributes = array_merge($dataAttributes2, $dataAttributesAs);
            $dataAttributes = array_filter($dataAttributes, 'strlen');
            $dataAttributes = array_count_values($dataAttributes);
            return view('reports.filters', compact('dataAttributes'));
        }

        public function filterStore(Request $request) {
            $input = ['attributes' => $request->input(trim('attributes'))];
            $attributes = $input['attributes'];
            $value = $request->session()->put('attributes', $attributes);            
            $attributes = $request->session()->get('attributes', $value);

            return redirect()->route('reports.index');
        }

        public function formatPdf() {                           
            $getAttributes = Session::get('attributes');
            if(isset($getAttributes)) {
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = $this->dataAttributes();    
            }
            $view = \View::make('reports.pdf', compact('dataAttributes'));
            $html = $view->render();        
            PDF::SetTitle('Snap Report Table');
            PDF::AddPage();
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('snapReportTable.pdf');        
        }

        public function formatExcel() {                           
            $getAttributes = Session::get('attributes');
            if(isset($getAttributes)) {
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = $this->dataAttributes();    
            }
            Excel::create('SnapReportTable', function($excel) use ($dataAttributes) {
                $excel->sheet('Snap Report Table', function($sheet) use ($dataAttributes) {
                    $sheet->fromArray($dataAttributes);
                });
            })->export('xls');
        }

        public function formatWord() {                           
            $getAttributes = Session::get('attributes');
            if(isset($getAttributes)) {
                $dataAttributes = $getAttributes;
            } else {
                $dataAttributes = $this->dataAttributes();    
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
                                <table border="1">
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
                            </body>
                        </html>';
            return \Response::make($content,200, $headers);        
        }
    
    }