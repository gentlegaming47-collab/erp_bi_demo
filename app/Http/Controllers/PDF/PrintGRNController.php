<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\Item;
use App\Models\Location;
use App\Models\GRNMaterial;
use App\Models\GRNMaterialDetails;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GRNMaterialController;
use App\Models\PurchaseOrder;
use Illuminate\Mail\Transport\Transport;
use TCPDF;

class PrintGRNController extends Controller
{
    public function printGRN(Request $request, $id){

        $request->id = base64_decode($id);
    
        $grnMaterial = GRNMaterial::select(
            'grn_material_receipt.grn_id',
            'grn_material_receipt.grn_number',
            'grn_material_receipt.grn_date',
            'grn_material_receipt.bill_no',
            'grn_material_receipt.bill_date',
            'transporters.transporter_name',
            'grn_material_receipt.vehicle_no',
            'grn_material_receipt.lr_no_date',
            'grn_material_receipt.special_notes',
            'grn_material_receipt.supplier_id',
            'suppliers.supplier_name',
            'suppliers.GSTIN',
            'suppliers.address',
            'suppliers.pincode',
            'villages.village_name',
            'villages.default_pincode',
            'talukas.taluka_name',
            'districts.district_name',
            'states.state_name',
            'countries.country_name',
        )
        ->leftJoin('suppliers', 'suppliers.id', '=', 'grn_material_receipt.supplier_id')
        ->leftJoin('transporters', 'transporters.id', '=', 'grn_material_receipt.transporter_id')
        ->leftJoin('villages', 'villages.id', '=', 'suppliers.village_id')
        ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
        ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
        ->leftJoin('states', 'states.id', '=', 'districts.state_id')
        ->leftJoin('countries', 'countries.id', '=', 'states.country_id')
        ->where('grn_id', $request->id)->first();

        $grnMaterial->grn_date = Date::createFromFormat('Y-m-d', $grnMaterial->grn_date)->format('d/m/Y');


        if($grnMaterial->bill_date != "" && $grnMaterial->bill_date != null)
        {
            $grnMaterial->bill_date = Date::createFromFormat('Y-m-d', $grnMaterial->bill_date)->format('d/m/Y');
        }

        // dd($grnMaterial);

        $grnMaterialDetails = GRNMaterialDetails::select('items.item_name','purchase_order.po_number','purchase_order.po_date','units.unit_name','material_receipt_grn_details.grn_qty','purchase_order_details.po_qty','material_receipt_grn_details.rate_per_unit','material_receipt_grn_details.remarks')
        ->leftJoin('purchase_order_details','purchase_order_details.po_details_id','=','material_receipt_grn_details.po_details_id')
        ->leftJoin('purchase_order','purchase_order.po_id','=','purchase_order_details.po_id')
        ->leftJoin('items', 'items.id', 'material_receipt_grn_details.item_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('grn_id','=',$request->id)
        ->get();


        if ($grnMaterialDetails != null) {
            foreach ($grnMaterialDetails as $cpKey => $cpVal) {
                if ($cpVal->po_date != null) {
                    $cpVal->po_date = Date::createFromFormat('Y-m-d', $cpVal->po_date)->format('d/m/Y');
                }
            }
        }


        
         $supplier_name =  $grnMaterial->supplier_name != "" ? $grnMaterial->supplier_name :  '';


         $grnNumber =  $grnMaterial->grn_number != '' &&  $grnMaterial->grn_number != null ? str_replace("/", "_", $grnMaterial->grn_number) : "";

         if ($grnMaterial != null && $supplier_name != '') {
             $pdfName = 'GRN_'.$grnNumber.'_'.$supplier_name;
         } else {
             $pdfName = 'GRN_'.$grnNumber.'_Bhumi Polymers Pvt. Ltd.';
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

      
        if ($grnMaterial->grn_number != "") {
            $grn_no = 'GRN No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $grnMaterial->grn_number . '</b>';
        }else{
            $grn_no = '';
        }


        if ($grnMaterial->grn_date != "") {
            $grn_date = '<br>GRN Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $grnMaterial->grn_date;
        }else{
            $grn_date = '';
        }

        if ($grnMaterial->bill_no != "") {
            $challan_no = '<br>Challan No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $grnMaterial->bill_no;
        }else{
            $challan_no = '';
        }

        if ($grnMaterial->bill_date != "") {
            $challan_date = '<br>Challan Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $grnMaterial->bill_date;
        }else{
            $challan_date = '';
        }

        if($grnMaterial != ""){
            $_SESSION['grnMaterial'] = $grnMaterial;
        }


        $counter = 0;
        $content = '';


        if($grnMaterial->address != ""){
            $address  = '<br>'.$grnMaterial->address .', ';
        }else{
            $address = '';
        }

       $fulladdress = '<br>'.checkAddress($grnMaterial->village_name,                               $grnMaterial->pincode,$grnMaterial->taluka_name,$grnMaterial->district_name,$grnMaterial->state_name,$grnMaterial->country_name);

       foreach($grnMaterialDetails as $key => $val){
        // dd($val);
        $po_number = '<br>PO No. & Date &nbsp;: '.$val['po_number'] .  ' - '.$val['po_date'];
       // $po_date   = '<br>PO Date : '.$val['po_date'];
        $counter ++;

        $content .= '<tr>             
                        <td style="text-align:center; width:5%;">' . $counter . '</td>
                        <td style="width:45%;text-align:left">' . $val['item_name'] .$po_number.'</td>
                         <td style="text-align:center; width:13%;">' . number_format((float)$val['po_qty'], 3, '.','') . '</td>
                        <td style="text-align:center; width:12%;">' . number_format((float)$val['grn_qty'], 3, '.','') . '</td>
                        <td style="text-align:center; width:10%;">'  . $val['unit_name'] . '</td>
                        <td style="text-align:center; width:15%;">'  . $val['remark'] . '</td>
                        </tr>';
                    }
                    // <td style="text-align:center; width:10%;">'  . number_format((float)$val['rate_per_unit'], 3, '.','')
                    //  . '</td>
                    // <td style="text-align:center; width:15%;">'  . $val['remarks'] . '</td>


        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="8" style="text-align:center; font-weight:bold; padding:6px;"><strong>GOODS RECEIPT NOTE</strong></th>
                </tr>
                <tr>
                    <td colspan="4">To, <br>$supplier_name$address$fulladdress</td>
                    <td colspan="4">$grn_no$grn_date$challan_no$challan_date</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>No.</b></th>
                    <th style="text-align:center; width:45%;"><b>Description</b></th>
                    <th style="text-align:center; width:13%;"><b>PO Qty.</b></th>
                    <th style="text-align:center; width:12%;"><b>GRN Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                    <th style="text-align:center; width:15%;"><b>Remark</b></th>
                    
                </tr>
            </thead>
            <tbody>
                $content
            </tbody>                               
        </table>
        EOD;
        // <th style="text-align:center; width:15%;"><b>Remark</b></th>
        // <th style="text-align:center; width:10%;"><b>Rate</b></th>
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
        $location  = getCurrentLocation();

        $header_print = $location->header_print != '' ? $location->header_print: '';

        $grnMaterial = $_SESSION['grnMaterial'];

        $transporter_name = $grnMaterial->transporter_name != '' ? $grnMaterial->transporter_name  : '';
        $vehicle_no = $grnMaterial->vehicle_no != '' ? $grnMaterial->vehicle_no  : '';
        $lr_no_date = $grnMaterial->lr_no_date != '' ? $grnMaterial->lr_no_date  : '';
        $special_notes = $grnMaterial->special_notes != '' ? $grnMaterial->special_notes  : '';

  
     
        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                <table cellspacing="0" cellpadding="2" border="1" id="footerTbl" nobr="true">
                    <tr>
                        <td style="width:20%;">Transporter</td>
                        <td style="width:20%;">$transporter_name</td> 
                        <td rowspan="3" colspan="6" style="width:60%;"> NOTE: $special_notes </td>
                    </tr>
                    <tr>
                        <th>Vehicle No.</th> 
                        <td>$vehicle_no </td>
                    </tr>
                    <tr>
                        <th>LR No. & Date</th> 
                        <td>$lr_no_date </td>
                    </tr>
                    <tr valign="top">
                        <td colspan="2" ></td>

                        <td colspan="3" style="text-align:center;"> Inspection By: <p style="border-top:1px solid black;">  </p></td>

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