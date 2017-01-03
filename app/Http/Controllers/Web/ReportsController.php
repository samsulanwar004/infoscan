<?php
    namespace App\Http\Controllers\Web;

    use Illuminate\Http\Request;
    use App\Reports;
    use League\Flysystem\Exception;
    use PDF;

    class ReportsController extends AdminController {

        const PER_PAGE_PAGINATION = 10;
        
        public function index() {
            $this->isAllowed('Reports.List');
            $reports = Reports::orderBy('id')->paginate(1000);
            return view('reports.index', compact('reports'));
        }

        public function formatPdf(Request $request) { 
            $this->isAllowed('Reports.List');
            $attributes = $request->all();
            $attributes = $attributes['attributes'];
            $attributesKeys = json_decode($attributes);
            $attributesCounts = count($attributesKeys);
            $attributesValues = Reports::orderBy('id')->paginate(1000);
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
            $attributesValues = Reports::orderBy('id')->paginate(1000);
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
            $attributesValues = Reports::orderBy('id')->paginate(1000);
            $headers = array(
                                "Content-type"=>"text/html",
                                "Content-Disposition"=>"attachment;Filename=SnapReportTable.doc"
                            );
            $content = $this->setContentFile($attributesKeys, $attributesCounts, $attributesValues);
            return \Response::make($content,200, $headers);        
        }
    
    }