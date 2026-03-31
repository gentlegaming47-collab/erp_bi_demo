<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\Village;
use App\Models\Taluka;
use App\Models\City;
use App\Models\State;
use App\Models\DispatchPlan;
use App\Models\MaterialRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\ItemProduction;
use App\Models\ItemIssue;
use App\Models\ItemReturn;
use App\Models\GRNVerification;
use App\Models\GRNMaterial;

class SuggestReportController extends Controller
{
    public function existscustomer(Request $request){
        if($request->term != ""){
            $fdCustomer = SalesOrder::select('customer_name')->where('customer_name', 'LIKE', $request->term.'%')->groupBy('customer_name')->get();
            // DD($fdCustomer);
            if($fdCustomer != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdCustomer as $dsKey){
                    $output .= '<li parent-id="customer_name" list-id="customer_name_list" class="list-group-item" tabindex="0">'.$dsKey->customer_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'customerList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Customer available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'customerList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsdpnumber(Request $request){
        if($request->term != ""){
            $fddispatch=DispatchPlan::select('dp_number')->where('dp_number', 'LIKE', $request->term.'%')->groupBy('dp_number')->get();
            // DD($fdCustomer);
            if($fddispatch != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fddispatch as $dsKey){
                    $output .= '<li parent-id="dp_number" list-id="dp_number_list" class="list-group-item" tabindex="0">'.$dsKey->dp_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'dpnumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Dispatch Plan available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'dpnumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existssonumber(Request $request){
        if($request->term != ""){
            $fdsonumber =SalesOrder::select('so_number')->where('so_number', 'LIKE', $request->term.'%')->groupBy('so_number')->get();
            // DD($fdCustomer);
            if($fdsonumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdsonumber as $dsKey){
                    $output .= '<li parent-id="so_number" list-id="so_number_list" class="list-group-item" tabindex="0">'.$dsKey->so_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'sonumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No SO Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'sonumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsmrnumber(Request $request){
        if($request->term != ""){
            $fdmrnumber =MaterialRequest::select('mr_number')->where('mr_number', 'LIKE', $request->term.'%')->groupBy('mr_number')->get();
            // DD($fdCustomer);
            if($fdmrnumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdmrnumber as $dsKey){
                    $output .= '<li parent-id="mr_number" list-id="mr_number_list" class="list-group-item" tabindex="0">'.$dsKey->mr_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'mrnumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No MR Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'mrnumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsponumber(Request $request){
        if($request->term != ""){
            $fdponumber =PurchaseOrder::select('po_number')->where('po_number', 'LIKE', $request->term.'%')->groupBy('po_number')->get();
            // DD($fdCustomer);
            if($fdponumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdponumber as $dsKey){
                    $output .= '<li parent-id="po_number" list-id="po_number_list" class="list-group-item" tabindex="0">'.$dsKey->po_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'ponumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No PO Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'ponumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsprnumber(Request $request){
        if($request->term != ""){
            $fdprnumber =PurchaseRequisition::select('pr_number')->where('pr_number', 'LIKE', $request->term.'%')->groupBy('pr_number')->get();
            if($fdprnumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdprnumber as $dsKey){
                    $output .= '<li parent-id="pr_number" list-id="pr_number_list" class="list-group-item" tabindex="0">'.$dsKey->pr_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'prnumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No PR Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'prnumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsorderby(Request $request){
        if($request->term != ""){
            $fdorderby =PurchaseOrder::select('order_by')->where('order_by', 'LIKE', $request->term.'%')->groupBy('order_by')->get();
            if($fdorderby != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdorderby as $dsKey){
                    $output .= '<li parent-id="order_by" list-id="order_by_list" class="list-group-item" tabindex="0">'.$dsKey->order_by.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'orderbyList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Order By available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'orderbyList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsipnumber(Request $request){
        if($request->term != ""){
            $fdipnumber =ItemProduction::select('ip_number')->where('ip_number', 'LIKE', $request->term.'%')->groupBy('ip_number')->get();
            if($fdipnumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdipnumber as $dsKey){
                    $output .= '<li parent-id="ip_number" list-id="ip_number_list" class="list-group-item" tabindex="0">'.$dsKey->ip_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'ipnumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No IP Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'ipnumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsissuenumber(Request $request){
        if($request->term != ""){
            $fdissueno =ItemIssue::select('issue_number')->where('issue_number', 'LIKE', $request->term.'%')->groupBy('issue_number')->get();
            if($fdissueno != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdissueno as $dsKey){
                    $output .= '<li parent-id="issue_number" list-id="issue_number_list" class="list-group-item" tabindex="0">'.$dsKey->issue_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'issuenumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Issue Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'issuenumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsreturnnumber(Request $request){
        if($request->term != ""){
            $fdreturnnumber =ItemReturn::select('return_number')->where('return_number', 'LIKE', $request->term.'%')->groupBy('return_number')->get();
            if($fdreturnnumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdreturnnumber as $dsKey){
                    $output .= '<li parent-id="return_number" list-id="return_number_list" class="list-group-item" tabindex="0">'.$dsKey->return_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'returnnumberList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Return Number available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'returnnumberList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function existsissueno(Request $request){
        if($request->term != ""){
            $fdissueno =ItemReturn::select('issue_no')->where('issue_no', 'LIKE', $request->term.'%')->groupBy('issue_no')->get();
            if($fdissueno != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdissueno as $dsKey){
                    $output .= '<li parent-id="issue_no" list-id="issue_no_list" class="list-group-item" tabindex="0">'.$dsKey->issue_no.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'issuenoList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No Issue No available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'issuenoList' => '',
                'response_code' => 1,
            ]);
        }

    }
    public function existsgrnno(Request $request){
        if($request->term != ""){
            $fdgrnnumber =GRNVerification::select('grn_verification.grn_details_id','material_receipt_grn_details.grn_id','grn_material_receipt.grn_number')
            ->leftJoin('material_receipt_grn_details','material_receipt_grn_details.grn_details_id' ,'grn_verification.grn_details_id')
            ->leftJoin('grn_material_receipt','grn_material_receipt.grn_id' ,'material_receipt_grn_details.grn_id')
            ->where('grn_number', 'LIKE', $request->term.'%')->groupBy('grn_number')->get();
            if($fdgrnnumber != null){
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdgrnnumber as $dsKey){
                    $output .= '<li parent-id="grn_no" list-id="grn_no_list" class="list-group-item" tabindex="0">'.$dsKey->grn_number.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'grnnoList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No GRN No available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'grnnoList' => '',
                'response_code' => 1,
            ]);
        }

    }


}
