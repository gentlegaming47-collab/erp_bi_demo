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

        
        $dp_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',           'dispatch_plan_details.dp_id','dispatch_plan_details.item_id','items.item_name',
        'units.unit_name as unitName','items.second_unit as second_unit_id','items.qty','items.show_item_in_print',
        'second_unit.unit_name as second_unit',DB::raw("SUM(dispatch_plan_details.plan_qty) as plan_qty"),        
        'dealers.dealer_name','dealers.mobile_no','villages.village_name as customer_village',DB::raw('(CASE WHEN sales_order.so_from_value_fix = "location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        ])
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('villages', 'villages.id', '=', 'sales_order.customer_village')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->leftJoin('units as second_unit', 'second_unit.id', '=', 'items.second_unit')
        ->leftJoin('dealers', 'dealers.id', 'sales_order.dealer_id')
        ->where('items.print_dispatch_plan','Yes')
        ->where('dp_id', base64_decode($id)) 
        ->groupBy(['name','customer_village','dealer_name', 'items.item_name',])
        ->get();   

        // dd($dp_details);

        
        // Create Table for Temporary
       
        DB::statement("CREATE TEMPORARY TABLE temp_item(
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(100) NOT NULL,
        raw_items TEXT NULL,  
        unit VARCHAR(255) NOT NULL,
        qty VARCHAR(255) NOT NULL,
        group_by VARCHAR(255) NOT NULL)");   

       

        if($dp_details->isEmpty()){
          
          return back()->with('error', 'No Data Available For Print.'); 
        }else{

            $counter = 0;
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
                        
                        // $rawMaterials = ItemRawMaterialMappingDetail::leftjoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                        //                 ->where('item_raw_material_mapping_details.item_id', $value['item_id'])
                        //                 ->pluck('raw_items.item_name');
                          $rawMaterials = ItemRawMaterialMappingDetail::select('raw_items.item_name', 'item_raw_material_mapping_details.raw_material_qty')
                        ->leftJoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                        ->where('item_raw_material_mapping_details.item_id', $value['item_id'])
                        ->get();


                        if ($rawMaterials->count() > 0) {
                            // $rawItemsHtml .= '<ul>';
                            // foreach ($rawMaterials as $rawItem) {
                            //     $rawItemsHtml .= '<br> <b>•</b> '. $rawItem ;
                            // }
                            foreach ($rawMaterials as $rawItem) {
                                $rawItemsHtml .= '<br> <b>•</b> ' . $rawItem->item_name . ' - ' . number_format((float)$rawItem->raw_material_qty , 3, '.','');
                            }
                            // $rawItemsHtml .= '</ul>';
                        }
                    } 
                // dd($rawItemsHtml);
                if($value['second_unit_id'] != ""){                   
                    // $MeterQty = round(floatval($value['plan_qty']) / floatval($value['qty']), 3);
                    $qty = floatval($value['qty']);
                    $MeterQty = $qty != 0 ? round(floatval($value['total_plan_qty']) / $qty, 3) : 0;

                    $TotalMeter   = floor($MeterQty); // value in meter
                    $decimalPart  = $MeterQty - $TotalMeter; //value in decimal like 0.500 
                    $decimalAsInt = $decimalPart * floatval($value['qty']); // 0.500 convert like 500 meter == katko
                    if($TotalMeter > 0){
                        DB::table('temp_item')->insert([
                            ['item_name' => $value['item_name'], 'raw_items' => $rawItemsHtml , 'unit' => $value['second_unit'],'qty' =>$TotalMeter,'group_by' => 1],
                        ]);
                        if($decimalAsInt > 0){                     
                            DB::table('temp_item')->insert([
                                ['item_name' => $value['item_name'], 'raw_items' => $rawItemsHtml ,'unit' => $value['unitName'],'qty' =>$decimalAsInt,'group_by' => 0],
                            ]);
                        }                          
                    }else{                 
                        DB::table('temp_item')->insert([
                            ['item_name' => $value['item_name'], 'raw_items' => $rawItemsHtml , 'unit' => $value['unitName'],'qty' =>$decimalAsInt,'group_by' => 0],
                        ]);
                    }
                }else{    
                    // dd($value);            
                    DB::table('temp_item')->insert([
                        ['item_name' => $value['item_name'], 'raw_items' => $rawItemsHtml ,'unit' => $value['unitName'],'qty' =>$value['plan_qty'],'group_by' => 1],
                    ]);
                } 
              
            }
        }
    
    

        $items = DB::table('temp_item')->get();


// dd($items);
        $groupedItems = $items->groupBy(function ($item) {
            return ($item->group_by == 1) ? $item->item_name . '-' . $item->group_by : $item->id;
        })->map(function ($group) {
            $first = $group->first();
            return (object) [
                'id' => $first->id,
                'item_name' => $first->item_name,
                'raw_items' => $first->raw_items,
                'unit' => $first->unit,
                'qty' => ($first->group_by == 1) ? $group->sum('qty') : $first->qty,
                'group_by' => $first->group_by,
            ];
        })->values(); // Reset the array keys
        // dd($groupedItems);
        $groupedItems = $groupedItems->sortBy('item_name')->values();
        foreach($groupedItems as $key=>$val){
            $counter++;
            $content .='<tr>
                        <td style="text-align:center; width:5%;">'.$counter.'</td>
                         <td style="text-align:left; width:75%;">'.$val->item_name.' '.$val->raw_items.'</td>
                          <td style="text-align:center; width:10%;">'.(int)$val->qty.'</td>
                          <td style="text-align:center; width:10%;">'.$val->unit.'</td>
                        </tr>';
        }


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
            $dp_date = 'Disp. Plan Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $dp_data->dp_date .'<br>';
        }else{
            $dp_date = '';
        }

        if ($so_no != "") {
            $so_no = 'SO No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $so_no ;
        }else{
            $so_no = '';
        }

        if ($dealerName != "") {
            $dealer_name = 'Dealer  &nbsp;:&nbsp;' . $dealerName;
        }else{
            $dealer_name = '';
        }

        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="9" style="text-align:center; font-weight:bold; padding:6px;"><strong>TOTAL DISPATCH</strong></th>
                </tr>
                 <tr>
                    <th colspan="4" style="">$dealer_name</th>
                    <th colspan="5" style="">$dp_no$dp_date$so_no</th>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>No.</b></th>
                    <th style="text-align:center; width:75%;"><b>Description</b></th>
                    <th style="text-align:center; width:10%;"><b>Disp. Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                </tr>
            </thead>
            <tbody>
                $content
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

        // Remove Temporary Table
        DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_item");
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