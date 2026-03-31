<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleOrderController;
use App\Models\Location;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrderDetailsDetails;
use Date;

use TCPDF;

class PrintSalesOrderFittingController extends Controller
{
    public function printSalesOrderFittingItem($id){

       
        
        $so_data = SalesOrder::select(['sales_order.customer_name','sales_order.so_number','sales_order.so_date','sales_order.so_from_id_fix','locations.location_name','sales_order.special_notes'])
        ->leftJoin('locations', 'locations.id', '=', 'sales_order.to_location_id')      
        ->where('sales_order.id',base64_decode($id))->first();

        $so_fiting_item = SalesOrderDetail::select(['sales_order_details.so_details_id','items.item_name',])
        ->leftJoin('items','items.id','=','sales_order_details.item_id')
        ->where('sales_order_details.fitting_item','yes')
        ->where('so_id',base64_decode($id))
        //->orderBy('items.item_name','asc')
        ->get();

       

        

         if($so_data->so_from_id_fix == '1' || $so_data->so_from_id_fix == '2'){
            $so_customer = $so_data->customer_name != "" ? $so_data->customer_name : "";
         }else{
           
            $so_customer = $so_data->location_name;
         }

         $soNumber =  $so_data->so_number != '' &&  $so_data->so_number != null ? str_replace("/", "_", $so_data->so_number) : "";
        if ($so_data != null && $so_customer != '') {
            $pdfName = 'Sales_Order_'.$soNumber.'_'.$so_customer;
        } else {
            $pdfName = 'Sales_Order_'.$soNumber.'_Bhumi Polymers Pvt. Ltd.';
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

        // $pdf->setHeader(function($pdf) use ($id){
        // $this->Header($pdf,$id);
        //});


        // set margins

        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 14.5, PDF_MARGIN_RIGHT, false);

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

        $_SESSION['so_data'] =   $so_data;

        if($so_fiting_item->isEmpty()){
          
            return back()->with('error', 'No Data Available For Print.'); 
          }else{
        
            if ($so_data->so_number != "") {
                $so_no = 'SO No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $so_data->so_number . '</b><br>';
            }else{
                $so_no = '';
            }

            if ($so_data->so_date != "") {
                $so_data->so_date = Date::createFromFormat('Y-m-d', $so_data->so_date)->format('d/m/Y');
                $so_date = 'SO Date.&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $so_data->so_date . '';
            }else{
                $so_date = '';
            }
            $counter = 0;
            $content = '';
            $table_data = '';

            $total_qty = 0;
             foreach($so_fiting_item as $key => $val){
                $table_data .= '<tr>
                <td colspan="6"><b><u>'.$val['item_name'].'</u></b></td>
                </tr>';

                $itemData = SalesOrderDetailsDetails::select(['items.item_name','item_groups.item_group_name','items.item_code','units.unit_name','sales_order_detail_details.so_qty'])
                ->leftJoin('items','items.id','=','sales_order_detail_details.item_id')
                ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
                ->leftJoin('units','units.id','=','items.unit_id')
                ->where('sales_order_detail_details.so_details_id',$val['so_details_id'])
                //->orderBy('item_groups.item_group_name','asc')
                //->orderBy('items.item_name','asc')
                ->get();

              
                foreach($itemData as $ikey=>$ival){
                    $counter++;
                    $total_qty += $ival['so_qty'];
                    $table_data .= '<tr>
                    <td style="text-align:center; width:5%;">'. $counter.'</td>
                    <td style="width:56%;">'.$ival['item_name'].'</td>
                    <td style="text-align:center; width:12%;">'.$ival['item_code'].'</td>                   
                    <td style="text-align:center; width:15%;">'. number_format((float)$ival['so_qty'], 3, '.','').'</td>
                    <td style="text-align:center; width:12%;">'.$ival['unit_name'].'</td>
                    </tr>';
                    //  <td style="text-align:center; width:13%;">'.$ival['item_group_name'].'</td>
                }
             }
             $total_qty = number_format((float)$total_qty, 3, '.','');
        
            $tbl = <<<EOD
            <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
                <thead>            
                    <tr>
                        <th colspan="6" style="text-align:center; font-weight:bold; padding:6px;"><strong>SO FITTING ITEMS</strong></th>
                    </tr>
                    <tr>
                        <td colspan="3"><b>$so_customer</b></td>
                        <td colspan="3">$so_no$so_date</td>
                    </tr>
                    <tr>
                        <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                        <th style="text-align:center; width:56%;"><b>Item</b></th>
                        <th style="text-align:center; width:12%;"><b>Code</b></th>
                        
                        <th style="text-align:center; width:15%;"><b>SO Qty.</b></th>
                        <th style="text-align:center; width:12%;"><b>Unit</b></th>                    
                    </tr>
                </thead>
                <tbody>
                 $table_data 
                 
                </tbody>                               
            </table>
            EOD;
            // <th style="text-align:center; width:13%;"><b>Group</b></th>
        }

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

        global $headerheight;


        $location = getCurrentLocation();

        $imagePath = public_path('images/logo/bhumi_logo_print.jpg');

        $tbl = <<<EOD
                     
        <table width="100%" cellpadding="2" cellspacing="0" border="1">  
        <tr>
            <td>
                <table>
                    <tr>
                        <td width="25%"><img src="$imagePath"></td>        
                        <td width="5%"></td>        
                        <td width="70%">$location->header_print</td>    
                    </tr>
                </table>
            </td>           
        </tr>
        </table>
        EOD;

        $this->writeHTMLCell(0, '', '', '', $tbl, 0, 0, false,true, "L", true);

      
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
            $this->setY(-5);
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
        $so_data   = $_SESSION['so_data'];

        $sp_note = $so_data['special_notes'];
      
        $tbl = <<<EOD
            <table id="footerTbl" cellspacing="0" cellpadding="0" border="1" nobr="true" >               
                <tr>
                    <td>Sp. Note : $sp_note </td>
                    <td  style="text-align:right;height:60px;">
                    For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b></span><br><br><br><br>
                    <span> Authorised Signatory&nbsp;&nbsp;</span>
                    </td>
                </tr>
            </table>

            EOD;


        return $tbl;


        $_SESSION = [];
    }
}