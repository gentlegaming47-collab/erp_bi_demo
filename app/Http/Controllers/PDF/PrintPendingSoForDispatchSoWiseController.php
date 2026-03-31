<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\DispatchPlanController;
use App\Models\ItemRawMaterialMappingDetail;
use App\Models\Location;
use App\Models\MisCategory;
use App\Models\Village;
use App\Models\Dealer;
use App\Models\DealerContacts;
use TCPDF;


class PrintPendingSoForDispatchSoWiseController extends Controller
{
      public function printSalesOrder($id = null,$merge = 'no'){
        

        $getSalesOrder = new SaleOrderController();
        $getSalesOrderRecord = $getSalesOrder->edit(base64_decode($id),true); 
        $jsonContent = $getSalesOrderRecord->getContent();

        $decodedData = json_decode($jsonContent, true);
        
        $so_data = $decodedData['so_data']; 


        $request = new Request(['chkSOId' => $so_data['id']]);

        $getPendingSODetailData = new DispatchPlanController();
        $getSalesOrderDetailRecord = $getPendingSODetailData->getSODetailData($request); 
         $jsonDetailContent = $getSalesOrderDetailRecord->getContent();
         
         
         $decodedDetailData = json_decode($jsonDetailContent, true);
         $so_part_details = $decodedDetailData['so_data'];
      


         if($so_data['so_from_id_fix'] == '1' || $so_data['so_from_id_fix'] == '2'){
            $so_customer = $so_data['customer_name'] != "" ? $so_data['customer_name'] : "";
            $area = $so_data['area'] != "" ? $so_data['area'] : "";
            $mis_category = MisCategory::select('mis_category')->where('id',$so_data['mis_category_id'])->pluck('mis_category')->first();
            
            $farmer_details = Village::select('villages.village_name','talukas.taluka_name','districts.district_name','states.state_name','countries.country_name')
            ->leftJoin('sales_order', 'sales_order.customer_village', '=', 'villages.id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->leftJoin('countries', 'countries.id', '=', 'states.country_id')
            ->where('villages.id',$so_data['customer_village'])
            ->first();
            // if ($area != "") {
            //     $area = '<br>Area &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $area . '<br>';
            // }else{
            //     $area = '';
            // }
            if ($farmer_details->village_name != "") {
                $village = '<br>Village &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $farmer_details->village_name . '<br>';
            }
            else
            {
                $village = '';
            }

            if ($farmer_details->taluka_name != "") {
                $taluka = 'Taluka &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $farmer_details->taluka_name . '<br>';
            }else{
                $taluka = '';
            }

            if ($farmer_details->district_name != "") {
                $district = 'District &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $farmer_details->district_name . '<br>';
            }else{
                $district = '';
            }

            if ($farmer_details->state_name != "") {
                $state = 'State &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $farmer_details->state_name . '<br>';
            }else{
                $state = '';
            }

            if ($farmer_details->country_name != "") {
                $country = 'Country &nbsp;&nbsp;&nbsp;: ' . $farmer_details->country_name;
            }else{
                $country = '';
            }

            if ($so_data['customer_pincode'] != "") {
                $customer_pincode = 'Pin Code &nbsp;:&nbsp;' . $so_data['customer_pincode'] . '<br>';
            }else{
                $customer_pincode = 'Pin Code &nbsp;:<br>';
            }
    


           

         }else{
            $location = Location::select('location_name','village_id')->where('id',$so_data['to_location_id'])->first();
            $so_customer = $location->location_name;
            $area = $so_data['area'] != "" ? $so_data['area'] : "";
            $location_details = Location::select('villages.village_name','talukas.taluka_name','districts.district_name','states.state_name','countries.country_name','villages.default_pincode')
            ->leftJoin('villages', 'villages.id', '=', 'locations.village_id')
            ->leftJoin('talukas', 'talukas.id', '=', 'villages.taluka_id')
            ->leftJoin('districts', 'districts.id', '=', 'talukas.district_id')
            ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->leftJoin('countries', 'countries.id', '=', 'states.country_id')
            ->where('villages.id',$location->village_id)
            ->first();
            // if ($area != "") {
            //     $area = '<br>Area &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $area . '<br>';
            // }else{
            //     $area = '';
            // }
            if ($location_details->village_name != "") {
                $village = '<br>Village &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $location_details->village_name . '<br>';
            }
            else
            {
                $village = '';
            }

            if ($location_details->taluka_name != "") {
                $taluka = 'Taluka &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $location_details->taluka_name . '<br>';
            }else{
                $taluka = '';
            }

            if ($location_details->district_name != "") {
                $district = 'District &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $location_details->district_name . '<br>';
            }else{
                $district = '';
            }

            if ($location_details->state_name != "") {
                $state = 'State &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $location_details->state_name . '<br>';
            }else{
                $state = '';
            }

            if ($location_details->country_name != "") {
                $country = 'Country &nbsp;&nbsp;&nbsp;: ' . $location_details->country_name;
            }else{
                $country = '';
            }

            if ($location_details->default_pincode != "") {
                $customer_pincode = 'Pin Code &nbsp;:&nbsp;' . $location_details->default_pincode . '<br>';
            }else{
                $customer_pincode = 'Pin Code &nbsp;:<br>';
            }

            $mis_category = "";

         }

         $soNumber =  $so_data['so_number'] != '' &&  $so_data['so_number'] != null ? str_replace("/", "_", $so_data['so_number']) : "";
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

       /* require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $fontFile = base_path('vendor/tecnickcom/tcpdf/fonts/notosansgujarati.ttf');

        if (!file_exists($fontFile)) {
            die("Font file not found!");
        }

        $fontName = \TCPDF_FONTS::addTTFfont($fontFile, 'TrueTypeUnicode', '', 96);

        echo "Font added: " . $fontName;*/


        
        if ($so_data['so_number'] != "") {
            $so_no = 'SO No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $so_data['so_number'] . '</b><br>';
        }else{
            $so_no = '';
        }

        if ($so_data['so_date'] != "") {
            $so_date = 'SO Date.&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $so_data['so_date'] . '<br>';
        }else{
            $so_date = '';
        }

        if ($so_data['customer_reg_no'] != "") {
            $customer_reg_no = 'Reg. No. &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'. $so_data['customer_reg_no'] . '';
        }else{
            $customer_reg_no = 'Reg. No. &nbsp;&nbsp;&nbsp;&nbsp;:';
        }

        $area = $so_data['area'] != "" ? $so_data['area'] : "";
        if ($so_data['area'] != "") {
            $area = '<br>Area &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $area . '';
        }else{
            $area = '';
        }

        $ship_to = $so_data['ship_to'] != "" ? $so_data['ship_to'] : "";
        if ($so_data['ship_to'] != "") {
            $ship_to = '<br>Ship To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $ship_to . '';
        }else{
            $ship_to = '';
        }
     
        $dealer = Dealer::select('dealer_name','mobile_no','gst_code')->where('id',$so_data['dealer_id'])->first();
        $dealer_contact = DealerContacts::select('contact_mobile_no')
        ->where('dealer_id', $so_data['dealer_id'])
        ->get()
        ->pluck('contact_mobile_no') // Extract only 'contact_mobile_no' values
        ->toArray(); // Convert collection to array
    
        if($dealer != ""){
            $_SESSION['dealer_name'] = $dealer->dealer_name;
            $_SESSION['gst_code'] = $dealer->gst_code;
            $_SESSION['dealer_mobile'] = $dealer->mobile_no;
            $_SESSION['dealer_contact'] = implode(', ', $dealer_contact); // Convert array to comma-separated string
        }else{
            $_SESSION['dealer_name'] = "";
            $_SESSION['gst_code'] = "";
            $_SESSION['dealer_mobile'] = "";
            $_SESSION['dealer_contact'] = "";
        }

        $_SESSION['so_data'] =   $so_data;
        
     
        $counter = 0;
        $content = '';
        $total_qty = 0;
        $total_amount = 0;

        foreach($so_part_details as $key => $val){

            // new logic details items

             $rawItemsHtml = '';
            if ($val['show_item_in_print'] == 'Yes') {
                        $rawMaterials = ItemRawMaterialMappingDetail::leftJoin('items as raw_items', 'raw_items.id', '=', 'item_raw_material_mapping_details.raw_material_id')
                        ->where('item_raw_material_mapping_details.item_id', $val['item_id'])
                        ->select('raw_items.item_name', 'item_raw_material_mapping_details.raw_material_qty')
                        ->get();

                        if ($rawMaterials->count() > 0) {
                            foreach ($rawMaterials as $rawItem) {
                                $rawItemsHtml .= '<br> <b>•</b> ' . $rawItem->item_name . ' - <b>' . number_format((float)$rawItem->raw_material_qty , 3, '.','') . '</b>';
                            }
                        }
            }

            // end 
            $counter ++;

            $content .= '<tr>             
                         <td style="text-align:center; width:5%;">' . $counter . '</td>
                          <td style="width:50%;">'.$val['item_name'].' '.$rawItemsHtml.'</td>
                         <td style="text-align:center; width:10%;">' . $val['item_code'] . '</td>
                         <td style="text-align:center; width:10%;">' . number_format((float)$val['pend_so_qty'], 3, '.','') . '</td>
                         <td style="text-align:center; width:10%;">'  . $val['unitName'] . '</td>
                         <td style="width:15%;">' . $val['remarks'] . '</td>        
                         </tr>';                     
        }

        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="3" style="text-align:left;  padding:6px; border-right:1px solid white; border-top:1px solid black; border-bottom:1px solid black;">MIS Type : $mis_category</th>
                    <th colspan="5" style="text-align:left; font-weight:bold; padding:6px; border-left:1px solid white; border-bottom:1px solid black; border-top:1px solid black;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SALES ORDER</strong></th>
                </tr>
                <tr>
                    <td colspan="4">Detail Of Farmer <br>Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $so_customer$village$customer_pincode$taluka$district$state$country</td>
                    <td colspan="4">$so_no$so_date$customer_reg_no$area$ship_to</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                    <th style="text-align:center; width:50%;"><b>Item</b></th>
                    <th style="text-align:center; width:10%;"><b>Code</b></th>
                    <th style="text-align:center; width:10%;"><b>Pend. SO Qty.</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                    <th style="text-align:center; width:15%;"><b>Remark</b></th>
                   
                </tr>
            </thead>
            <tbody>
                $content
              
            </tbody>                               
        </table>
        EOD;
//  <th style="text-align:center; width:6%;"><b>Unit</b></th>
//                     <th style="text-align:center; width:9%;"><b>Rate/Unit</b></th>
//                     <th style="text-align:center; width:10%;"><b>Amount</b></th>
//                    
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
        $so_data         = $_SESSION['so_data'];
        $dealer          = $_SESSION['dealer_name'];
        $gst_code          = $_SESSION['gst_code'] != '' ? ' , GSTIN : '.$_SESSION['gst_code'] : ' , GSTIN :';
        // $dealer_mobile   = $_SESSION['dealer_mobile'] ? $_SESSION['dealer_mobile'].', ' : "";
        $dealer_mobile   = $_SESSION['dealer_mobile'] ? $_SESSION['dealer_contact'] != '' ? $_SESSION['dealer_mobile'].', ' : $_SESSION['dealer_mobile'] : "";
        $dealer_contacts = $_SESSION['dealer_contact'];
        $farmer_mobile   = $_SESSION['so_data']['mobile_no'] ? $_SESSION['so_data']['mobile_no'] : "";

        $dealer_table = '<table  cellspacing="0"  cellpadding="1"> 
            <tr>
                <td>Dealer : '.$dealer.$gst_code.'  </td>
            </tr>
            <tr>
                <td style="font-family: notosansgujarati;">Mobile No. : '.$dealer_mobile .' '. $dealer_contacts.'   </td>
            </tr>
            <tr>
                <td style="font-family: notosansgujarati;">Farmer Phone No. : '.$farmer_mobile.'  </td>
            </tr>
            <tr>
                <td>Sp. Note : '.$so_data['special_notes'].'  </td>
            </tr>
        </table>';

        $tbl = <<<EOD

            <table id="footerTbl" cellspacing="0" cellpadding="0" border="1" nobr="true" >               
                <tr>
                    <td>$dealer_table</td>                  
                </tr>
                <tr valign="top">
                    <td colspan="2"  align="right" style="height:60px;">
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