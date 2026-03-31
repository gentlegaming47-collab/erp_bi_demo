<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use App\Models\Dealer;
use App\Models\DealerContacts;
use TCPDF;

class PrintQuotationController extends Controller
{
    public function printQuotation(Request $request,$id){

        $quotation = Quotation::select(['quotation.id','quotation.quot_sequence','quotation.quot_number','quotation.customer_name','quotation.quot_date', 'quotation.total_qty', 'quotation.total_amount', 'quotation.basic_amount','quotation.less_discount_percentage','quotation.less_discount_amount','quotation.secondary_transport','quotation.sgst_percentage','quotation.sgst_amount','quotation.cgst_percentage','quotation.cgst_amount','quotation.igst_percentage','quotation.igst_amount','quotation.net_amount','quotation.round_off_val','quotation.special_notes','quotation.mobile_no',      
        'customer_groups.customer_group_name', 'villages.village_name','talukas.taluka_name','districts.district_name','states.state_name','countries.country_name','dealers.dealer_name','mis_category.mis_category','quotation.pincode'
        ])

        ->leftJoin('customer_groups','customer_groups.id','=','quotation.customer_group_id')
        ->leftJoin('districts','districts.id','=','quotation.quot_district_id')
        ->leftJoin('states','states.id','=','districts.state_id')
        ->leftJoin('countries','countries.id','=','states.country_id')
        ->leftJoin('talukas','talukas.id','=','quotation.quot_taluka_id')
        ->leftJoin('villages','villages.id','=','quotation.quot_village_id')      
        ->leftJoin('dealers','dealers.id','=','quotation.dealer_id')
        ->leftJoin('mis_category','mis_category.id','=','quotation.mis_category_id')
        ->where('quotation.id',base64_decode($id))
        ->first();

        $quotation_details_data = QuotationDetails::select(
        'items.item_name', 'units.unit_name', 'quotation_details.quot_qty','quotation_details.rate_per_unit','items.item_code','item_groups.item_group_name','quotation_details.quot_amount',
        )
        ->leftJoin('items', 'items.id', '=', 'quotation_details.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units', 'units.id', '=', 'items.unit_id')
        ->where('quotation_details.quot_id',base64_decode($id))
        ->get();


         if($quotation->quot_date != "" && $quotation->quot_date != null)
        {
            $quotation->quot_date = Date::createFromFormat('Y-m-d', $quotation->quot_date)->format('d/m/Y');
        }


        if ($quotation->village_name != "") {
            $village = '<br>Village &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $quotation->village_name . '<br>';
        }
        else
        {
            $village = '';
        }

        if ($quotation->taluka_name != "") {
            $taluka = 'Taluka &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $quotation->taluka_name . '<br>';
        }else{
            $taluka = '';
        }

        if ($quotation->district_name != "") {
            $district = 'District &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $quotation->district_name . '<br>';
        }else{
            $district = '';
        }

        if ($quotation->state_name != "") {
            $state = 'State &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $quotation->state_name . '<br>';
        }else{
            $state = '';
        }

        if ($quotation->country_name != "") {
            $country = 'Country &nbsp;&nbsp;&nbsp;: ' . $quotation->country_name;
        }else{
            $country = '';
        }

        if ($quotation->pincode != "") {
            $customer_pincode = 'Pin Code &nbsp;:&nbsp;' . $quotation->pincode . '<br>';
        }else{
            $customer_pincode = 'Pin Code &nbsp;:<br>';
        }

        $quotationNumber =  $quotation->quot_number != '' &&  $quotation->quot_number != null ? str_replace("/", "_", $quotation->quot_number) : "";
        if ($quotation != null && $quotation->customer_name != '') {
            $pdfName = 'Quotation_'.$quotationNumber.'_'.$quotation->customer_name;
        } else {
            $pdfName = 'Quotation_'.'_Bhumi Polymers Pvt. Ltd.';
        }


         
        $dealer = Dealer::select('dealer_name','mobile_no','gst_code')->where('dealer_name',$quotation->dealer_name)->first();
        // dd($quotation);
        $dealer_contact = DealerContacts::select('contact_mobile_no')
        ->leftJoin('dealers', 'dealers.id', '=', 'dealer_contacts.dealer_id')
        ->where('dealer_name', $quotation->dealer_name)
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

        
    


        $_SESSION['quotation'] =  $quotation;

        
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


        if($quotation->quot_number != "") {
            $quo_no = 'Quot. No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<b>' . $quotation->quot_number . '</b><br>';
        }else{
            $quo_no = '';
        }

        if ($quotation->quot_date != "") {
            $quo_date = 'Quot. Date.&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;' . $quotation->quot_date . '<br>';
        }else{
            $quo_date = '';
        }
        if ($quotation->mis_category != "") {
          $mis_category = $quotation->mis_category;
        }else{
            $mis_category = '';
        }

         $quo_customer = $quotation->customer_name  != "" ? $quotation->customer_name : "";


        $content = '';
        $counter = 0;
         $total_qty = 0;
        $total_amount = 0;


        foreach ($quotation_details_data as $key => $val) {

           $total_qty += $val['quot_qty']; 
           $total_qty = number_format((float)$total_qty, 3, '.','');

           $total_amount += $val['quot_amount'];
           $total_amount = number_format((float)$total_amount, 2, '.','');

           $counter++;

           $content .= '<tr>             
                         <td style="text-align:center; width:5%;">' . $counter . '</td>
                          <td style="width:38%; text-align:left">'.$val['item_name'].'</td>
                         <td style="text-align:center; width:10%;">' . $val['item_code'] . '</td>
                         <td style="text-align:center; width:10%;">' . $val['item_group_name'] . '</td>
                         <td style="text-align:center; width:12%;">' . number_format((float)$val['quot_qty'], 3, '.','') . '</td>
                         <td style="text-align:center; width:6%;">' . $val['unit_name'] . '</td>
                         <td style="text-align:center; width:9%;">' . number_format((float)$val['rate_per_unit'], 2, '.','') . '</td>
                         <td style="text-align:right; width:10%;">' . number_format((float)$val['quot_amount'], 2, '.','') . '</td>            
                        </tr>';
        }


         $tbl = <<<EOD
        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
            <thead>            
                <tr>
                    <th colspan="3" style="text-align:left;  padding:6px; border-right:1px solid white; border-top:1px solid black; border-bottom:1px solid black;">MIS Type : $mis_category</th>
                    <th colspan="5" style="text-align:left; font-weight:bold; padding:6px; border-left:1px solid white; border-bottom:1px solid black; border-top:1px solid black;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;QUOTATION</strong></th>
                </tr>
                <tr>
                    <td colspan="4">Detail Of Farmer <br>Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $quo_customer$village$customer_pincode$taluka$district$state$country</td>
                    <td colspan="4">$quo_no$quo_date</td>
                </tr>
                <tr>
                    <th style="text-align:center; width:5%;"><b>Sr. No.</b></th>
                    <th style="text-align:center; width:38%;"><b>Item</b></th>
                    <th style="text-align:center; width:10%;"><b>Code</b></th>
                    <th style="text-align:center; width:10%;"><b>Group</b></th>
                    <th style="text-align:center; width:12%;"><b>Qty.</b></th>
                    <th style="text-align:center; width:6%;"><b>Unit</b></th>
                    <th style="text-align:center; width:9%;"><b>Rate/Unit</b></th>
                    <th style="text-align:center; width:10%;"><b>Amount</b></th>
                </tr>
            </thead>
            <tbody>
            $content
                <tr><td colspan="4" style="text-align:right;"><b>TOTAL</b></td>
                    <td style="text-align:center;"><b>$total_qty</b></td>
                    <td colspan="2" style="text-align:right;"></td>
                    <td style="text-align:right;"><b>$total_amount</b></td>
                </tr>
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
        $quotation         = $_SESSION['quotation'];
        $dealer          = $_SESSION['dealer_name'];
        $gst_code          = $_SESSION['gst_code'] != '' ? ' , GSTIN : '.$_SESSION['gst_code'] : ' , GSTIN :';
        // $dealer_mobile   = $_SESSION['dealer_mobile'] ? $_SESSION['dealer_mobile'].', ' : "";
        $dealer_mobile   = $_SESSION['dealer_mobile'] ? $_SESSION['dealer_contact'] != '' ? $_SESSION['dealer_mobile'].', ' : $_SESSION['dealer_mobile'] : "";
        $dealer_contacts = $_SESSION['dealer_contact'];
        $farmer_mobile   = $_SESSION['quotation']['mobile_no'] ? $_SESSION['quotation']['mobile_no'] : "";



        // dd($so_data);

        if($quotation['basic_amount'] != ''){
            $basic_amt  = '<tr>
                                <td width="45%">Basic Amount</td>
                                <td width="18%"></td>
                                <td width="37%" style="text-align:right;">'. number_format((float)$quotation['basic_amount'], 2, '.','').'</td>
                            </tr>';
        }else{
            $basic_amt  = '<tr>
                                <td width="45%">Basic Amount</td>
                                <td width="18%"></td>
                                <td width="37%" style="text-align:right;">'. number_format((float)$quotation['total_amount'], 2, '.','').'</td>
                            </tr>';
        }

        if($quotation['less_discount_amount'] != ''){
            $less_pr = $quotation['less_discount_percentage'] != '' ? number_format((float)$quotation['less_discount_percentage'], 2, '.','').'%' : '';
            $less_discount_amt  = '<tr>
                                <td width="45%">Less Discount</td>
                                <td width="18%">'.$less_pr.'</td>
                                <td width="37%" style="text-align:right;">'.  number_format((float)$quotation['less_discount_amount'], 2, '.','').'</td>
                            </tr>';
        }else{
            $less_discount_amt  = '';
        }
        
        if($quotation['secondary_transport'] != ''){
            $secondary_transport   = '<tr>
                                        <td width="45%">Secondary Transport</td>
                                        <td width="18%"></td>
                                        <td width="37%" style="text-align:right;">'.   number_format((float)$quotation['secondary_transport'], 2, '.','').'</td>
                                    </tr>';
        }else{
            $secondary_transport   = '';
        }       
       

        if( $quotation['sgst_amount'] != ''){
            $sgst_amount  = '<tr>
                                <td width="45%">SGST</td>
                                <td width="18%">'.  number_format((float)$quotation['sgst_percentage'], 2, '.','').'%</td>
                                <td width="37%" style="text-align:right;">'.  number_format((float)$quotation['sgst_amount'], 2, '.','').'</td>
                            </tr>';
        }else{
            $sgst_amount  = '';
        }

        
        if($quotation['cgst_amount'] != ''){
            $cgst_amount  = '<tr>
                                <td width="45%">CGST</td>
                                <td width="18%">'.  number_format((float)$quotation['cgst_percentage'], 2, '.','').'%</td>
                                <td width="37%" style="text-align:right;">'. number_format((float)$quotation['cgst_amount'], 2, '.','').'</td>
                            </tr>';
        }else{
            $cgst_amount  = '';
        }

       
        if($quotation['igst_amount'] != ''){
            $igst_amount  = '<tr>
                                <td width="45%">IGST</td>
                                <td width="18%">'. number_format((float)$quotation['igst_percentage'], 2, '.','') .'%</td>
                                <td width="37%" style="text-align:right;">'. number_format((float)$quotation['igst_amount'], 2, '.','').'</td>
                            </tr>';
        }else{
            $igst_amount  = '';
        }
        if($quotation['round_off_val'] != ''){
            $round_off  = '<tr>
                                <td width="45%">Round Off</td>
                                <td width="18%"></td>
                                <td width="37%" style="text-align:right;">'. number_format((float)$quotation['round_off_val'], 2, '.','').'</td>
                            </tr>';
        }else{
            $round_off  = '';
        }
        
        if($secondary_transport =='' && $sgst_amount =='' && $cgst_amount =='' && $igst_amount ==''){          
            $extra_tr = '<tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3">&nbsp;</td></tr>';
        }else{           
            $extra_tr = '';
        }
        
        $net_amt         =  $quotation->net_amount != '' ?  number_format((float)$quotation->net_amount, 2, '.','') :  number_format((float)$quotation->total_amount, 2, '.','');

        $table = '<table  cellspacing="0"  cellpadding="1">                        
                        '.$basic_amt.$less_discount_amt.$secondary_transport.$sgst_amount.$cgst_amount.$igst_amount.$round_off.$extra_tr.'       
                         <tr> 
                            <td width="45%"style="border-top:1px solid black;"><b>Net Amount</b></td>
                            <td width="18%" style="border-top:1px solid black;" ></td> 
                            <td width="37%" style="border-top:1px solid black; text-align:right;"><b>'.$net_amt.'</b></td>
                         </tr> 
                  </table>';
            
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
                                <td>Sp. Note : '.$quotation['special_notes'].'  </td>
                            </tr>
                        </table>';
        $tbl = <<<EOD

            <table id="footerTbl" cellspacing="0" cellpadding="0" border="1" nobr="true" >               
                <tr>
                    <td>$dealer_table</td>
                    <td>$table</td>
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