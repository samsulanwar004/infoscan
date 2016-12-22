<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Report;
    use League\Flysystem\Exception;

    class ReportController extends AdminController {
        public function index() {
            if(isset($_GET['attributes'])) {
                $getAttributes = $_GET['attributes'];
                $dataAttributes = $getAttributes;
                //$dataAttributes = array_unique($dataAttributes); 
                //$dataAttributes = array_values(array_filter($dataAttributes));            
                $dataAttributesCount = count($dataAttributes)-1;                           
            } else {
                $dataAttributes = [
                                    "1"  => "Outlet Name",
                                    "2"  => "Outlet Area",
                                    "3"  => "Products",
                                    "4"  => "User's City",
                                    "5"  => "Province",
                                    "6"  => "Age",
                                    "7"  => "Gender",
                                    "8"  => "Usership",
                                    "9"  => "SEC",
                                    "10" => "Outlet Type"
                                  ];    
                $dataAttributesCount = count($dataAttributes)-1;                           
            }
            $dataType = [
                            "1"  => "Table",
                            "2"  => "Cluster Map"
                        ];    
            $dataTypeCount = count($dataType);                           
            $dataFormat = [
                            "1"  => "PDF",
                            "2"  => "Word",
                            "3"  => "Excel",
                            "4"  => "Image"
                          ];    
            $dataFormatCount = count($dataFormat);                           
            return view('report.index', compact('dataAttributes', 'dataAttributesCount', 'dataType', 'dataTypeCount','dataFormat', 'dataFormatCount'));
        }

        public function filters() {
            $dataAttributes = [
                                "1"  => "Outlet Name",
                                "2"  => "Outlet Area",
                                "3"  => "Products",
                                "4"  => "User's City",
                                "5"  => "Province",
                                "6"  => "Age",
                                "7"  => "Gender",
                                "8"  => "Usership",
                                "9"  => "SEC",
                                "10" => "Outlet Type"
                              ];    
            $dataAttributesCount = count($dataAttributes);                           
            return view('report.filters', compact('dataAttributes', 'dataAttributesCount'));
        }

        public function filterStore(Request $request) {
            $input = [
                        'attributes' => $request->input(trim('attributes'))
                     ];
            $attributes = $input['attributes'];
            //dd($attributes);
            //exit();

            /*$this->validate($request, [
                'code' => 'required|string|unique:code',
                'range_start' => 'required|numeric|different:range_end|min:0',
                'range_end' => 'required|numeric|min:0'
            ]);
            try {
                $input = $request->all();
                if ($input['range_start'] > $input['range_end']) {
                    return back()->with('errors', 'End range must be greater than start range.');
                }
                SocioEconomicStatus::create($input);
                return redirect()->action('Web\SesController@index')->with('success', 'SES succesfully saved.');
            } catch (Exception $e) {
                return back()->with('errors', $e->getMessage());
            }*/
            return redirect()->route('report.index', ['attributes' => $attributes]);
        }
    }