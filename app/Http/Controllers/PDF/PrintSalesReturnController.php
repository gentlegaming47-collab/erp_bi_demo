<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SalesReturnController;
use Illuminate\Http\Request;
use TCPDF;


class PrintSalesReturnController extends Controller
{
   public function printSalesReturn($id = null,$merge = 'no'){
        

        $getSalesReturn = new SalesReturnController();
        $getSalesReturnRecord = $getSalesReturn->edit(base64_decode($id),true); 
        $jsonContent = $getSalesReturnRecord->getContent();

        $decodedData = json_decode($jsonContent, true);
        
         $sr_data = $decodedData['sr_data'];        
         $sr_part_details = $decodedData['sr_part_details'];
        
        $sr_customer = $sr_data['customer_name'] != "" ? $sr_data['customer_name'] : "";

         if($sr_data != ""){
            $_SESSION['sr_date'] = $sr_data;
        }


        $soNumber =  $sr_data['sr_number'] != '' &&  $sr_data['sr_number'] != null ? str_replace("/", "_", $sr_data['sr_number']) : "";
        if ($sr_data != null && $sr_customer != '') {
            $pdfName = 'Sales_Return_'.$soNumber.'_'.$sr_customer;
        } else {
            $pdfName = 'Sales_Return_'.$soNumber.'_Bhumi Polymers Pvt. Ltd.';
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

        // $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_RIGHT, false); // fot 1366 screen
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

        if ($sr_data['sr_number'] != "") {
            $sr_no = 'SR No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $sr_data['sr_number'] . '</b><br>';
        }else{
            $sr_no = '';
        }

        if ($sr_data['sr_date'] != "") {
            $sr_date = 'SR Date &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $sr_data['sr_date'] . '<br>';
        }else{
            $sr_date = '';
        }

      
        if ($sr_data['dp_number'] != "") {
            $dp_number = 'DP No. & Date&nbsp;:&nbsp;' . $sr_data['dp_number'] . ' - ' .$sr_data['dp_date']. '<br>';
        }else{
            $dp_number = '';
        }

        if ($sr_data['customer_name'] != "") {
            $customer = 'Customer &nbsp;:&nbsp;' . $sr_data['customer_name'] . '<br>';
        }else{
            $customer = '';
        }


        $_SESSION['sr_data'] =   $sr_data;
        
     
        $counter = 0;
        $content = '';
        $total_qty = 0;
        foreach($sr_part_details as $key => $val){

            if($val['secondary_item_name'] != null){
                $item = $val['secondary_item_name'];
                $qty = $val['sr_details_qty'];
            }else{
                $item = $val['item_name'];
                $qty = $val['sr_qty'];
            }

            $counter ++;
            $table_data = '';
            $total_qty += $val['sr_qty']; 
            $total_qty = number_format((float)$total_qty, 3, '.','');
          
            $table_data .= '<tr>';

            $content .= '<tr>             
                         <td style="text-align:center; width:5%;">' . $counter . '</td>
                          <td style="width:55%;text-align:left">'.$item.'</td>
                         <td style="text-align:center; width:10%;">' . $val['item_code'] . '</td>
                         <td style="text-align:center; width:12%;">' . number_format((float)$qty, 3, '.','') . '</td>
                         <td style="text-align:center; width:8%;">' . $val['unit_name'] . '</td>
                         <td style="width:10%; text-align:center">' . $val['remark'] . '</td>            
                        </tr>';
        }

        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                   
                    <th colspan="8" style="text-align:center; font-weight:bold; padding:6px; border-left:1px solid white; border-bottom:1px solid black; border-top:1px solid black;"><strong>SALES RETURN</strong></th>
                </tr>
                <tr>
                    <td colspan="4">$customer$dp_number</td>
                    <td colspan="4">$sr_no$sr_date</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                    <th style="text-align:center; width:55%;"><b>Description</b></th>
                    <th style="text-align:center; width:10%;"><b>Code</b></th>
                    <th style="text-align:center; width:12%;"><b>SR Qty.</b></th>
                    <th style="text-align:center; width:8%;"><b>Unit</b></th>
                    <th style="text-align:center; width:10%;"><b>Remark</b></th>
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
        if($merge == 'no'){
            $pdf->Output(urlencode($pdfName) . '.pdf', 'I');
         }else{
            
            $pdfOutput = $pdf->Output('', 'S');
            return $pdfOutput;
         }

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
            $height = 15 + $this->fHeight;
            $this->setY(-$height);
            //$this->setAutoPageBreak(TRUE, 80);
            $content = $content2;
        } else {
            $this->setY(-15);
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
        $sr_data   = $_SESSION['sr_data'];

        $location  = getCurrentLocation();

        $header_print = $location->header_print != '' ? $location->header_print: '';
        $transporter_name = $sr_data['transporter_name'] != '' ? $sr_data['transporter_name'] : '';
        $vehicle_no = $sr_data['vehicle_no'] != '' ? $sr_data['vehicle_no']  : '';
        $lr_no_date = $sr_data['lr_no_date'] != '' ? $sr_data['lr_no_date']  : '';
        $special_notes = $sr_data['sp_note'] != '' ? $sr_data['sp_note']  : '';

        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                <table cellspacing="0" cellpadding="2" border="1" id="footerTbl" nobr="true">
                <tr>
                    <td style="width:20%;">Transporter</td>
                    <td style="width:20%;">$transporter_name</td> 
                    <td rowspan="4" colspan="2" align="right" style="width:60%;">
                         For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b>
                         </span><br><br><br><br>
                        <span> Authorised Signatory&nbsp;&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <th>Vehicle No.</th> 
                    <td>$vehicle_no</td>
                </tr>
                <tr>
                    <th>LR No. & Date</th> 
                    <td>$lr_no_date</td>
                </tr>
                <tr>
                    <th>Sp. Note</th> 
                    <td>$special_notes</td>
                </tr>               
               
            </table>               
            EOD;
        return $tbl;
        $_SESSION = [];
    }

    


}