<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DispatchPlanController;
use TCPDF;
use App\Models\DispatchPlan;
use App\Models\SalesOrderDetailsDetails;
use App\Models\DispatchPlanDetails;
use App\Models\DispatchPlanDetailsDetails;
use App\Models\LoadingEntryDetails;
use App\Models\SOShortClose;
use App\Models\SOMappingDetails;
use App\Models\LocationStock;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Date;

class PrintFarmerDispatchPlanController extends Controller
{
    public function printFarmerDispatchPlan(Request $request, $id){ 

        $dp_data = DispatchPlan::where('dp_id', base64_decode($id))->first();

        $dispatch_plan_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',
        'dispatch_plan_details.dp_id','dealers.dealer_name','dealers.mobile_no','villages.village_name as customer_village',DB::raw('(CASE WHEN sales_order.so_from_value_fix = "location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        ])
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('villages', 'villages.id', '=', 'sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
        ->leftJoin('dealers', 'dealers.id', 'sales_order.dealer_id')
        ->where('items.print_dispatch_plan','Yes')
        ->where('dp_id', base64_decode($id)) 
        ->groupBy(['name','customer_village','dealer_name'])
        ->get();   

        if($dispatch_plan_details->isEmpty()){
            // session()->flash('message','No Data Available For Print!');
            // return back();
            // return Redirect::back();
            return redirect()->back()->with('error', 'No Data Available For Print.'); 

        }else{

            $pdfName = 'Farmer_Dispatch_'.str_replace("/", "_", $dp_data->dp_number);

            $pdfName = trim($pdfName, ".");
            // $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // $pdf = new TCPDF('P', PDF_UNIT, array(250, 110), true, 'UTF-8', false);
            $pdf = new TCPDF('P', PDF_UNIT, array(200, 110), true, 'UTF-8', false);
            
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
              // set document information
            $pdf->setCreator(PDF_CREATOR);
            $pdf->setAuthor('Nicola Asuni');
            $pdf->setTitle($pdfName);
            $pdf->setSubject('TCPDF Tutorial');
            $pdf->setKeywords('TCPDF, PDF, example, test, guide');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->setFooterMargin(PDF_MARGIN_FOOTER);    
			
			$pdf->setMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT, false);
    
            // set auto page breaks
            $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
                require_once(dirname(__FILE__) . '/lang/eng.php');
                $pdf->setLanguageArray($l);
            }
    
            // ---------------------------------------------------------
    
            // set font
            $pdf->setFont('helvetica', '', 9);
    
            // add a page
            // $pdf->AddPage();
            $counter = 0;
          
    
            // $pdf->AddPage('P', 'A4');

            // LandScape Page
            // $pdf->AddPage('L', 'A5 PORTRAIT');
    
            foreach($dispatch_plan_details as $key => $val){
                $content = '';
                $counter++;
               
                $data = DispatchPlanDetails::select('items.item_name','item_details.secondary_item_name','items.print_dispatch_plan','items.second_unit','items.qty',
                // 'units.unit_name','second_unit.unit_name as second_unit_name', 
                  DB::raw("CASE  WHEN dispatch_plan_secondary_details.plan_qty IS NOT NULL THEN dispatch_plan_secondary_details.plan_qty
        ELSE dispatch_plan_details.plan_qty  END as plan_qty"),
        DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
        ELSE units.unit_name END as unit_name"),
            DB::raw("CASE  WHEN item_details.secondary_item_name IS NOT NULL THEN 
        item_details.secondary_item_name
        ELSE items.item_name END as item_name"),
                // DB::raw("SUM(dispatch_plan_details.plan_qty) as plan_qty"),
                )
                ->leftJoin('dispatch_plan_secondary_details', 'dispatch_plan_secondary_details.dp_details_id', 'dispatch_plan_details.dp_details_id')
                ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
                ->leftJoin('items', 'items.id', '=', 'dispatch_plan_details.item_id')
                   ->leftJoin('item_details','item_details.item_details_id','=','dispatch_plan_secondary_details.item_details_id')
                ->leftJoin('units', 'units.id', '=', 'items.unit_id')
                ->leftJoin('units as second_unit', 'second_unit.id', '=', 'items.second_unit')
                ->leftJoin('dealers', 'dealers.id', '=', 'sales_order.dealer_id')
                ->leftJoin('locations', 'locations.id', '=', 'sales_order.to_location_id')
                ->leftJoin('villages', 'villages.id', '=', 'sales_order.customer_village')
                ->where([
                    ['dispatch_plan_details.dp_id', $val['dp_id']],
                    ['villages.village_name', $val['customer_village']],
                    ['dealers.dealer_name', $val['dealer_name']],
                    [DB::raw("BINARY CASE WHEN sales_order.so_from_value_fix = 'location' THEN 
                locations.location_name ELSE sales_order.customer_name END"), '=', $val['name']],
                ])
                ->where('items.print_dispatch_plan','Yes')
                ->groupBY('items.item_name','item_details.secondary_item_name',)
                ->get();

    
                $table_data = '';

                foreach ($data as $key => $value) {
           
                    $table_data .= '<tr>
                    <td colspan="3">'.$value['item_name'].'  &nbsp;&nbsp;'.number_format((float)$value['plan_qty'], 3, '.','').' - '.$value['unit_name'].' </td>                       
                    </tr>';
                

                    if($val['dealer_name'] !='' && $val['mobile_no'] != ''){
                        $dealer = '<u>'.$val['dealer_name'].'</u> <br>'.$val['mobile_no'];
                    }else{
                        $dealer = '<u>'.$val['dealer_name'].'</u>';
                    }
                             $content = '<table cellpadding="2" >
                              <tr>
                                    <td>
                                            <table cellpadding="2" border="0">
                                                <tr>
                                                    <td width="45%"><b>Name: &nbsp;&nbsp;</b><u>'.$val['name'].' </u></td>
                                                    <td width="25%"><b>Village: </b> <u>'.$val['customer_village'].' </u> </td>
                                                    <td style="background-color:#D3D3D3;" width="30%">'.$dealer.'</td>
                                                </tr>
                                                '.$table_data.'
                                                <tr>
                                                    <td colspan="3"><b><u>Bag - </u></b></td>
                                                </tr>
                                            </table>
                                    </td>                                                                
                                </tr>
                            </table>';
                }       

                // generate $content as before

                $pdf->AddPage();
                $pdf->Line(5, 5, 5,195);  
                $pdf->Line(5, 5, 105,5);  
                $pdf->Line(5, 15.3, 105,15.3);  
                $pdf->SetFillColor(169, 169, 169);  // Set the grey background color (RGB format)
                $pdf->Rect(5, 5, 100, 10, 'F');  
 
                $pdf->Line(105, 195, 5,195);  
                $pdf->Line(105, 5, 105,195);  

                // Get page dimensions
                $pageWidth  = $pdf->getPageWidth();
                $pageHeight = $pdf->getPageHeight();

                // Measure content height for natural size
                $contentHeight = $pdf->getStringHeight($pageWidth, $content);
                $contentWidth  = $pdf->getStringWidth(strip_tags($content)); // approximate width

                // Start transform
                $pdf->StartTransform();

                // Move origin to page center
                $cx = 0;
                // $cy = $pageHeight / 2;
                $cy =165;
                $pdf->Translate($cx, $cy);

                // Rotate 90 degrees clockwise around center
                $pdf->Rotate(90);


                $html = '<span style="font-size:25px;">'.$counter.'</span>';

                $pdf->writeHTMLCell(
                    180,   // width after rotation (use height as width)
                    0,    // height after rotation
                    166,                // X offset inside cell
                    5,                // Y offset inside cell
                    $html,
                    0,                // border
                    1,                // line break
                    0,                // fill
                    true,             // reset height
                    '',               // align
                    true              // autopadding
                );

                $pdf->writeHTMLCell(
                    180,   // width after rotation (use height as width)
                    0,    // height after rotation
                    -15,                // X offset inside cell
                    0,                // Y offset inside cell
                    $content,
                    0,                // border
                    1,                // line break
                    0,                // fill
                    true,             // reset height
                    '',               // align
                    true              // autopadding
                );

                $pdf->StopTransform();

            }

        }

       
      
        // ---------------------------------------------------------
        //Close and output PDF document
        // $pdf->render($pdfName . '.pdf', 'I');
        $pdf->Output(urlencode($pdfName) . '.pdf', 'I');
        //============================================================+
        // END OF FILE
        //============================================================+
    }
}