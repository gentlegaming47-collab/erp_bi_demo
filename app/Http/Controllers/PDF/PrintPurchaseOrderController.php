<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use Illuminate\Support\Facades\Date;

use App\Http\Controllers\PurchaseOrderController;
use App\Models\Village;
use TCPDF;


class PrintPurchaseOrderController extends Controller
{
    public function printPurchaseOrder(Request $request, $id)
    {

        $request->id = base64_decode($id);

        $purchase_order = PurchaseOrder::select(
            'purchase_order.po_id',
            'purchase_order.po_number',
            'purchase_order.po_date',
            'purchase_order.supplier_id',
            'purchase_order.order_by',
            'purchase_order.person_name',
            'purchase_order.test_certificate',
            'purchase_order.delivery_date',
            'purchase_order.gst',
            'purchase_order.order_acceptance',
            'purchase_order.prepared_by',
            'purchase_order.special_notes',
            'suppliers.supplier_name',
            'suppliers.GSTIN',
            'suppliers.address',
            'suppliers.pincode',
            'suppliers.contact_person_mobile',
            'villages.village_name',
            'location_village.village_name as location_village',
            'villages.default_pincode',
            'talukas.taluka_name',
            'districts.district_name',
            'states.state_name',
            'countries.country_name',
            'locations.location_name',
        )
        ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_order.supplier_id')
        ->leftJoin('villages', 'villages.id', '=', 'suppliers.village_id')
        ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
        ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
        ->leftJoin('states', 'states.id', '=', 'districts.state_id')
        ->leftJoin('countries', 'countries.id', '=', 'states.country_id')
        ->leftJoin('locations', 'locations.id', '=', 'purchase_order.to_location_id')
        ->leftJoin('villages as location_village', 'location_village.id', '=', 'locations.village_id')        
        ->where('po_id','=',$request->id)->first();

        $purchase_order_details = PurchaseOrderDetails::select(
             'items.item_name', 'units.unit_name', 'purchase_order_details.po_qty','purchase_order_details.discount','purchase_order_details.amount','purchase_order_details.remarks','purchase_order_details.rate_per_unit',
        )
        ->leftJoin('items', 'items.id', '=', 'purchase_order_details.item_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->where('po_id','=',$request->id)->get();


        $purchase_order->po_date = Date::createFromFormat('Y-m-d', $purchase_order->po_date)->format('d/m/Y');
        if($purchase_order->ref_date != "" && $purchase_order->ref_date != null)
        {
            $purchase_order->ref_date = Date::createFromFormat('Y-m-d', $purchase_order->ref_date)->format('d/m/Y');
        }
        if($purchase_order->delivery_date != "0000-00-00" && $purchase_order->delivery_date != null)
        {
            $purchase_order->delivery_date = Date::createFromFormat('Y-m-d', $purchase_order->delivery_date)->format('d/m/Y');
            }else{
                $purchase_order->delivery_date = "";
        }

      

        $poNumber =  $purchase_order->po_number != '' &&  $purchase_order->po_number != null ? str_replace("/", "_", $purchase_order->po_number) : "";
        if ($purchase_order != null && $purchase_order->supplier_name != '') {
            $pdfName = 'Purchase_Order_' . $poNumber . '_' . str_replace(" ", "_", $purchase_order->supplier_name);
        } else {
            $pdfName = 'Purchase_Order_' . $poNumber . '_Bhumi Polymers Pvt. Ltd.';
        }

        $pdfName = trim($pdfName, ".");



        if ($purchase_order->po_number != "") {
            $po_no = 'PO No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $purchase_order->po_number . '</b>';
        } else {
            $po_no = '';
        }


        if ($purchase_order->po_date != "") {
            $po_date = '<br>PO Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $purchase_order->po_date;
        } else {
            $po_date = '';
        }

        if ($purchase_order != "") {
            $_SESSION['purchase_order'] = $purchase_order;
        }

        if ($purchase_order->order_by != "") {
            $ref = '<br>Order By &nbsp;&nbsp;:&nbsp;' . $purchase_order->order_by;
        } else {
            $ref = '<br>Order By &nbsp;&nbsp;:&nbsp;';
        }

        if ($purchase_order->person_name != "") {
            $person_name = '<br>Kind Attn.  &nbsp;&nbsp;:&nbsp;' . $purchase_order->person_name;

            if($purchase_order->contact_person_mobile != ''){
                    $person_name = '<br>Kind Attn.  &nbsp;&nbsp;:&nbsp;' . $purchase_order->person_name. ' - '. $purchase_order->contact_person_mobile;
            }else{
                    $person_name = '<br>Kind Attn.  &nbsp;&nbsp;:&nbsp;' . $purchase_order->person_name;
            }
        } else {
            $person_name = '<br>Kind Attn. &nbsp;&nbsp;:&nbsp;';
        }

        if ($purchase_order->location_name != "") {
            $location_name = '<br>Ship To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $purchase_order->location_name;
        } else {
            $location_name = '';
        }

        $counter = 0;
        $content = '';


        if ($purchase_order->address != "") {
            $address  = '<br>' . $purchase_order->address . ', ';
        } else {
            $address = '';
        }

        $fulladdress = '<br>' . checkPOAddress($purchase_order->village_name,                               $purchase_order->pincode, $purchase_order->taluka_name, $purchase_order->district_name, $purchase_order->state_name, $purchase_order->country_name);



        $header_table = ' <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
             
                <tr>
                    <th colspan="8" style="text-align:center; font-weight:bold; padding:6px;"><strong>PURCHASE ORDER</strong></th>
                </tr>
                <tr>
                    <td colspan="4">To, <br>' . $purchase_order->supplier_name . $address . $fulladdress . '</td>
                    <td colspan="4">' . $po_no . $po_date . $ref . $person_name . $location_name. '</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:4%;"><b>Sr.No.</b></th>
                    <th style="text-align:center; width:50%;"><b>Description</b></th>
                    <th style="text-align:center; width:12%;"><b>Quantity</b></th>
                    <th style="text-align:center; width:6%;"><b>Unit</b></th>
                    <th style="text-align:center; width:9%;"><b>Rate</b></th>
                    <th style="text-align:center; width:7%;"><b>Dis.(%)</b></th>
                    <th style="text-align:center; width:12%;"><b>Amount</b></th>
                </tr>
           </table>';

        $_SESSION['header_table'] = $header_table;


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
        
  
        $count =  0 ;
        $extratopMargin = 46;
        
        if($purchase_order->address === strtoupper($purchase_order->address)){
            if(strlen($address) > 165){
                $extratopMargin = 60;
            }elseif(strlen($address) > 105){
                $extratopMargin = 54;
            }elseif(strlen($address) > 99){
                $extratopMargin = 54;
            }else if(strlen($address) > 55){
                $extratopMargin = 50;
            }

        }else{
            if(strlen($address) > 214){
                $extratopMargin = 58;
            }else if(strlen($address) > 171){
                $extratopMargin = 54;
            }else if(strlen($address) > 125){
                $extratopMargin = 54;
            }elseif(strlen($address) > 70){
                $extratopMargin = 54;
            }else if(strlen($address) > 66){
                $extratopMargin = 50;
            }
        }

        $checkLength = checkPOAddress($purchase_order->village_name,                               $purchase_order->pincode, $purchase_order->taluka_name, $purchase_order->district_name, $purchase_order->state_name, $purchase_order->country_name);

        $addLength = strlen($checkLength);

        if($addLength > 90){
            $extratopMargin = $extratopMargin + 3.8;
        }

        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + $extratopMargin, PDF_MARGIN_RIGHT, false);

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



        foreach ($purchase_order_details as $key => $val) {

            $_SESSION['gstAmtWord'] = '';

            $counter++;
            $qty[] = $val['po_qty'];
            $total_qty = array_sum($qty);
            $total_qty = number_format((float)$total_qty, 3, '.', '');

            $_SESSION['totalAmount'] = '';
            $amount[] = $val['amount'];
            $total_amount = array_sum($amount);
            $total_amount = number_format((float)$total_amount, 3, '.', '');
            $_SESSION['totalAmount'] = $total_amount;

            $content .= '<tr>             
                            <td style="text-align:center; width:4%;">' . $counter . '</td>
                            <td style="text-align:left; width:50%;">' . $val['item_name'] . '</td>
                            <td style="text-align:center; width:12%;">' . number_format((float)$val['po_qty'], 3, '.', '') . '</td>
                            <td style="text-align:center; width:6%;">'  . $val['unit_name'] . '</td>
                            <td style="text-align:center; width:9%;">' . number_format((float)$val['rate_per_unit'], 3, '.', '') . '</td>
                            <td style="text-align:center; width:7%;">' . number_format((float)$val['discount'], 2, '.', '') . '</td>
                            <td style="text-align:right; width:12%;">'  . number_format((float)$val['amount'], 3, '.', '') . '</td>  
                        </tr>';
        }


        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            
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

        $tbl_header = $_SESSION['header_table'];

        $header = commonPdfHeader("PO");
        $header .= $tbl_header;

        $this->writeHTMLCell(0, '', '', '', $header, 0, 0, false, true, "L", true);
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
            $height = 8 + $this->fHeight;
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
        $po_data =  $_SESSION['purchase_order'];
        $location  = getCurrentLocation();
        $total_amount  = $_SESSION['totalAmount'];
        $Delivery_Terms  = $po_data->location_village != '' ? $po_data->location_village  : '';
        $delivery_date  = $po_data->delivery_date != '' ? $po_data->delivery_date  : '';
        $gst  = $po_data->gst != '' ? $po_data->gst  : '';
        $test_certificate  = $po_data->test_certificate != '' ? $po_data->test_certificate  : '';
        $order_acceptance  = $po_data->order_acceptance != '' ? $po_data->order_acceptance  : '';
        $prepared_by  = $po_data->prepared_by != '' ? $po_data->prepared_by  : '';
        $special_notes  = $po_data->special_notes != '' ? $po_data->special_notes  : '';
        $gstAmtWord = digitsToWords($_SESSION['totalAmount']);
        $header_print     = $location->header_print;


        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                    <table id="footerTbl" cellspacing="0" cellpadding="2" border="1" nobr="true">
                        <tr>
                            <td style="font-size:10px;width:61%;">Amount in Words : $gstAmtWord</td>
                            <td style="width:12%; text-align:center;"><b>Grand Total</b></td>
                            <td style="text-align:left;width:27%;"><b> $total_amount</b></td> 
                        </tr>
                        <tr>
                            <td style="width:20%;">Delivery Place</td>
                            <td style="width:20%;"><b>$Delivery_Terms</b></td> 
                            <td rowspan="6" colspan="6" style="width:60%;"> NOTE: $special_notes </td>                 
                        </tr>
                        <tr>
                            <td>Delivery Peroid</td> 
                            <td><b>$delivery_date </b> </td>
                        </tr>
                        <tr>
                            <td>GST</td> 
                            <td>$gst </td>
                        </tr>
                        <tr>
                            <td>Test Certificate</td> 
                            <td>$test_certificate </td>
                        </tr>
                        <tr>
                            <td>Order Acceptance</td> 
                            <td>$order_acceptance</td>
                        </tr>  
                        <tr>
                            <td>Quantity Variation</td> 
                            <td>± 5% Acceptable</td>
                        </tr>  
                        <tr valign="top">
                            <td colspan="2" style="font-size:9;"></td>
                            <td colspan="3" style="text-align:center;"> Prepared By <p style="border-top:1px solid black;"> $prepared_by </p></td>
                            <td colspan="3" style="height:60px;text">
                            For,<span> Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</span><br><br><br><br>
                            <span> Authorised Signatory&nbsp;&nbsp;</span>
                            </td>
                        </tr>
                        <tr>    
                            <td colspan="8" style="color:#5A7D29; text-align:center; font-size:15px; border-right:1px solid black; border-left:1px solid black;"> Bhumi Polymers Private Limited </td> 
                        </tr>
                        <tr>
                            <td colspan="8" style="border-bottom:1px solid black; border-right:1px solid black; border-left:1px solid black; text-align:center; ">$header_print</td>
                        </tr>
                    </table>
                    EOD;

        return $tbl;
        $_SESSION = [];
    }
}