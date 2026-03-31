<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCPDF;
use App\Models\DispatchPlan;
use App\Models\DispatchPlanDetails;
use DB;
use Carbon\Carbon;
use Date;


class PrintFarmerWiseTotalDispatchPlanController extends Controller
{
    public function printFarmerWiseTotalDispatchPlan(Request $request, $id){
        
        $dp_data = DispatchPlan::where('dp_id', base64_decode($id))->first();

        if($dp_data->dp_date != "" && $dp_data->dp_date != null)
        {
            $dp_data->dp_date = Date::createFromFormat('Y-m-d', $dp_data->dp_date)->format('d/m/Y');
        }

        $dispatch_plan_details = DispatchPlanDetails::select(['dispatch_plan_details.dp_details_id',
        'dispatch_plan_details.dp_id',DB::raw('(CASE WHEN sales_order.so_from_value_fix = "location" THEN locations.location_name ELSE sales_order.customer_name END) as name'),
        ])
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('locations','locations.id', 'sales_order.to_location_id')
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
        ->where('items.print_dispatch_plan','Yes')
        ->where('dp_id', base64_decode($id)) 
        ->groupBy(['name'])
        ->get();   

        $so_no = DispatchPlanDetails::select('sales_order.so_number')
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')      
        ->where('dp_id', base64_decode($id))     
        ->groupBy('sales_order.so_number') 
        ->pluck('so_number') 
        ->implode(', '); 


        if($dispatch_plan_details->isEmpty()){
            // session()->flash('message','No Data Available For Print!');
            // return back();
            // return Redirect::back();
            return redirect()->back()->with('error', 'No Data Available For Print.'); 

        }else{

            $pdfName = 'Farmer_Wise_Total_Dispatch_'.str_replace("/", "_", $dp_data->dp_number);

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
            $pdf->setFont('helvetica', '', 9);
    
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
                $so_no = 'SO No.&nbsp;&nbsp;&nbsp;:&nbsp;' . $so_no ;
            }else{
                $so_no = '';
            }
            
            $counter = 0;

            $location  = getCurrentLocation();
       
            if($location->header_print != ""){
                $_SESSION['header_print'] = $location->header_print;
            }else{
                $_SESSION['header_print'] = '';
            }
          
    
            // $pdf->AddPage('P', 'A4');

            // LandScape Page
            // $pdf->AddPage('L', 'A5 PORTRAIT');
    
            $content = '';
            foreach($dispatch_plan_details as $key => $val){
                $counter++;
               
                $data = DispatchPlanDetails::select(
                     'items.item_name','items.print_dispatch_plan','items.second_unit','items.qty','units.unit_name','second_unit.unit_name as second_unit_name','villages.village_name','dealers.dealer_name',
                     DB::raw("SUM(dispatch_plan_details.plan_qty) as total_plan_qty"),
                     DB::raw("CASE WHEN sales_order.so_from_value_fix = 'location' THEN locations.location_name ELSE sales_order.customer_name END as farmer_name")
                 )
                 ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', '=', 'dispatch_plan_details.so_details_id')
                 ->leftJoin('sales_order', 'sales_order.id', '=', 'sales_order_details.so_id')
                 ->leftJoin('items', 'items.id', '=', 'dispatch_plan_details.item_id')
                 ->leftJoin('units', 'units.id', '=', 'items.unit_id')
                 ->leftJoin('units as second_unit', 'second_unit.id', '=', 'items.second_unit')              
                 ->leftJoin('locations', 'locations.id', '=', 'sales_order.to_location_id')         
                 ->leftJoin('villages','villages.id','=','sales_order.customer_village')      
                 ->leftJoin('dealers','dealers.id','=','sales_order.dealer_id')      
                 ->where('dispatch_plan_details.dp_id', $val['dp_id'])
                ->where('items.print_dispatch_plan','Yes')
                 ->groupBy('farmer_name', 'items.item_name', 'items.second_unit', 'items.qty', 'units.unit_name', 'second_unit.unit_name') // Grouping properly
                 ->get();
             
                $table_data = '';

                foreach ($data as $value) {
                    $farmerName = '<u>'.$value->farmer_name.'</u>';

                    if($value->village_name != null){
                        $farmerName = '<u>'.$value->farmer_name.'</u> - '.$value->village_name;
                    }

                    if($value->dealer_name != null){
                        $farmerName = '<u>'.$value->farmer_name.' - '.$value->village_name . '</u> , <b>Dealer</b> :&nbsp;&nbsp;<u>'.$value->dealer_name.'</u>';
                    }
                    $itemName = $value->item_name;
            
                    if (!isset($groupedData[$farmerName])) {
                        $groupedData[$farmerName] = [];
                    }
            
                    if (!isset($groupedData[$farmerName][$itemName])) {
                        $groupedData[$farmerName][$itemName] = [
                            'item_name' => $itemName,
                            'total_plan_qty' => 0,
                            'unit_name' => $value->unit_name,
                            'second_unit_name' => $value->second_unit_name,
                            'qty' => $value->qty,
                            'second_unit' => $value->second_unit
                        ];
                    }
            
                    // Sum the quantities for the same item
                    $groupedData[$farmerName][$itemName]['total_plan_qty'] = $value->total_plan_qty;
                }
            }
            
            // Now generate the HTML table
            $content = '';
            $counter = 0;
            
            foreach ($groupedData as $farmerName => $items) {
                $table_data = '';
            
                foreach ($items as $value) {
                    $counter++;
                    if ($value['second_unit'] != "") {
                        // $MeterQty = round(floatval($value['total_plan_qty']) / floatval($value['qty']), 3);
                        $qty = floatval($value['qty']);
                        $MeterQty = $qty != 0 ? round(floatval($value['total_plan_qty']) / $qty, 3) : 0;

                        $TotalMeter = floor($MeterQty);
                        $decimalPart = $MeterQty - $TotalMeter;
                        $decimalAsInt = $decimalPart * floatval($value['qty']);
            
                        if ($TotalMeter > 0) {
                            $table_data .= '<tr>
                                <td style="text-align:center; width:5%;">' . $counter . '</td>
                                <td style="width:72%;">' . $value['item_name'] . '</td>
                                <td style="text-align:center; width:13%;">' . number_format((float)$TotalMeter, 3, '.', '') . '</td>
                                <td style="text-align:center; width:10%;">' . $value['second_unit_name'] . '</td>
                            </tr>';
            
                            if ($decimalAsInt > 0) {
                                $table_data .= '<tr>
                                    <td style="text-align:center; width:5%;">' . $counter . '</td>
                                    <td style="width:72%;">' . $value['item_name'] . '</td>
                                    <td style="text-align:center; width:13%;">' . number_format((float)$decimalAsInt, 3, '.', '') . '</td>
                                    <td style="text-align:center; width:10%;">' . $value['unit_name'] . '</td>
                                </tr>';
                            }
                        } else {
                            $table_data .= '<tr>
                                <td style="text-align:center; width:5%;">' . $counter . '</td>
                                <td style="width:72%;">' . $value['item_name'] . '</td>
                                <td style="text-align:center; width:13%;">' . number_format((float)$decimalAsInt, 3, '.', '') . '</td>
                                <td style="text-align:center; width:10%;">' . $value['unit_name'] . '</td>
                            </tr>';
                        }
                    } else {
                        $table_data .= '<tr>
                            <td style="text-align:center; width:5%;">' . $counter . '</td>
                            <td style="width:72%;">' . $value['item_name'] . '</td>
                            <td style="text-align:center; width:13%;">' . number_format((float)$value['total_plan_qty'], 3, '.', '') . '</td>
                            <td style="text-align:center; width:10%;">' . $value['unit_name'] . '</td>
                        </tr>';
                    }
                }
            
                $content .= '
                    <tr>
                        <td colspan="4"><b>Name : &nbsp;&nbsp;</b>' . $farmerName . '</td>                                           
                    </tr>
                    ' . $table_data . '
                ';
            }
            
        }

        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
          <thead> 
                 <tr>
                    <th colspan="4" style="text-align:center; font-weight:bold; padding:6px;"><strong>FARMER WISE DISPATCH</strong></th>
                </tr>
                 <tr>
                    <th colspan="2" style="">$so_no</th>
                    <th colspan="2" style="">$dp_no$dp_date</th>
                </tr>
                 <tr>
                    <th style="text-align:center; width:5%;"><b>No.</b></th>
                    <th style="text-align:center; width:72%;"><b>Description</b></th>
                    <th style="text-align:center; width:13%;"><b>Disp. Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                </tr>             
            </thead>          
            <tbody>
                $content
            </tbody>                               
        </table>
        EOD;
    //    dd($tbl);
        
    $pdf->writeHTML($tbl, true, false, false, false, '');
    //$pdf->endPage(false);
    //$pdf->startTransaction();

    $content = $pdf->customFooter();
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