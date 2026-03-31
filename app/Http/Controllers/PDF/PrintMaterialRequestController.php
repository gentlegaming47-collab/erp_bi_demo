<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\ItemRawMaterialMappingDetail;
use Date;
use TCPDF;
use Illuminate\Support\Facades\DB;


class PrintMaterialRequestController extends Controller
{
    public function printMaterialRequest($id){

        $id = base64_decode($id);

        $materialRequest = MaterialRequest::select('material_request.mr_id','material_request.mr_number','material_request.mr_date','locations.location_name','material_request.special_notes','material_request.current_location_id','material_request.customer_group_id','customer_groups.customer_group_name')
        ->leftJoin('customer_groups','customer_groups.id', 'material_request.customer_group_id')
        ->leftJoin('locations','locations.id','=','material_request.to_location_id')
        ->where('mr_id', $id)->first();



        
        $materialRequest->mr_date = Date::createFromFormat('Y-m-d', $materialRequest->mr_date)->format('d/m/Y');

        $materialRequestDetails = MaterialRequestDetail::select(['material_request_details.mr_details_id','material_request_details.mr_id', 'material_request_details.mr_qty','material_request_details.item_id','items.item_code', 'units.unit_name', 'item_groups.item_group_name','material_request_details.remarks','items.item_name', 'items.show_item_in_print',

         DB::raw("(SELECT IFNULL(SUM(location_stock.stock_qty),0) FROM location_stock WHERE material_request_details.item_id = location_stock.item_id AND location_stock.location_id = $materialRequest->current_location_id ) as stock_qty"),
        ])
        ->leftJoin('items', 'items.id', 'material_request_details.item_id')       
        ->leftJoin('item_groups', 'item_groups.id', 'items.item_group_id')       
        ->leftJoin('units', 'units.id', 'items.unit_id')        
        ->where('material_request_details.mr_id','=',$id)->get();

        $mr_customer_group =   $materialRequest->customer_group_name != '' ? $materialRequest->customer_group_name : '';

        $mr_number =  $materialRequest->mr_number != '' &&  $materialRequest->mr_number != null ? str_replace("/", "_", $materialRequest->mr_number) : "";

        $pdfName = 'Material_Request_'.$mr_number.'_Bhumi_Polymers_Pvt_Ltd';

        // if ($mr_number != null && $mr_customer_group != '') {
        //     $pdfName = 'Material_Request_'.$mr_number.'_'.$mr_customer_group;
        // } else {
        //     $pdfName = 'Material_Request_'.$mr_number.'_Bhumi Polymers Pvt. Ltd.';
        // }
        


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


        if ($materialRequest->mr_number != "") {
            $mr_no = 'MR No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $materialRequest->mr_number . '</b><br>';
        }else{
            $mr_no = '';
        }

        if ($materialRequest->mr_date != "") {          
            $mr_date = 'MR Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $materialRequest->mr_date . '<br>';
        }else{
            $mr_date = '';
        }  
        if ($materialRequest->customer_group_name != "") {          
            $customer_group_name = 'Customer Group&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $materialRequest->customer_group_name . '';
        }else{
            $customer_group_name = '';
        } 

        $_SESSION['mr_data'] =   $materialRequest;
        $counter = 0;
        $table_data = '';
        $total_qty = 0;

        foreach($materialRequestDetails as $key=>$val){
            $counter++;
            $total_qty += $val['mr_qty'];
                      $rawItemsHtml = '';
                    if ($val['show_item_in_print'] == 'Yes') {
                        // $rawMaterials = ItemRawMaterialMappingDetail::leftjoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                        //                 ->where('item_raw_material_mapping_details.item_id', $val['item_id'])
                        //                 ->pluck('raw_items.item_name');
                         $rawMaterials = ItemRawMaterialMappingDetail::select('raw_items.item_name', 'item_raw_material_mapping_details.raw_material_qty')
                        ->leftJoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                        ->where('item_raw_material_mapping_details.item_id', $val['item_id'])
                        ->get();

                        if ($rawMaterials->count() > 0) {
                         
                            // foreach ($rawMaterials as $rawItem) {
                            //     $rawItemsHtml .= '<br> <b>•</b> '. $rawItem ;
                            // }
                             foreach ($rawMaterials as $rawItem) {
                            $rawItemsHtml .= '<br> <b>•</b> ' . $rawItem->item_name . ' -<b> ' . number_format((float)$rawItem->raw_material_qty , 3, '.','') . '</b>';
                        }
                           
                        }
                    }
                    $table_data .= '<tr>
            <td style="text-align:center; width:5%;">'. $counter.'</td>
            <td style="width:40%; text-align:left">'.$val['item_name'].' '.$rawItemsHtml.'</td>
            <td style="text-align:center; width:10%;">'.$val['item_code'].'</td>
            <td style="text-align:center; width:10%;">'. number_format((float)$val['mr_qty'], 3, '.','').'</td>
            <td style="text-align:center; width:10%;">'.$val['unit_name'].'</td>
            <td style="text-align:center; width:10%;">'.number_format((float)$val['stock_qty'],3,'.','').'</td>
            <td style="text-align:center; width:15%;">'.$val['remarks'].'</td>
            </tr>';
        }
        $total_qty = number_format((float)$total_qty, 3, '.','');
        $tbl = <<<EOD
            <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
                <thead>            
                    <tr>
                        <th colspan="7" style="text-align:center; font-weight:bold; padding:6px;"><strong>MATERIAL REQUEST</strong></th>
                    </tr>
                    <tr>
                        <td colspan="4">To,<br>$materialRequest->location_name</td>
                        <td colspan="3">$mr_no$mr_date$customer_group_name</td>
                    </tr>
                    <tr>
                        <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                        <th style="text-align:center; width:40%;"><b>Item</b></th>
                        <th style="text-align:center; width:10%;"><b>Code</b></th>
                        <th style="text-align:center; width:10%;"><b>MR Qty.</b></th>
                        <th style="text-align:center; width:10%;"><b>Unit</b></th>                    
                        <th style="text-align:center; width:10%;"><b>Stock Qty.</b></th>                    
                        <th style="text-align:center; width:15%;"><b>Remarks</b></th>                    
                    </tr>
                </thead>
                <tbody>
                 $table_data 
              
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

        $mr_data   = $_SESSION['mr_data'];

        $sp_note = $mr_data['special_notes'];


        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                    <table id="footerTbl" cellspacing="0" cellpadding="2" border="1" nobr="true">
                            <tr valign="top">
                                <td>Sp. Note : $sp_note </td>
                                <td  align="right" style="height:60px;">
                                For,<span> <b>Bhumi Polymers Pvt. Ltd.&nbsp;&nbsp;</b></span><br><br><br><br>
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