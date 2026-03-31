<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationStock;
use DataTables;
use TCPDF;


class ReportController extends Controller
{
    public function manage()
    {
        return view('manage.manage-report');
    }

    public function index(LocationStock $location_stock,Request $request,DataTables $dataTables)
    { 
        $Location = getCurrentLocation();


        $location_stock = LocationStock::select(['location_stock.ls_id','location_stock.stock_qty','locations.location_name','items.item_name','items.item_code','items.min_stock_qty','items.max_stock_qty','items.re_order_qty','item_groups.item_group_name','units.unit_name'])
        ->leftJoin('locations','locations.id','=','location_stock.location_id')
        ->leftJoin('items','items.id','=','location_stock.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
       ->where('location_stock.location_id','=',$Location->id);
        if($request->min_max =='max_stock'){
         $location_stock->where('items.max_stock_qty','>',0);
        }
        else if($request->min_max =='min_stock'){
              $location_stock->where('items.min_stock_qty','>',0);
        }

        return DataTables::of($location_stock)
        ->editColumn('item_name', function($location_stock){ 
            if($location_stock->item_name != ''){
                $item_name = ucfirst($location_stock->item_name);
                return $item_name;
            }else{
                return '';
            }
        })
          ->editColumn('stock_qty', function($location_stock){
            if($location_stock->stock_qty != null || $location_stock->stock_qty){
                $stockQty = number_format((float)$location_stock->stock_qty, 3, '.','');
                
                return isset($stockQty)?$stockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
        ->editColumn('min_stock_qty', function($location_stock){
            if($location_stock->min_stock_qty != null || $location_stock->min_stock_qty){
                $minStockQty = number_format((float)$location_stock->min_stock_qty, 3, '.','');
                
                return isset($minStockQty)?$minStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
        ->editColumn('max_stock_qty', function($location_stock){
            if($location_stock->max_stock_qty != null || $location_stock->max_stock_qty){
                $maxStockQty = number_format((float)$location_stock->max_stock_qty, 3, '.','');
                
                return isset($maxStockQty)?$maxStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })
        ->editColumn('re_order_qty', function($location_stock){
            if($location_stock->re_order_qty != null || $location_stock->re_order_qty){
                $reOrderStockQty = number_format((float)$location_stock->re_order_qty, 3, '.','');
                
                return isset($reOrderStockQty)?$reOrderStockQty :'';
            }else{
                return number_format((float)0, 3, '.','');
            }
        })

        ->make(true);
    }

    public function printItemStockReport()
    {

       $Location = getCurrentLocation();

       $pdfName = str_replace(" ", "_",$Location->location_name).'_Stock_Report_Bhumi_Polymers_Pvt._Ltd.';
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

        $Location = getCurrentLocation();

        $location_stock = LocationStock::select(['location_stock.ls_id','location_stock.stock_qty','locations.location_name','items.item_name','items.item_code','items.min_stock_qty','items.max_stock_qty','items.re_order_qty','item_groups.item_group_name','units.unit_name'])
        ->leftJoin('locations','locations.id','=','location_stock.location_id')
        ->leftJoin('items','items.id','=','location_stock.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->leftJoin('units','units.id','=','items.unit_id')
        ->where('location_stock.location_id','=',$Location->id)
        ->where('location_stock.stock_qty','>',0)
        ->get();

       $item_group = LocationStock::select(['item_groups.item_group_name'])
        ->leftJoin('items','items.id','=','location_stock.item_id')
        ->leftJoin('item_groups','item_groups.id','=','items.item_group_id')
        ->where('location_stock.location_id','=',$Location->id)
        ->where('location_stock.stock_qty','>',0)
        ->groupBy('item_group_name')
        ->get();

       $outputArray = [];
       foreach ($location_stock as $key => $value) {
           $item_group =  $value['item_group_name'];
           if (!isset($outputArray[$item_group])) {
               $outputArray[$item_group] = [];
           }
           $outputArray[$item_group][$key] = $value;
       }

       $content = '';
       $counter = 0;
       foreach($outputArray as $key => $val){

           $content .= '<tr>
           <td colspan="7"><u><b>'.$key.'</u></b> </td>
           </tr>';

           foreach ($val as $key => $value) {
                $counter ++;
                    $content .= '<tr>
                                    <td style="width:4%;">'.$counter.' </td>
                                    <td style="width:46%;">'.$value['item_name'].' </td>
                                    <td style="width:10%; text-align:center;">'.$value['unit_name'].' </td>
                                    <td style="width:10%; text-align:center;">'.number_format((float)$value['stock_qty'], 3, '.','').' </td>
                                    <td style="width:10%; text-align:center;">'.(empty($value['min_stock_qty']) && $value['min_stock_qty'] !== '0' ? '' : number_format((float)$value['min_stock_qty'], 3, '.','')) .' </td>
                                    <td style="width:10%; text-align:center;">'.(empty($value['max_stock_qty']) && $value['max_stock_qty'] !== '0' ? '' : number_format((float)$value['max_stock_qty'], 3, '.','')) .' </td>
                                    <td style="width:10%; text-align:center;">'.(empty($value['re_order_qty']) && $value['re_order_qty'] !== '0' ? '' : number_format((float)$value['re_order_qty'], 3, '.','')) .' </td>
                                </tr>';
            }
        }

        if($Location->header_print != ""){
                $_SESSION['header_print'] = $Location->header_print;
        }else{
            $_SESSION['header_print'] = '';
        }
        $tbl = <<<EOD

        <table cellspacing="0" cellpadding="3" border="1"  style="border-top:none; text-aling:center; font-size:11px; width:100%;">
           <thead>
                <tr>    
                    <td colspan="7" style="text-align:center; border-top:none;"><b>$Location->location_name </b></td>
                </tr>
                <tr>
                    <th style="text-align:center; width:4%;"><b>Sr.No.</b></th>
                    <th style="text-align:center; width:46%;"><b>Item Name</b></th>
                    <th style="text-align:center; width:10%;"><b>Unit</b></th>
                    <th style="text-align:center; width:10%;"><b>Stock</b></th>
                    <th style="text-align:center; width:10%;"><b>Min. Stock</b></th>
                    <th style="text-align:center; width:10%;"><b>Max. Stock</b></th>
                    <th style="text-align:center; width:10%;"><b>Re-Order</b></th>
                </tr>
           </thead>
            <tbody>
                $content
            </tbody>                               
        </table>
        EOD;

       $pdf->writeHTML($tbl, true, false, false, false, '');

       $content = $pdf->customFooter();
       $js = <<<EOD
                       var footerlen=document.getElementById("footerTbl").length;
                       app.alert(footerlen);
                   EOD;
       $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 0, false, true, "L", true);
       $lastH = $pdf->getLastH();
      
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
            $height = 10 + $this->fHeight;
            $this->setY(-$height);
            //$this->setAutoPageBreak(TRUE, 80);
            $content = $content2;
        } else {
            $this->setY(-10);
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
        $header_print     = $_SESSION['header_print'];

        $this->setFont('helvetica', '', 8);

        $tbl = <<<EOD
                
                <table cellspacing="0" cellpadding="2" border="1" id="footerTbl" nobr="true">
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