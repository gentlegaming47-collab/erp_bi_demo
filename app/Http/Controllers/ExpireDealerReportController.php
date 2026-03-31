<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\DealerAgreement;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpireDealerReportController extends Controller
{
    public function manage()
    {
        return view('manage.manage-expire_dealer_report');
    }

    public function index(Dealer $dealer,Request $request,DataTables $dataTables)
    {

        $yearIds = getCompanyYearIdsToTill();
        $location = getCurrentLocation();
        
        $FifteenDayAgo = Carbon::now()->subDays(15)->format('Y-m-d');
        $FifteenDayAfter = Carbon::now()->addDays(15)->format('Y-m-d');
       
    //     $dealer = Dealer::select('dealer_name','aggrement_start_date','aggrement_end_date')
    
    //    ->where([
    //         ['aggrement_start_date', '!=', null],
    //         ['aggrement_end_date', '!=', null],
    //         ['aggrement_end_date', '<=',$FifteenDayAfter],
    //       //  ['aggrement_end_date', '>=',$FifteenDayAgo]
    //    ]);

        $dealer = DealerAgreement::select('dealers.dealer_name', 'agreement_start_date', 
        DB::raw('MAX(dealer_agreement.agreement_end_date) as agreement_end_date'))
        
        ->leftJoin('dealers', 'dealers.id', '=', 'dealer_agreement.dealer_id')
        ->whereNotNull('agreement_start_date')
        ->whereNotNull('agreement_end_date')
        ->having('agreement_end_date', '<=', $FifteenDayAfter) 
        ->groupBy('dealer_agreement.dealer_id'); 
          

        return DataTables::of($dealer)

        ->editColumn('agreement_start_date', function($dealer){           
            if ($dealer->agreement_start_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $dealer->agreement_start_date)->format('d/m/Y'); 
                return $formatedDate1;
            }else{
                return '';
            }
        })
       

        ->editColumn('agreement_end_date', function($dealer){           
            if ($dealer->agreement_end_date != null) {
                $formatedDate1 = Date::createFromFormat('Y-m-d', $dealer->agreement_end_date)->format('d/m/Y'); 
                return $formatedDate1;
            }else{
                return '';
            }
        })

        ->filterColumn('dealer_agreement.agreement_start_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dealer_agreement.agreement_start_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->filterColumn('dealer_agreement.agreement_end_date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(dealer_agreement.agreement_end_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->rawColumns(['agreement_start_date', 'agreement_end_date'])
        ->make(true);
    }

}