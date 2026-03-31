<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionDetails;
use App\Models\Supplier;
use App\Models\Location;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use TCPDF;

class PrintPurchaseRequisitionController extends Controller
{
    //
    public function printPurchaseRequisition(Request $request,$id){

        $location = getCurrentLocation();
        
        $pr_data = PurchaseRequisition::select('pr_number','pr_date','supplier_id','pr_form_id_fix','to_location_id')->where('pr_id', base64_decode($id))->first();

        if($pr_data->pr_date != "" && $pr_data->pr_date != null)
        {
            $pr_data->pr_date = Date::createFromFormat('Y-m-d', $pr_data->pr_date)->format('d/m/Y');
        }

        // $supplierName = PurchaseRequisitionDetails::select('suppliers.supplier_name')
        // ->leftJoin('suppliers','suppliers.id','=','purchase_requisition_details.supplier_id')    
        // ->where('purchase_requisition_details.pr_id',base64_decode($id))
        // ->groupBy('suppliers.id')
        // ->pluck('supplier_name')
        // ->implode(', ');

        if($pr_data->pr_form_id_fix == 2){

            $pr_details_data = PurchaseRequisitionDetails::select(
            'items.item_name', 'units.unit_name', 'purchase_requisition_details.req_qty','purchase_requisition_details.remarks','purchase_requisition_details.rate_per_unit','suppliers.supplier_name',
            DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE purchase_requisition_details.item_id = location_stock.item_id AND location_stock.location_id = $pr_data->to_location_id ) as stock_qty"),
            )
            ->leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_requisition_details.supplier_id')
            ->where('purchase_requisition_details.pr_id',base64_decode($id))
            ->get();

            $supplierName = Location::where('id',$pr_data->to_location_id)->value('location_name');

        }else{
            $pr_details_data = PurchaseRequisitionDetails::select(
            'items.item_name', 'units.unit_name', 'purchase_requisition_details.req_qty','purchase_requisition_details.remarks','purchase_requisition_details.rate_per_unit',
            DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE purchase_requisition_details.item_id = location_stock.item_id AND location_stock.location_id = $location->id ) as stock_qty"),
            )
            ->leftJoin('items', 'items.id', '=', 'purchase_requisition_details.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->where('purchase_requisition_details.pr_id',base64_decode($id))
            ->get();

            $supplierName = Supplier::where('id',$pr_data->supplier_id)->value('supplier_name');
        }



        $prNumber =  $pr_data->pr_number != '' &&  $pr_data->pr_number != null ? str_replace("/", "_", $pr_data->pr_number) : "";
        if ($pr_data != null && $pr_data->supplier_id > 0) {
            $pdfName = 'Purchase_Requisition_' . $prNumber .  '_' . str_replace(" ", "_", $supplierName);
        } else {
            $pdfName = 'Purchase_Requisition_' . $prNumber . '_Bhumi Polymers Pvt. Ltd.';
        }


        if ($pr_data->pr_number != "") {
            $pr_no = 'PR No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $pr_data->pr_number . '</b>';
        } else {
            $pr_no = '';
        }


        if ($pr_data->pr_date != "") {
            $pr_date = '<br>PR Date &nbsp;&nbsp;&nbsp;:&nbsp;' . $pr_data->pr_date;
        } else {
            $pr_date = '';
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

        $content = '';
        $table_head = '';
        $counter = 0;
        $to_from = '';

        if($pr_data->pr_form_id_fix == 2){
            $to_from = 'From';
            $table_head = '<tr>
                <th style="text-align:center; width:4%;"><b>Sr.No.</b></th>
                <th style="text-align:center; width:28%;"><b>Description</b></th>
                <th style="text-align:center; width:20%;"><b>Supplier</b></th>
                <th style="text-align:center; width:10%;"><b>Quantity</b></th>
                <th style="text-align:center; width:10%;"><b>Stock Qty.</b></th>
                <th style="text-align:center; width:8%;"><b>Unit</b></th>
                <th style="text-align:center; width:20%;"><b>Remark</b></th>
            </tr>';

            foreach ($pr_details_data as $key => $val) {
                $counter++;

                $content .= '<tr>             
                                <td style="text-align:center; width:4%;">' . $counter . '</td>
                                <td style="text-align:left; width:28%;">' . $val['item_name'] . '</td>
                                <td style="text-align:center; width:20%;">' . $val['supplier_name'] . '</td>
                                <td style="text-align:center; width:10%;">' . number_format((float)$val['req_qty'], 3, '.', '') . '</td>
                                <td style="text-align:center; width:10%;">' . number_format((float)$val['stock_qty'], 3, '.', '') . '</td>
                                <td style="text-align:center; width:8%;">'  . $val['unit_name'] . '</td>
                                <td style="text-align:center; width:20%;">'  . $val['remarks'] . '</td>
                            </tr>';
            }
        }else{
            $to_from = 'To';

             $table_head = '<tr>
                    <th style="text-align:center; width:4%;"><b>Sr.No.</b></th>
                    <th style="text-align:center; width:42%;"><b>Description</b></th>
                    <th style="text-align:center; width:12%;"><b>Quantity</b></th>
                    <th style="text-align:center; width:12%;"><b>Stock Qty.</b></th>
                    <th style="text-align:center; width:8%;"><b>Unit</b></th>
                    <th style="text-align:center; width:22%;"><b>Remark</b></th>
                </tr>';

            foreach ($pr_details_data as $key => $val) {
                $counter++;

                $content .= '<tr>             
                                <td style="text-align:center; width:4%;">' . $counter . '</td>
                                <td style="text-align:left; width:42%;">' . $val['item_name'] . '</td>
                                <td style="text-align:center; width:12%;">' . number_format((float)$val['req_qty'], 3, '.', '') . '</td>
                                <td style="text-align:center; width:12%;">' . number_format((float)$val['stock_qty'], 3, '.', '') . '</td>
                                <td style="text-align:center; width:8%;">'  . $val['unit_name'] . '</td>
                                <td style="text-align:center; width:22%;">'  . $val['remarks'] . '</td>
                            </tr>';
            }

        }


        

        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>
                <tr>
                    <th colspan="6" style="text-align:center; font-weight:bold; padding:6px;"><strong>PURCHASE REQUISITION</strong></th>
                </tr>
                <tr>
                    <td colspan="3">$to_from, <br>$supplierName</td>
                    <td colspan="3">$pr_no$pr_date</td>
                </tr>
               $table_head
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

        $header = commonPdfHeader("PO");     

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
        $location  = getCurrentLocation();
        $header_print     = $location->header_print;



        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                    <table id="footerTbl" cellspacing="0" cellpadding="2" border="1" nobr="true">
                        
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