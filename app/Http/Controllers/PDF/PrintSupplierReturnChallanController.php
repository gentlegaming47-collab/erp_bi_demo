<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\SupplierRejectionController;
use TCPDF;
use App\Models\Supplier;
use App\Models\SupplierRejection;
use App\Models\SupplierRejectoionDetails;
use Illuminate\Support\Facades\Date;
//use Date;
use App\Models\Item;




class PrintSupplierReturnChallanController extends Controller
{
    public function printSupplierReturnChallan(Request $request, $id){

     
        $request->id = base64_decode($id);

        $src_data = SupplierRejection::select('supplier_rejection_challan.src_number','supplier_rejection_challan.src_date','supplier_rejection_challan.ref_no','ref_date','suppliers.supplier_name', 'transporters.transporter_name', 'supplier_rejection_challan.vehicle_no','supplier_rejection_challan.lr_no_date', 'supplier_rejection_challan.special_notes')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_rejection_challan.supplier_id')
        ->leftJoin('transporters', 'transporters.id', '=', 'supplier_rejection_challan.transporter_id')

        ->where('src_id','=',$request->id)->first();

            if($src_data->ref_date != "" && $src_data->ref_date != null)
            {
                $src_data->ref_date = Date::createFromFormat('Y-m-d', $src_data->ref_date)->format('d/m/Y');
            }
            if($src_data->src_date  != "" &&  $src_data->src_date  != null)
            {
                $src_data->src_date = Date::createFromFormat('Y-m-d', $src_data->src_date)->format('d/m/Y');
            }
        
            $src_details = SupplierRejectoionDetails::select(['units.unit_name', 'items.item_name', 'items.item_code', 'item_groups.item_group_name','supplier_rejection_challan_details.challan_qty','supplier_rejection_challan_details.remarks',
            ])
            ->leftJoin('items', 'items.id', 'supplier_rejection_challan_details.item_id')
            ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')
            ->leftJoin('units', 'units.id', 'items.unit_id')
            ->where('src_id','=',$request->id)->get();

         $supplier_name =  $src_data->supplier_name != '' ? $src_data->supplier_name : '';
         $supplier_name =  str_replace(" ","_",$supplier_name);

         $srcNumber =  $src_data->src_number != '' &&  $src_data->src_number != null ? str_replace("/", "_", $src_data->src_number) : "";

        if ($src_data != null && $supplier_name != '') {
            $pdfName = 'Supplier_Return_Challan_'.$srcNumber.'_'.$supplier_name;
        } else {
            $pdfName = 'Supplier_Return_Challan_'.$srcNumber.'_Bhumi Polymers Pvt. Ltd.';
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

        $sup_name =  $supplier_name;
        $sup_name =  str_replace("_"," ",$sup_name);
        
        if ($src_data->src_number != "") {
            $src_no = 'Challan No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $src_data->src_number . '</b><br>';
        }else{
            $src_no = '';
        }

        if ($src_data->src_date != "") {
            $src_date = 'Challan Date.&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $src_data->src_date . '';
        }else{
            $src_date = '';
        }

        if ($src_data->ref_no != "") {
            $ref_no = '<br>GRN. No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $src_data->ref_no;
        }else{
            $ref_no = '';
        }

        if ($src_data->ref_date != "") {
            $ref_date = '<br>GRN. Date.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $src_data->ref_date;

        }else{
            $ref_date = '';
        }

        if($src_data != ""){
            $_SESSION['src_data'] = $src_data;
        }
      

        $counter = 0;
        $content = '';
        $total_qty = 0; 
        foreach($src_details as $key => $val){

            $counter ++;
           
            $total_qty += $val['challan_qty']; 
            $total_qty = number_format((float)$total_qty, 3, '.','');

            $content .= '<tr>             
                         <td style="text-align:center; width:5%;">' . $counter . '</td>
                         <td style="text-align:left; width:50%;">' . $val['item_name']. '</td>
                         <td style="text-align:center; width:10%;">' . $val['item_code'] . '</td>
                         <td style="text-align:center; width:12%;">' . number_format((float)$val['challan_qty'], 3, '.','') . '</td>
                         <td style="text-align:center; width:10%;">' . $val['unit_name'] . '</td>
                         <td style="text-align:center; width:13%;">' . $val['remarks'] . '</td>          
                        </tr>';
        }

        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="7" style="text-align:center; font-weight:bold; padding:6px;"><strong>SUPPLIER RETURN CHALLAN</strong></th>
                </tr>
                <tr>
                    <td colspan="4"><b>$sup_name</b></td>
                    <td colspan="3">$src_no$src_date</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                    <th style="text-align:center; width:50%;"><b>Item</b></th>
                    <th style="text-align:center; width:10%;"><b>Code</b></th>
                    <th style="text-align:center; width:12%;"><b>Challan Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                    <th style="text-align:center; width:13%;"><b>Remark</b></th>
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

        $header = commonPdfHeader("SRC");

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

        $src_data = $_SESSION['src_data'];

        $transporter_name = $src_data->transporter_name != '' ? $src_data->transporter_name  : '';
        $vehicle_no = $src_data->vehicle_no != '' ? $src_data->vehicle_no  : '';
        $lr_no_date = $src_data->lr_no_date != '' ? $src_data->lr_no_date  : '';
        $special_notes = $src_data->special_notes != '' ? $src_data->special_notes  : '';
      
        $tbl = <<<EOD
            <table id="footerTbl" cellspacing="0" cellpadding="" border="1" nobr="true" >    
                    <tr>
                        <td style="width:15%;">Transporter</td>
                        <td style="width:25%;">&nbsp;&nbsp;$transporter_name</td> 
                        <td rowspan="4" colspan="3"  align="right" style="height:60px;">
                        For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b></span><br><br><br><br>
                        <span> Authorised Signatory&nbsp;&nbsp;</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Vehicle No.</th> 
                        <td>&nbsp;&nbsp;$vehicle_no </td>
                    </tr>
                    <tr>
                        <th>LR No. & Date</th> 
                        <td>&nbsp;&nbsp;$lr_no_date </td>
                    </tr>           
                    <tr>
                        <th>Sp. Note</th> 
                        <td>&nbsp;&nbsp;$special_notes </td>
                    </tr>           
            </table>

            EOD;


        return $tbl;


        $_SESSION = [];
    }
}