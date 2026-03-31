<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\DispatchPlan;
use App\Models\Item;
use App\Http\Controllers\DispatchPlanController;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\DispatchPlanDetails;
use TCPDF;
use DB;
use Carbon\Carbon;
use Date;
use Illuminate\Support\Facades\Redirect;


class PrintDispatchPlanController extends Controller
{
    public function printDispatchPlan(Request $request, $id){ 

        $dp_data = DispatchPlan::select('dp_number','dp_date')->where('dp_id', base64_decode($id))->first();

        if($dp_data->dp_date != "" && $dp_data->dp_date != null)
        {
            $dp_data->dp_date = Date::createFromFormat('Y-m-d', $dp_data->dp_date)->format('d/m/Y');
        }

        $dealerName = DispatchPlan::select('dealers.dealer_name')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_id','=','dispatch_plan.dp_id')
        ->leftJoin('sales_order_details','sales_order_details.so_details_id','=','dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order','sales_order.id','=','sales_order_details.so_id')
        ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')
        ->where('dispatch_plan.dp_id',base64_decode($id))
        ->groupBy('dealers.id')
        ->pluck('dealer_name')
        ->implode(', ');

        $so_no = DispatchPlanDetails::select('sales_order.so_number')
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')      
        ->where('dp_id', base64_decode($id))     
        ->groupBy('sales_order.so_number') 
        ->pluck('so_number') 
        ->implode(', '); 

        
        $dp_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',           'dispatch_plan_details.dp_id','dispatch_plan_details.item_id',
        // 'units.unit_name as unitName',
        'items.second_unit as second_unit_id','items.qty','items.show_item_in_print',
        // 'second_unit.unit_name as second_unit',
        // DB::raw("SUM(dispatch_plan_details.plan_qty) as plan_qty"), 

        'items.item_name','item_details.secondary_item_name',

        DB::raw("CASE  WHEN second_unit.unit_name IS NOT NULL THEN second_unit.unit_name 
        ELSE units.unit_name END as unit_name"),

        DB::raw("CASE  WHEN dispatch_plan_secondary_details.plan_qty IS NOT NULL THEN dispatch_plan_secondary_details.plan_qty
        ELSE dispatch_plan_details.plan_qty  END as plan_qty"),


        DB::raw("CASE  WHEN item_details.secondary_item_name IS NOT NULL THEN 
        item_details.secondary_item_name
        ELSE items.item_name END as item_name"),
        
        'dealers.dealer_name','dealers.mobile_no','villages.village_name as customer_village',
        DB::raw('(CASE WHEN sales_order.so_from_value_fix = "location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),

        'items.require_raw_material_mapping','items.secondary_unit','items.wt_pc','item_details.secondary_wt_pc',
        ])
        ->leftJoin('dispatch_plan_secondary_details', 'dispatch_plan_secondary_details.dp_details_id', 'dispatch_plan_details.dp_details_id')
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('villages', 'villages.id', '=', 'sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
         ->leftJoin('item_details','item_details.item_details_id','=','dispatch_plan_secondary_details.item_details_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->leftJoin('units as second_unit', 'second_unit.id', '=', 'items.second_unit')
        ->leftJoin('dealers', 'dealers.id', 'sales_order.dealer_id')
        ->where('items.print_dispatch_plan','Yes')
        ->where('dp_id', base64_decode($id)) 
        ->groupBy(['name','customer_village','dealer_name', 'items.item_name','item_details.secondary_item_name'])
        ->get();   
      

        if($dp_details->isEmpty()){
          
          return back()->with('error', 'No Data Available For Print.'); 
        }else{

            $counter = 0;
              $total_wt_pc = 0;
            $content = '';

            $location  = getCurrentLocation();
       
            if($location->header_print != ""){
                $_SESSION['header_print'] = $location->header_print;
            }else{
                $_SESSION['header_print'] = '';
            }



            foreach ($dp_details as $key => $value) { 
                // dd($dp_details); 
                  $rawItemsHtml = '';      
                if ($value['show_item_in_print'] == 'Yes') {
                 
                    $rawMaterials = ItemRawMaterialMappingDetail::select('raw_items.item_name', 'item_raw_material_mapping_details.raw_material_qty')
                    ->leftJoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                    ->where('item_raw_material_mapping_details.item_id', $value['item_id'])
                    ->get();

                    if ($rawMaterials->count() > 0) {                   
                        foreach ($rawMaterials as $rawItem) {
                            $rawItemsHtml .= '<br> <b>•</b> ' . $rawItem->item_name . ' - <b>' . number_format((float)$rawItem->raw_material_qty , 3, '.','').'</b>';
                        }
                    }
                } 

            if($value->require_raw_material_mapping == 'Yes' && $value->secondary_unit == 'Yes'){              
                $wt_pc = $value->secondary_wt_pc;
            }else if($value->require_raw_material_mapping == 'Yes' && $value->secondary_unit == 'No'){               
              
                $mappin_sum = 0;
                $getItem = ItemRawMaterialMappingDetail::where('item_id','=',$value->item_id)->get();

                foreach($getItem as $fkey=>$fval){
                    $item_wt_pc = Item::where('id',$fval->raw_material_id)->sum('wt_pc');
                    $mappin_sum += $item_wt_pc;
                }
                
                $wt_pc =  $mappin_sum;
            }else{
                $wt_pc =  $value->wt_pc;
                
            }
        
                $wt_pc_qty = $wt_pc * (int)$value->plan_qty;
                $total_wt_pc += $wt_pc_qty;

                $counter++;

                $content .='<tr>
                           <td style="text-align:center; width:5%;">'.$counter.'</td>
                           <td style="text-align:left; width:65%;">'.$value->item_name.' '.$rawItemsHtml.'</td>
                           <td style="text-align:center; width:10%;">'.(int)$value->plan_qty.'</td>
                           <td style="text-align:center; width:10%;">'.$value->unit_name.'</td>
                           <td style="text-align:center; width:10%;">'.number_format((float)$wt_pc_qty , 3, '.','').'</td>
                           </tr>';
               
              
            }
        }   
    

    
       $total_wt_pc = number_format((float)$total_wt_pc , 3, '.','');

        $pdfName = 'Total_Dispatch_'.str_replace("/", "_", $dp_data->dp_number);
        
        $pdfName = trim($pdfName, ".");
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

          // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('Nicola Asuni');
        $pdf->setTitle($pdfName);
        $pdf->setSubject('TCPDF Tutorial');
        $pdf->setKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        // set margins

        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_RIGHT, false);

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
        $pdf->setFont('helvetica', '', 12);

        // add a page
        $pdf->AddPage();


        if ($dp_data->dp_number != "") {
            $dp_no = 'Disp. Plan No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $dp_data->dp_number . '</b><br>';
        }else{
            $dp_no = '';
        }

        if ($dp_data->dp_date != "") {
            $dp_date = 'Disp. Plan Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $dp_data->dp_date;
        }else{
            $dp_date = '';
        }

        if ($so_no != "") {
            $so_no = 'SO No.&nbsp;&nbsp;:&nbsp;' . $so_no ;
        }else{
            $so_no = '';
        }

        if ($dealerName != "") {
            $dealer_name = 'Dealer  &nbsp;:&nbsp;' . $dealerName .'<br>';
        }else{
            $dealer_name = '';
        }
//   <th colspan="4" style="">$dealer_name$so_no</th>
        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="9" style="text-align:center; font-weight:bold; padding:6px;"><strong>TOTAL DISPATCH</strong></th>
                </tr>
                 <tr>
                    <th colspan="4" style="">$so_no</th>
                    <th colspan="5" style="">$dp_no$dp_date</th>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>No.</b></th>
                    <th style="text-align:center; width:65%;"><b>Description</b></th>
                    <th style="text-align:center; width:10%;"><b>Disp. Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                    <th style="text-align:center; width:10%;"><b>Wt./Pc.</b></th>
                </tr>                 
            </thead>
            <tbody>
                $content
                <tr><td colspan="4" style="text-align:right;"><b>TOTAL</b></td>
                    <td style="text-align:center;"><b>$total_wt_pc</b></td>
                </tr>
            </tbody>                               
        </table>
        EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');

        $content = $pdf->customFooter();
        // dd($content);
        $js = <<<EOD
                        var footerlen=document.getElementById("footerTbl").length;
                        app.alert(footerlen);
                    EOD;
        //$pdf->includeJs($js);
        //$pdf->writeHTML($content, true, false, false, false, '');
        $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 0, false, true, "L", true);
        $lastH = $pdf->getLastH();
        //$pH=$pdf->getPageHeight();
        //$diff=$pH-$lastH;
        //echo $pH-$diff;
        $pdf = $pdf->rollbackTransaction();
        $pdf->commitTransaction();
        $pdf->pCounter = $pdf->getNumPages();
        $pdf->fHeight = $lastH;

        // ---------------------------------------------------------
        //Close and output PDF document
        // $pdf->render($pdfName . '.pdf', 'I');
        $pdf->Output(urlencode($pdfName) . '.pdf', 'I');
        //============================================================+
        // END OF FILE
        //============================================================+

    
    }
}

class MyPDF extends TCPDF
{
    //Page header
    public $pCounter;
    public $fHeight;

    public $currentPage; // To store the current page number


    public function Header()
    {
        $header = commonPdfHeader("PO");
        $this->writeHTMLCell(0, '', '', '', $header, 0, 0, false,true, "L", true);
    }


    // Page footer
    public function Footer()
    {
        // Set font
        $this->setFont('helvetica', '', 8);
        // Page number
        $n = "";

        $var1 = $this->getAliasNumPage();
        $var2 = $this->getAliasNbPages();


        $content2 = $this->customFooter();
        //$height=$this->getStringHeight(0,$content2);

        $content = "";
        if (isset($this->pCounter) && ($this->getPage() == $this->pCounter)) {
            $height = 5 + $this->fHeight;
            $this->setY(-$height);
            //$this->setAutoPageBreak(TRUE, 80);
            $content = $content2;
        } else {
            $this->setY(-10);
            $height = $this->getRemainingWidth();  // Added on 2nd Oct 2023
        }

        $tbl = <<<EOD
            $content
            <table width="100%" cellpadding="6" cellspacing="0" border="0">
                <tr><td style="text-align:center;">Page $var1 / $var2</td></tr>
            </table>
            EOD;

        $this->writeHTMLCell(0, '', '', '', $tbl, 0, 0, false, true, "L", true);

        /** Added on 2nd Oct 2023 ***/
        $this->Line(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_LEFT, $this->getPageHeight() - $height);  //left border
        $this->Line($this->getPageWidth() - PDF_MARGIN_RIGHT, PDF_MARGIN_TOP + 10, $this->getPageWidth() - PDF_MARGIN_RIGHT, $this->getPageHeight() - $height); //right border
        $this->Line(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, $this->getPageWidth() - PDF_MARGIN_RIGHT, PDF_MARGIN_TOP + 10);  //top border
        //$this->Line(PDF_MARGIN_LEFT, $this->getPageHeight()-$height, $this->getPageWidth()-PDF_MARGIN_RIGHT, $this->getPageHeight()-$height);  //bottom border
        /** \Added on 2nd Oct 2023 ***/
    }

    public function customFooter()
    {
        $header_print  = $_SESSION['header_print'];
        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
            <table id="footerTbl" cellspacing="0" cellpadding="2" border="1" nobr="true" >     
                <tr><td>Vehicle No.  : <br>Transporter   : <br>Loading By  : <br>Driver Name  : <br>Driver Mobile No.  : 
                </td></tr>          
                <tr valign="top">
                    <td colspan="2"  align="right" style="height:60px;">
                    For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b></span><br><br><br><br>
                    <span> Authorised Signatory&nbsp;&nbsp;</span>
                    </td>
                </tr>
                <tr>    
                    <td colspan="7" style="color:#5A7D29; text-align:center; font-size:15px; border-right:1px solid black; border-left:1px solid black;"> Bhumi Polymers Private Limited </td> 
                </tr>
                <tr>
                    <td colspan="7" style="border-bottom:1px solid black; border-right:1px solid black; border-left:1px solid black; text-align:center; ">$header_print</td>
                </tr>
            </table>
            EOD;
        return $tbl;
        $_SESSION = [];
    }
}