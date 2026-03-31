<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\DispatchPlanDetails;
use App\Models\LoadingEntry;
use App\Models\LoadingEntryDetails;
use Illuminate\Http\Request;
use App\Models\MisCategory;
use App\Models\Village;
use App\Models\Location;
use App\Models\Dealer;
use App\Models\DealerContacts;
use App\Models\SalesOrderDetail;
use TCPDF;
use Illuminate\Support\Facades\Date;


class PrintLoadingEntryController extends Controller
{
    public function printLoadingEntry(Request $request,$id){

       

        $loading_data = LoadingEntryDetails::select(['transporters.transporter_name','loading_entry.vehicle_no','loading_entry.loading_by','loading_entry.driver_name','loading_entry.driver_mobile_no','dispatch_plan.dp_number','dispatch_plan.dp_date'])
        ->leftJoin('loading_entry','loading_entry.le_id','loading_entry_details.le_id')
        ->leftJoin('dispatch_plan_details','dispatch_plan_details.dp_details_id','loading_entry_details.dp_details_id')      
        ->leftJoin('dispatch_plan','dispatch_plan.dp_id','dispatch_plan_details.dp_id')      
        ->leftJoin('transporters','transporters.id','loading_entry.transporter_id')
        ->where('loading_entry.le_id',base64_decode($id))->first();


        if($loading_data->dp_date != "" && $loading_data->dp_date != null)
        {
            $loading_data->dp_date = Date::createFromFormat('Y-m-d', $loading_data->dp_date)->format('d/m/Y');
        }

          $_SESSION['loading_data'] = $loading_data;

        

         $dpNumber =  $loading_data['dp_number'] != '' &&  $loading_data['dp_number'] != null ? str_replace("/", "_", $loading_data['dp_number']) : "";
         if ($loading_data != null) {
             $pdfName = 'Loading_Entry_'.$dpNumber;
         } else {
             $pdfName = 'Loading_Entry_'.$dpNumber.'_Bhumi Polymers Pvt. Ltd.';
         }



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
 
         // $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_RIGHT, false); // fot 1366 screen
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

        //  if ($loading_data['so_number'] != "") {
        //     $so_no = 'SO No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $loading_data['so_number'] . '</b><br>';
        // }else{
        //     $so_no = '';
        // }

        // if ($loading_data['so_date'] != "") {
        //     $so_date = 'SO Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $loading_data['so_date'] . '<br>';
        // }else{
        //     $so_date = '';
        // }

        // if ($loading_data['transporter_name'] != "") {
        //     $transporter_name = 'Transporter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $loading_data['transporter_name'] . '<br>';
        // }else{
        //     $transporter_name = '';
        // }

        // if ($loading_data['vehicle_no'] != "") {
        //     $vehicle_no = 'Vehicle No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $loading_data['vehicle_no'] . '<br>';
        // }else{
        //     $vehicle_no = '';
        // }

        // if ($loading_data['driver_name'] != "") {
        //     $driver_name = 'Driver Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  :&nbsp;' . $loading_data['driver_name'] . '<br>';
        // }else{
        //     $driver_name = '';
        // }

        // if ($loading_data['driver_mobile_no'] != "") {
        //     $driver_mobile_no = 'Driver Mobile No.&nbsp; :&nbsp;' . $loading_data['driver_mobile_no'] . '';
        // }else{
        //     $driver_mobile_no = '';
        // }

        if ($loading_data->dp_number != "") {
            $dp_no = 'Disp. Plan No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $loading_data->dp_number . '</b><br>';
        }else{
            $dp_no = '';
        }

        if ($loading_data->dp_date != "") {
            $dp_date = 'Disp. Plan Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $loading_data->dp_date;
        }else{
            $dp_date = '';
        }
      
        $location  = getCurrentLocation();
       
        if($location->header_print != ""){
            $_SESSION['header_print'] = $location->header_print;
        }else{
            $_SESSION['header_print'] = '';
        }


        // Qty Details

        $getItemData = LoadingEntryDetails::select(  
            'sales_order.so_number', 
            'sales_order.customer_name',         
            'dealers.dealer_name',
            'dealers.gst_code',
            'units.unit_name',
            'items.item_name',
            'dispatch_plan_details.fitting_item',
            'loading_entry_details.loading_qty','sales_order_details.rate_per_unit','sales_order_details.discount','sales_order_details.so_amount','sales_order_details.so_qty'
        )
        ->leftJoin('loading_entry', 'loading_entry.le_id', 'loading_entry_details.le_id')
        ->leftJoin('dispatch_plan_details', 'dispatch_plan_details.dp_details_id', 'loading_entry_details.dp_details_id')
        ->leftJoin('sales_order_details', 'sales_order_details.so_details_id', 'dispatch_plan_details.so_details_id')
        ->leftJoin('sales_order', 'sales_order.id', 'sales_order_details.so_id')
        ->leftJoin('dealers', 'dealers.id', 'sales_order.dealer_id')
        ->leftJoin('items', 'items.id', 'dispatch_plan_details.item_id')
        ->leftJoin('units', 'units.id', 'items.unit_id')
        ->where('loading_entry.le_id', base64_decode($id))
        ->orderBy('sales_order.customer_name')
        ->orderBy('dealers.dealer_name')
        ->get()
         ->groupBy(['customer_name']);   // Grouping here for PDF building
    


         $content = '';
         $total_qty = 0;
         foreach($getItemData as $key => $val){
             $table_data = '';
             $gst_number = '';
             $dealer_name = '';
             $counter = 0;
             $total_amount = 0;
             foreach($val as $iKey => $iVal){
                    if($iVal['fitting_item'] == 'yes'){
                        $iVal['loading_qty'] = $iVal['so_qty'];
                    }else{
                        $iVal['loading_qty'] = $iVal['loading_qty'];
                    }
                $dealer_name = $iVal->dealer_name;
                // $gst_number = $iVal->gst_code != null ? ', GSTIN  : '. $iVal->gst_code : '';
                $counter ++;
                $total_qty += $iVal['loading_qty']; 
                $total_qty = number_format((float)$total_qty, 3, '.','');   
                
                $qty = (float)$iVal['loading_qty'];
                $rate = (float)$iVal['rate_per_unit'];
                $discount_percent = (float)$iVal['discount'];

                $gross_amount = $qty * $rate;
                $discount_amount = ($gross_amount * $discount_percent) / 100;
                $amount = $gross_amount - $discount_amount;

                $total_amount += $amount;
            
    
                $table_data .= '<tr>             
                             <td style="text-align:center; width:5%;">' . $counter . '</td>
                             <td style="text-align:center; width:17%;">' . $iVal['so_number'] . '</td>                            
                             <td style="width:32%;">' . $iVal['item_name'] . '</td>                            
                             <td style="text-align:center; width:8%;">' . number_format((float)$iVal['loading_qty'], 3, '.','') . '</td>
                             <td style="text-align:center; width:7%;">' . $iVal['unit_name'] . '</td>
                             <td style="text-align:center; width:10%;">' . number_format((float)$iVal['rate_per_unit'], 2, '.','') .'</td>
                            <td style="text-align:center; width:7%;">' . number_format((float)$iVal['discount'], 2, '.','') . '</td>
                            <td style="text-align:right; width:14%;">' . number_format((float)$amount, 2, '.','') . '</td>   
                            </tr>';
            }

            $content .= '
            <tr>
                <td colspan="8"><b>Farmer : </b><u>'. $key . '</u> <br><b>Dealer : </b><u>'. $dealer_name . '</u></td>                                           
            </tr>           
            ' . $table_data . '
               <tr>
                <td colspan="7" style="text-align:right;"><b>Total Amount : </b></td>                                           
                <td style="text-align:right;">'.number_format((float)$total_amount, 2, '.','') .'</td>                                           
            </tr>    
        ';

          
        }

        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                      <th colspan="4" style="text-align:center; font-weight:bold; padding:6px;"><strong>LOADING ENTRY</strong></th>
                </tr>
                 <tr>
                    <th colspan="2" style=""></th>
                    <th colspan="2" style="">$dp_no$dp_date</th>
                </tr>
                  <tr>
                    <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                    <th style="text-align:center; width:17%;"><b>SO No.</b></th>
                    <th style="text-align:center; width:32%;"><b>Item</b></th>
                    <th style="text-align:center; width:8%;"><b>Plan Qty.</b></th>
                    <th style="text-align:center; width:7%;"><b>Unit</b></th>
                    <th style="text-align:center; width:10%;"><b>Rate/Unit</b></th>
                    <th style="text-align:center; width:7%;"><b>Dis.(%)</b></th>
                    <th style="text-align:center; width:14%;"><b>Amount</b></th>
                </tr>        
            </thead>
            <tbody>
                $content
               
            </tbody>                               
        </table>
        EOD;


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


    public function Header()
    {
        $header = commonPdfHeader("LE");
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
            $height =  8 + $this->fHeight;
            $this->setY(-$height);
            //$this->setAutoPageBreak(TRUE, 80);
            $content = $content2;
        } else {
            $this->setY(-8);
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

        $loading_data = $_SESSION['loading_data'];

        $vehicle_no = $loading_data->vehicle_no != '' ? $loading_data->vehicle_no  : '';
        $transporter_name = $loading_data->transporter_name != '' ? $loading_data->transporter_name  : '';
        $loading_by = $loading_data->loading_by != '' ? $loading_data->loading_by  : '';
        $driver_name = $loading_data->driver_name != '' ? $loading_data->driver_name  : '';
        $driver_mobile_no = $loading_data->driver_mobile_no != '' ? $loading_data->driver_mobile_no  : '';
      

       $tbl = <<<EOD
           <table id="footerTbl" cellspacing="0" cellpadding="2" border="1" nobr="true" >      
                    
                <tr>
                    <td style="width:20%;">Vehicle No.</td>
                    <td style="width:20%;">$vehicle_no</td> 
                    <td rowspan="5" colspan="3"  align="right" style="height:60px;">
                    For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b></span><br><br><br><br><br>
                    <span> Authorised Signatory&nbsp;&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <th>Transporter</th> 
                    <td>$transporter_name </td>
                </tr>
                <tr>
                    <th>Loading By</th> 
                    <td>$loading_by </td>
                </tr>           
                <tr>
                    <th>Driver Name </th> 
                    <td>$driver_name </td>
                </tr>           
                <tr>
                    <th>Driver Mobile No. </th> 
                    <td>$driver_mobile_no </td>
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