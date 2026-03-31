<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SupplierRejectionController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\ConstantController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyYearController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HsnCodeController;
use App\Http\Controllers\TalukaController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\ItemGroupController;
use App\Http\Controllers\RawMaterialGroupController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ItemRawMaterialMappingController;
use App\Http\Controllers\DuplicationVerificationController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\POShortCloseController;
use App\Http\Controllers\GRNMaterialController;
use App\Http\Controllers\ItemIssueController;
use App\Http\Controllers\ItemReturnController;
use App\Http\Controllers\ItemProductionController;
use App\Http\Controllers\ItemAssemblyProductionController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\TruckWiseItemListController;
use App\Http\Controllers\CustomerReturnController;
use App\Http\Controllers\MaterialRequirmentController;
use App\Http\Controllers\CRDisionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\DispatchPlanController;
use App\Http\Controllers\LoadingEntryController;
use App\Http\Controllers\SOShortCloseController;
use App\Http\Controllers\CustomerReplacementEntryController;
use App\Http\Controllers\SOmappingController;
use App\Http\Controllers\ReplacementItemDecisionController;
use App\Http\Controllers\LocationCustomerGroupMappingController;
use App\Http\Controllers\SupplierItemMappingController;
use App\Http\Controllers\POApprovalController;
use App\Http\Controllers\GrnAgainstPOApprovalController;
use App\Http\Controllers\ApprovalReportController;
use App\Http\Controllers\PoVsExcessGrnController;
use App\Http\Controllers\ExpireDealerReportController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MisCategoryController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\PRShortCloseController;
use App\Http\Controllers\QCApprovalController;
use App\Http\Controllers\ApprovalstatusController;
use App\Http\Controllers\ItemLedgerReportController;
use App\Http\Controllers\PDF\PrintSalesOrderController;
use App\Http\Controllers\PDF\PrintPurchaseOrderController;
use App\Http\Controllers\PDF\PrintDispatchPlanController;
use App\Http\Controllers\PDF\PrintGRNController;
use App\Http\Controllers\PDF\PrintFarmerDispatchPlanController;
use App\Http\Controllers\PDF\PrintSupplierReturnChallanController;
use App\Http\Controllers\PDF\PrintSalesOrderFittingController;
use App\Http\Controllers\PDF\PrintMaterialRequestController;
use App\Http\Controllers\PDF\PrintFarmerWiseTotalDispatchPlanController;
use App\Http\Controllers\PDF\PrintLoadingEntryController;
use App\Http\Controllers\PDF\PrintPurchaseRequisitionController;
use App\Http\Controllers\PDF\PrintPendingSoForDispatchSoWiseController;
use App\Http\Controllers\PDF\PrintGRNLocationController;
use App\Http\Controllers\PDF\Report\PrintReportsController;
use App\Http\Controllers\PDF\PrintQuotationController;
use App\Http\Controllers\PDF\PrintSalesReturnController;
use App\Http\Controllers\PurchaseRequisitionSummaryController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CrystalReportController;
use App\Http\Controllers\SalesOrderSummaryController;
use App\Http\Controllers\DispatchPlanSummaryController;
use App\Http\Controllers\PendingSOforDispatchController;
use App\Http\Controllers\LoadingSummaryController;
use App\Http\Controllers\PendingDispatchPlanforLoadingEntryController;
use App\Http\Controllers\SuggestReportController;
use App\Http\Controllers\MaterialRequestSummaryController;
use App\Http\Controllers\PurchaseOrderSummaryController;
use App\Http\Controllers\ItemProductionSummaryController;
use App\Http\Controllers\ItemIssueSummaryController;
use App\Http\Controllers\ItemReturnSlipSummaryController;
use App\Http\Controllers\DealerStatusUpdateUtilityController;
use App\Http\Controllers\GRNVerificationController;
use App\Http\Controllers\ItemDetailsReportController;
use App\Http\Controllers\ItemStockTransferController;
use App\Http\Controllers\TransactionSOShortCloseController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\PrintSalesOrderReportController;
use App\Http\Controllers\PendingSoForDispatchSoWiseController;
use App\Http\Controllers\PendingPurchaseRequisitionForLocationController;
use App\Http\Controllers\GRNVerificationSummaryController;
use App\Http\Controllers\QCApprovalSummaryController;
use App\Http\Controllers\SupplierReturnChallanSummaryController;
use App\Http\Controllers\PendingMaterialRequestStatusController;
use App\Http\Controllers\SalesReturnSummaryController;
use App\Http\Controllers\PendingGRNForQCVerificationController;
use App\Http\Controllers\GRNSummaryController;
use App\Http\Controllers\GRNSummaryLocationController;
use App\Models\GRNVerification;
use App\Models\ItemStockTransfer;
use App\Http\Controllers\PendingSOForLocationController;

Auth::routes();

Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::post('/login',[AuthController::class,'login'])->name('login');
// Route::get('/selectYear',[AuthController::class,'selectYear'])->name('selectYear');
// Route::get('/selectLocation',[AuthController::class,'selectYear'])->name('selectLocation');
 Route::get('/selectLocation',[AuthController::class,'selectYear'])->name('selectLocation')->middleware('auth.admin_access');
// Admin Route
// Route::middleware(['auth', 'auth.admin_access'])->group(function(){




    Route::post('/update-user',[AdminController::class,'update'])->name('update-user');
    Route::get('/username-list',[AdminController::class,'existsUsername'])->name('username-list');


    Route::get('/designation-list',[AdminController::class,'existsDesignation'])->name('designation-list');

    Route::post('/change-password',[AdminController::class,'changePassword'])->name('change-password');

    Route::get('/get-companies',[AdminController::class,'userData'])->name('get-companies');
// });






Route::middleware(['auth','auth.user_access'])->group( function () {

Route::get('/linkstorage', function () {
  dd(Artisan::call('storage:link'));
});

    Route::get('/dashboard',[AuthController::class,'dashboard'])->name('dashboard');

    Route::get('/',[AuthController::class,'dashboard'])->name('/');

       /**
     * admin routes
     */

     Route::get('/get-companies',[AdminController::class,'userData'])->name('get-companies');
     Route::post('/add-user',[AdminController::class,'store'])->name('add-user');
     Route::get('/add-user',[AdminController::class,'create'])->name('add-user');

      Route::get('/users',[AdminController::class,'index'])->name('users');
     Route::get('/delete-user',[AdminController::class,'destroy'])->name('remove-user');
    Route::get('/manage-user',[AdminController::class,'manage'])->name('manage-user');

     Route::get('/get-user/{id}',[AdminController::class,'edit'])->name('get-user');
     Route::post('/edit-user',[AdminController::class,'update'])->name('edit-user');
     Route::get('/get-users',[AdminController::class,'userData'])->name('get-users');
     Route::get('/edit-user/{id}',[AdminController::class,'show'])->name('edit-user');
     Route::Post('/edit-user/{id}',[AdminController::class,'show'])->name('edit-user');

     Route::post('/listing-user',[AdminController::class,'index'])->name('listing-user');
     Route::post('/store-user',[AdminController::class,'store'])->name('store-user');


     //end admin route


    /**
     * profile routes
     */
    Route::get('/manage-company_year',[CompanyYearController::class,'manage'])->name('manage-company_year');

    Route::post('/check-login',[AdminController::class,'check'])->name('check-login');

    Route::get('/get-user_access',[UserAccessController::class,'getUserAccess'])->name('get-user_access');


    Route::post('/update-user_access',[UserAccessController::class,'setUserAccess'])->name('update-user_access');
    Route::get('/get-pages',[UserAccessController::class,'getPages'])->name('get-pages');
    Route::get('/get-actions',[UserAccessController::class,'getActions'])->name('get-actions');
    Route::get('/get-access_modules',[UserAccessController::class,'getAccessModules'])->name('get-access_modules');

    /**
     * Manage Routes
     */



    Route::get('/manage-company',[CompanyController::class,'manage'])->name('manage-company');
    Route::get('/manage-country',[CountryController::class,'manage'])->name('manage-country');
    Route::get('/manage-village',[VillageController::class,'manage'])->name('manage-village');
    Route::get('/manage-item',[ItemController::class,'manage'])->name('manage-item');
    Route::get('/manage-raw-material',[RawMaterialController::class,'manage'])->name('manage-raw_material');
    Route::get('/manage-transporter',[TransporterController::class,'manage'])->name('manage-transporter');
    Route::get('/manage-item_group',[ItemGroupController::class,'manage'])->name('manage-item_group');
    Route::get('/manage-raw-material-group',[RawMaterialGroupController::class,'manage'])->name('manage-raw_material_group');
    Route::get('/manage-customer_group',[CustomerGroupController::class,'manage'])->name('manage-customer_group');
    Route::get('/manage-state',[StateController::class,'manage'])->name('manage-state');
    Route::get('/manage-district',[CityController::class,'manage'])->name('manage-district');
    Route::get('/manage-customer',[CustomerController::class,'manage'])->name('manage-customer');
    //Route::get('/manage-company_year',[CompanyYearController::class,'manage'])->name('manage-company_year');
    Route::get('/manage-hsn_code',[HsnCodeController::class,'manage'])->name('manage-hsn_code');
    Route::get('/manage-supplier',[SupplierController::class,'manage'])->name('manage-supplier');
    Route::get('/manage-taluka',[TalukaController::class,'manage'])->name('manage-taluka');
    Route::get('/manage-location',[LocationController::class,'manage'])->name('manage-location');
    Route::get('/manage-unit',[UnitController::class,'manage'])->name('manage-unit');
    Route::get('/manage-item_item_mapping',[ItemRawMaterialMappingController::class,'manage'])->name('manage-item_raw_material_mapping');
    Route::get('/manage-sales_order',[SaleOrderController::class,'manage'])->name('manage-sales_order');
    Route::get('/manage-price_list',[PriceListController::class,'manage'])->name('manage-price_list');
    Route::get('/manage-purchase_order',[PurchaseOrderController::class,'manage'])->name('manage-purchase_order');
    Route::get('/manage-po_short_close',[POShortCloseController::class,'manage'])->name('manage-po_short_close');
    Route::get('/manage-grn_details',[GRNMaterialController::class,'manage'])->name('manage-grn_details');
    Route::get('/manage-grn_location',[GRNMaterialController::class,'manage'])->name('manage-grn_location');
    Route::get('/manage-supplier_rej_challan',[SupplierRejectionController::class,'manage'])->name('manage-supplier_rej_challan');
    Route::get('/manage-item_issue',[ItemIssueController::class,'manage'])->name('manage-item_issue');
    Route::get('/manage-item_return',[ItemReturnController::class,'manage'])->name('manage-item_return');
    Route::get('/manage-item_production',[ItemProductionController::class,'manage'])->name('manage-item_production');
    Route::get('/manage-item_assm_production',[ItemAssemblyProductionController::class,'manage'])->name('manage-item_assm_production');
    Route::get('/manage-material_request',[MaterialRequestController::class,'manage'])->name('manage-material_request');
    Route::get('/manage-delivery_challan',[DeliveryChallanController::class,'manage'])->name('manage-delivery_challan');
    Route::get('/manage-truck_wise_item',[TruckWiseItemListController::class,'manage'])->name('manage-truck_wise_item');
    Route::get('/manage-customer_return',[CustomerReturnController::class,'manage'])->name('manage-customer_return');
    Route::get('/manage-cr_decision',[CRDisionController::class,'manage'])->name('manage-cr_decision');
    Route::get('/manage-material_requirement',[MaterialRequirmentController::class,'manage'])->name('manage-material_requirement');
    Route::get('/manage-report',[ReportController::class,'manage'])->name('manage-report');
    Route::get('/manage-item_details_report',[ItemDetailsReportController::class,'manage'])->name('manage-item_details_report');
    Route::get('/manage-sales_return',[SalesReturnController::class,'manage'])->name('manage-sales_return');

    Route::get('/manage-sm_approval', [ApprovalController::class, 'manage'])->name('manage-sm_approval');
    Route::get('/manage-state_coordinator_approval', [ApprovalController::class, 'manage'])->name('manage-state_coordinator_approval');
    Route::get('/manage-zsm_approval', [ApprovalController::class, 'manage'])->name('manage-zsm_approval');
    Route::get('/manage-gm_approval', [ApprovalController::class, 'manage'])->name('manage-gm_approval');
    // Route::get('/manage-md_approval', [ApprovalController::class, 'manage'])->name('manage-md_approval');
    Route::get('/manage-dealer', [DealerController::class, 'manage'])->name('manage-dealer');
    Route::get('/manage-dispatch_plan', [DispatchPlanController::class, 'manage'])->name('manage-dispatch_plan');
    Route::get('/manage-loading_entry', [LoadingEntryController::class, 'manage'])->name('manage-loading_entry');
    Route::get('/manage-pending_dispatch_plan_for_loading_entry', [PendingDispatchPlanforLoadingEntryController::class, 'manage'])->name('manage-pending_dispatch_plan_for_loading_entry');

    Route::get('/manage-so_short_close',[SOShortCloseController::class,'manage'])->name('manage-so_short_close');

    Route::get('/manage-transaction_so_short_close',[TransactionSOShortCloseController::class,'manage'])->name('manage-transaction_so_short_close');

    Route::get('/manage-customer_replacement_entry', [CustomerReplacementEntryController::class, 'manage'])->name('manage-customer_replacement_entry');

    Route::get('/manage-so_mapping', [SOmappingController::class, 'manage'])->name('manage-so_mapping');

    Route::get('/manage-replacement_item_decision', [ReplacementItemDecisionController::class, 'manage'])->name('manage-replacement_item_decision');

    Route::get('/manage-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'manage'])->name('manage-location_customer_group_mappning');

    Route::get('/manage-supplier_item_mapping', [SupplierItemMappingController::class, 'manage'])->name('manage-supplier_item_mapping');

    Route::get('/manage-po_approval', [POApprovalController::class, 'manage'])->name('manage-po_approval');
    Route::get('/manage-grn_against_po_approval', [GrnAgainstPOApprovalController::class, 'manage'])->name('manage-grn_against_po_approval');
    Route::get('/manage-po_vs_excess_grn', [PoVsExcessGrnController::class, 'manage'])->name('manage-po_vs_excess_grn');
    Route::get('/manage-purchase_requisition_summary',[PurchaseRequisitionSummaryController::class,'manage'])->name('manage-purchase_requisition_summary');
    Route::get('/manage-sales_order_summary',[SalesOrderSummaryController::class,'manage'])->name('manage-sales_order_summary');
    Route::get('/manage-dispatch_plan_summary',[DispatchPlanSummaryController::class,'manage'])->name('manage-dispatch_plan_summary');
    Route::get('/manage-loading_summary',[LoadingSummaryController::class,'manage'])->name('manage-loading_summary');
    Route::get('/manage-material_request_summary',[MaterialRequestSummaryController::class,'manage'])->name('manage-material_request_summary');
    Route::get('/manage-purchase_order_summary',[PurchaseOrderSummaryController::class,'manage'])->name('manage-purchase_order_summary');
    Route::get('/manage-item_production_summary',[ItemProductionSummaryController::class,'manage'])->name('manage-item_production_summary');
    Route::get('/manage-item_issue_summary',[ItemIssueSummaryController::class,'manage'])->name('manage-item_issue_summary');
    Route::get('/manage-item_return_slip_summary',[ItemReturnSlipSummaryController::class,'manage'])->name('manage-item_return_slip_summary');
    Route::get('/manage-qc_approval_summary',[QCApprovalSummaryController::class,'manage'])->name('manage-qc_approval_summary');
    Route::get('/manage-supplier_return_challan_summary',[SupplierReturnChallanSummaryController::class,'manage'])->name('manage-supplier_return_challan_summary');
    Route::get('/manage-print_sales_order',[PrintSalesOrderReportController::class,'manage'])->name('manage-print_sales_order');

    Route::get('/sm_approval_report', [ApprovalReportController::class, 'manage'])->name('manage-sm_approval_report');
    Route::get('/state_coordinator_approval_report', [ApprovalReportController::class, 'manage'])->name('manage-state_coordinator_approval_report');
    Route::get('/zsm_approval_report', [ApprovalReportController::class, 'manage'])->name('manage-zsm_approval_report');
    Route::get('/gm_approval_report', [ApprovalReportController::class, 'manage'])->name('manage-gm_approval_report');
    // Route::get('/md_approval_report', [ApprovalReportController::class, 'manage'])->name('manage-md_approval_report');


    Route::get('/manage-expire_dealer_report', [ExpireDealerReportController::class, 'manage'])->name('manage-expire_dealer_report');
    Route::get('/manage-mis_category', [MisCategoryController::class, 'manage'])->name('manage-mis_category');

    Route::get('/manage-pending_po_list', [POApprovalController::class, 'managePendingPo'])->name('manage-pending_po_list');
    Route::get('/manage-purchase_requisition', [PurchaseRequisitionController::class, 'manage'])->name('manage-purchase_requisition');
    Route::get('/manage-purchase_requisition_short_close', [PRShortCloseController::class, 'manage'])->name('manage-purchase_requisition_short_close');

    Route::get('/manage-qc_approval', [QCApprovalController::class, 'manage'])->name('manage-qc_approval');

    // old route
    // Route::get('/manage-pending_so_for_dispatch', [DispatchPlanController::class, 'managePendingSo'])->name('manage-pending_so_for_dispatch');
    // new changes
    Route::get('/manage-pending_so_for_dispatch', [PendingSOforDispatchController::class, 'manage'])->name('manage-pending_so_for_dispatch');
    Route::get('/manage-pending_so_for_dispatch_so_wise', [PendingSoForDispatchSoWiseController::class, 'manage'])->name('manage-pending_so_for_dispatch_so_wise');

    Route::get('/manage-pending_dispatch_plan_for_loading', [LoadingEntryController::class, 'managePendingDispatch'])->name('manage-pending_dispatch_plan_for_loading');

    Route::get('/manage-pending_grn_for_location_dispatch', [GRNMaterialController::class, 'managePendingGrn'])->name('manage-pending_grn_for_location_dispatch');

    Route::get('/manage-pending_material_request_for_so', [SaleOrderController::class, 'managePendingMr'])->name('manage-pending_material_request_for_so');

    Route::get('/manage-approval_status',[ApprovalstatusController::class,'manage'])->name('manage-approval_status');
    Route::get('/manage-item_ledger_report',[ItemLedgerReportController::class,'manage'])->name('manage-item_ledger_report');

    Route::get('/manage-pending_pr_for_po', [PurchaseRequisitionController::class, 'managePendingPO'])->name('manage-pending_pr_for_po');
    Route::get('/manage-pending_purchase_requisition_for_location', [PendingPurchaseRequisitionForLocationController::class, 'manage'])->name('manage-pending_purchase_requisition_for_location');
    Route::get('/manage-quotation', [QuotationController::class, 'manage'])->name('manage-quotation');
    Route::get('/manage-pending_so_for_location', [PendingSOForLocationController::class, 'manage'])->name('manage-pending_so_for_location');
    
    Route::get('/manage-dealer_status_update_utility', [DealerStatusUpdateUtilityController::class, 'manage'])->name('manage-dealer_status_update_utility');
    Route::get('/manage-grn_verification', [GRNVerificationController::class, 'manage'])->name('manage-grn_verification');
    Route::get('/manage-item_stock_transfer', [ItemStockTransferController::class, 'manage'])->name('manage-item_stock_transfer');
    Route::get('/manage-grn_verification_summary', [GRNVerificationSummaryController::class, 'manage'])->name('manage-grn_verification_summary');
    Route::get('/manage-sales_return_summary', [SalesReturnSummaryController::class, 'manage'])->name('manage-sales_return_summary');
    Route::get('/manage-pending_grn_for_qc_verification', [PendingGRNForQCVerificationController::class, 'manage'])->name('manage-pending_grn_for_qc_verification');
    Route::get('/manage-grn_summary', [GRNSummaryController::class, 'manage'])->name('manage-grn_summary');
    Route::get('/manage-grn_summary_location', [GRNSummaryLocationController::class, 'manage'])->name('manage-grn_summary_location');

    Route::get('/manage-pending_material_request_status', [PendingMaterialRequestStatusController::class, 'manage'])->name('manage-pending_material_request_status');





     /**
     * Data Listing Routes
     */

    Route::post('/listing-price_list',[PriceListController::class,'index'])->name('listing-price_list');
    Route::post('/listing-company',[CompanyController::class,'index'])->name('listing-company');
    Route::post('/listing-country',[CountryController::class,'index'])->name('listing-country');
    Route::post('/listing-village',[VillageController::class,'index'])->name('listing-village');
    Route::post('/listing-item',[ItemController::class,'index'])->name('listing-item');
    Route::post('/listing-raw-material',[RawMaterialController::class,'index'])->name('listing-raw-material');
    Route::post('/listing-transporter',[TransporterController::class,'index'])->name('listing-transporter');
    Route::post('/listing-item-group',[ItemGroupController::class,'index'])->name('listing-item-group');
    Route::post('/listing-raw-material-group',[RawMaterialGroupController::class,'index'])->name('listing-raw-material-group');
    Route::post('/listing-customer_group',[CustomerGroupController::class,'index'])->name('listing-customer_group');
    Route::post('/listing-state',[StateController::class,'index'])->name('listing-state');
    Route::post('/listing-district',[CityController::class,'index'])->name('listing-district');
    Route::post('/listing-customer',[CustomerController::class,'index'])->name('listing-customer');
    Route::post('/listing-company_year',[CompanyYearController::class,'index'])->name('listing-company_year');
    Route::post('/listing-hsn_code',[HsnCodeController::class,'index'])->name('listing-hsn_code');
    Route::post('/listing-supplier',[SupplierController::class,'index'])->name('listing-supplier');
    Route::post('/listing-taluka',[TalukaController::class,'index'])->name('listing-taluka');
    Route::post('/listing-location',[LocationController::class,'index'])->name('listing-location');
    Route::post('/listing-unit',[UnitController::class,'index'])->name('listing-unit');
    Route::post('/listing-item_item_mapping',[ItemRawMaterialMappingController::class,'index'])->name('listing-item-raw-matrial-mapping');
    Route::post('/listing-sales_order',[SaleOrderController::class,'index'])->name('listing-sales_order');
    Route::post('/listing-purchase_order',[PurchaseOrderController::class,'index'])->name('listing-purchase_order');
    Route::post('/listing-po_short_close',[POShortCloseController::class,'index'])->name('listing-po_short_close');
    Route::post('/listing-grn_details',[GRNMaterialController::class,'index'])->name('listing-grn_details');
    Route::post('/listing-supplier_rej_challan',[SupplierRejectionController::class,'index'])->name('listing-supplier_rej_challan');
    Route::post('/listing-item_issue',[ItemIssueController::class,'index'])->name('listing-item_issue');
    Route::post('/listing-item_return',[ItemReturnController::class,'index'])->name('listing-item_return');
    Route::post('/listing-item_production',[ItemProductionController::class,'index'])->name('listing-item_production');
    Route::post('/listing-item_assm_production',[ItemAssemblyProductionController::class,'index'])->name('listing-item_assm_production');
    Route::post('/listing-material_request',[MaterialRequestController::class,'index'])->name('listing-material_request');
    Route::post('/listing-delivery_challan',[DeliveryChallanController::class,'index'])->name('listing-delivery_challan');
    Route::post('/listing-truck_wise_item',[TruckWiseItemListController::class,'index'])->name('listing-truck_wise_item');
    Route::post('/listing-customer_return',[CustomerReturnController::class,'index'])->name('listing-customer_return');
    Route::post('/listing-cr_decision',[CRDisionController::class,'index'])->name('listing-cr_decision');
    Route::post('/listing-approval_request',[ApprovalController::class,'show'])->name('listing-approval_request');
     Route::post('/listing-material_requirement',[MaterialRequirmentController::class,'index'])->name('listing-material_requirement');
     Route::post('/listing-report',[ReportController::class,'index'])->name('listing-report');
     Route::post('/listing-item_details_report',[ItemDetailsReportController::class,'index'])->name('listing-item_details_report');
     Route::post('/listing-dealer',[DealerController::class,'index'])->name('listing-dealer');
     Route::post('/listing-dispatch_plan',[DispatchPlanController::class,'index'])->name('listing-dispatch_plan');
     Route::post('/listing-loading_entry',[LoadingEntryController::class,'index'])->name('listing-loading_entry');
    Route::post('/listing-so_short_close',[SOShortCloseController::class,'index'])->name('listing-so_short_close');
    Route::post('/listing-transaction_so_short_close',[TransactionSOShortCloseController::class,'index'])->name('listing-transaction_so_short_close');
    Route::post('/listing-customer_replacement_entry',[CustomerReplacementEntryController::class,'index'])->name('listing-customer_replacement_entry');
    Route::post('/listing-so_mapping',[SOmappingController::class,'index'])->name('listing-so_mapping');
    Route::post('/listing-replacement_item_decision',[ReplacementItemDecisionController::class,'index'])->name('listing-replacement_item_decision');
    Route::post('/listing-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'index'])->name('listing-location_customer_group_mappning');
    Route::post('/listing-supplier_item_mapping',[SupplierItemMappingController::class,'index'])->name('listing-supplier_item_mapping');
    Route::post('/listing-po_approval_request',[POApprovalController::class,'index'])->name('listing-po_approval_request');
    Route::post('/listing-po_approval_grn_request',[GrnAgainstPOApprovalController::class,'index'])->name('listing-po_approval_grn_request');
    Route::post('/listing-approval',[ApprovalController::class,'index'])->name('listing-approval');
    Route::post('/listing-approval_report',[ApprovalReportController::class,'index'])->name('listing-approval_report');
    Route::post('/listing-po_approval',[POApprovalController::class,'indexforManage'])->name('listing-po_approval');
    Route::post('/listing-po_vs_excess_grn',[PoVsExcessGrnController::class,'index'])->name('listing-po_vs_excess_grn');
    Route::post('/listing-expire_dealer_report',[ExpireDealerReportController::class,'index'])->name('listing-expire_dealer_report');
    Route::post('/listing-mis_category',[MisCategoryController::class,'index'])->name('listing-mis_category');
    Route::post('/listing-pending_po_list',[POApprovalController::class,'indexPendingPo'])->name('listing-pending_po_list');
    Route::post('/listing-purchase_requisition',[PurchaseRequisitionController::class,'index'])->name('listing-purchase_requisition');
    Route::post('/listing-purchase_requisition_short_close',[PRShortCloseController::class,'index'])->name('listing-purchase_requisition_short_close');
    Route::post('/listing-qc_approval',[QCApprovalController::class,'index'])->name('listing-qc_approval');
    Route::post('/listing-pending_so_for_dispatch',[DispatchPlanController::class,'indexPendingSo'])->name('listing-pending_so_for_dispatch');
    Route::post('/listing-pending_dispatch_plan_for_loading',[LoadingEntryController::class,'indexPendingDispatch'])->name('listing-pending_dispatch_plan_for_loading');
    Route::post('/listing-pending_grn_for_location_dispatch',[GRNMaterialController::class,'indexPendingGrn'])->name('listing-pending_grn_for_location_dispatch');
    Route::post('/listing-pending_material_request_for_so',[SaleOrderController::class,'indexPendingMr'])->name('listing-pending_material_request_for_so');
    Route::post('/listing-item_ledger_report',[ItemLedgerReportController::class,'index'])->name('listing-item_ledger_report');
    Route::post('/listing-pending_pr_for_po',[PurchaseRequisitionController::class,'indexPendingPO'])->name('listing-pending_pr_for_po');
    Route::post('/listing-purchase_requisition_summary',[PurchaseRequisitionSummaryController::class,'index'])->name('listing-purchase_requisition_summary');
    Route::post('/listing-sales_order_summary',[SalesOrderSummaryController::class,'index'])->name('listing-sales_order_summary');
    Route::post('/listing-dispatch_plan_summary',[DispatchPlanSummaryController::class,'index'])->name('listing-dispatch_plan_summary');
    Route::post('/listing-pending_so_for_dispatch_report',[PendingSOforDispatchController::class,'index'])->name('listing-pending_so_for_dispatch_report');
    Route::post('/listing-pending_so_for_dispatch_so_wise_report',[PendingSoForDispatchSoWiseController::class,'index'])->name('listing-pending_so_for_dispatch_so_wise_report');
    Route::post('/listing-loading_entry_summary',[LoadingSummaryController::class,'index'])->name('listing-loading_entry_summary');
    Route::post('/listing-pending_dispatch_plan_for_loading_entry_summary',[PendingDispatchPlanforLoadingEntryController::class,'index'])->name('listing-pending_dispatch_plan_for_loading_entry_summary');
    Route::post('/listing-quotation',[QuotationController::class,'index'])->name('listing-quotation');
    Route::post('/listing-sales_return',[SalesReturnController::class,'index'])->name('listing-sales_return');
    Route::post('/listing-material_request_summary',[MaterialRequestSummaryController::class,'index'])->name('listing-material_request_summary');
    Route::post('/listing-item_production_summary',[ItemProductionSummaryController::class,'index'])->name('listing-item_production_summary');
    Route::post('/listing-item_issue_summary',[ItemIssueSummaryController::class,'index'])->name('listing-item_issue_summary');
    Route::post('/listing-item_return_slip_summary',[ItemReturnSlipSummaryController::class,'index'])->name('listing-item_return_slip_summary');
    Route::post('/listing-purchase_order_summary',[PurchaseOrderSummaryController::class,'index'])->name('listing-purchase_order_summary');

    Route::post('/listing-dealer_status_update_utility',[DealerStatusUpdateUtilityController::class,'index'])->name('listing-dealer_status_update_utility');
    Route::post('/listing-grn_verification',[GRNVerificationController::class,'index'])->name('listing-grn_verification');
    Route::post('/listing-item_stock_transfer',[ItemStockTransferController::class,'index'])->name('listing-item_stock_transfer');
    Route::post('/listing-print_sales_order_report',[PrintSalesOrderReportController::class,'index'])->name('listing-print_sales_order_report');
    Route::post('/listing-pending_purchase_requisition_for_location',[PendingPurchaseRequisitionForLocationController::class,'index'])->name('listing-pending_purchase_requisition_for_location');
    Route::post('/listing-pending_so_for_location',[PendingSOForLocationController::class,'index'])->name('listing-pending_so_for_location');
    Route::post('/listing-grn_verification_summary',[GRNVerificationSummaryController::class,'index'])->name('listing-grn_verification_summary');
    Route::post('/listing-qc_approval_summary',[QCApprovalSummaryController::class,'index'])->name('listing-qc_approval_summary');
    Route::post('/listing-sales_return_summary',[SalesReturnSummaryController::class,'index'])->name('listing-sales_return_summary');
    Route::post('/listing-pending_grn_for_qc_verification',[PendingGRNForQCVerificationController::class,'index'])->name('listing-pending_grn_for_qc_verification');
    Route::post('/listing-grn_summary',[GRNSummaryController::class,'index'])->name('listing-grn_summary');
    Route::post('/listing-grn_summary_location',[GRNSummaryLocationController::class,'index'])->name('listing-grn_summary_location');
    Route::post('/listing-supplier_return_challan_summary',[SupplierReturnChallanSummaryController::class,'index'])->name('listing-supplier_return_challan_summary');
    Route::post('/listing-pending_material_request_status',[PendingMaterialRequestStatusController::class,'index'])->name('listing-pending_material_request_status');


    
     /**
     * Add Routes
     */


    Route::get('/add-company',[CompanyController::class,'create'])->name('add-company');
    Route::get('/add-country',[CountryController::class,'create'])->name('add-country');
    Route::get('/add-village',[VillageController::class,'create'])->name('add-village');
    Route::get('/add-item',[ItemController::class,'create'])->name('add-item');
    Route::get('/add-raw-material',[RawMaterialController::class,'create'])->name('add-raw_material');
    Route::get('/add-transporter',[TransporterController::class,'create'])->name('add-transporter');
    Route::get('/add-item-group',[ItemGroupController::class,'create'])->name('add-item_group');
    Route::get('/add-raw-material-group',[RawMaterialGroupController::class,'create'])->name('add-raw_material_group');
    Route::get('/add-customer-group',[CustomerGroupController::class,'create'])->name('add-customer_group');
    Route::get('/add-state',[StateController::class,'create'])->name('add-state');
    Route::get('/add-district',[CityController::class,'create'])->name('add-district');
    Route::get('/add-customer',[CustomerController::class,'create'])->name('add-customer');
    Route::get('/add-company_year',[CompanyYearController::class,'create'])->name('add-company_year');
    Route::get('/add-company_year',[CompanyYearController::class,'create'])->name('add-company_year');
    Route::get('/add-hsn_code',[HsnCodeController::class,'create'])->name('add-hsn_code');
    Route::get('/add-supplier',[SupplierController::class,'create'])->name('add-supplier');
    Route::get('/add-taluka',[TalukaController::class,'create'])->name('add-taluka');
    Route::get('/add-location',[LocationController::class,'create'])->name('add-location');
    Route::get('/add-unit',[UnitController::class,'create'])->name('add-unit');
    Route::get('/add-item_item_mapping',[ItemRawMaterialMappingController::class,'create'])->name('add-item_raw_material_mapping');
    Route::get('/add-sales_order',[SaleOrderController::class,'create'])->name('add-sales_order');
    Route::get('/add-price_list',[PriceListController::class,'create'])->name('add-price_list');
    Route::get('/add-purchase_order',[PurchaseOrderController::class,'create'])->name('add-purchase_order');
    Route::get('/add-po_short_close',[POShortCloseController::class,'create'])->name('add-po_short_close');
    Route::get('/add-grn_details',[GRNMaterialController::class,'create'])->name('add-grn_details');
    Route::get('/add-grn_location',[GRNMaterialController::class,'create'])->name('add-grn_location');
    Route::get('/add-supplier_rej_challan',[SupplierRejectionController::class,'create'])->name('add-supplier_rej_challan');
    Route::get('/add-item_issue',[ItemIssueController::class,'create'])->name('add-item_issue');
    Route::get('/add-item_return',[ItemReturnController::class,'create'])->name('add-item_return');
    Route::get('/add-item_production',[ItemProductionController::class,'create'])->name('add-item_production');
    Route::get('/add-item_assm_production',[ItemAssemblyProductionController::class,'create'])->name('add-item_assm_production');
    Route::get('/add-material_request',[MaterialRequestController::class,'create'])->name('add-material_request');
    Route::get('/add-delivery_challan',[DeliveryChallanController::class,'create'])->name('add-delivery_challan');
    Route::get('/add-truck_wise_item',[TruckWiseItemListController::class,'create'])->name('add-truck_wise_item');
    Route::get('/add-customer_return',[CustomerReturnController::class,'create'])->name('add-customer_return');
    Route::get('/add-cr_decision',[CRDisionController::class,'create'])->name('add-cr_decision');
    Route::get('/add-dealer',[DealerController::class,'create'])->name('add-dealer');
    Route::get('/add-dispatch_plan',[DispatchPlanController::class,'create'])->name('add-dispatch_plan');
    Route::get('/add-loading_entry',[LoadingEntryController::class,'create'])->name('add-loading_entry');
    Route::get('/add-so_short_close',[SOShortCloseController::class,'create'])->name('add-so_short_close');
    Route::get('/add-transaction_so_short_close',[TransactionSOShortCloseController::class,'create'])->name('add-transaction_so_short_close');
    Route::get('/add-customer_replacement_entry',[CustomerReplacementEntryController::class,'create'])->name('add-customer_replacement_entry');
    Route::get('/add-so_mapping',[SOmappingController::class,'create'])->name('add-so_mapping');
    Route::get('/add-replacement_item_decision',[ReplacementItemDecisionController::class,'create'])->name('add-replacement_item_decision');
    Route::get('/add-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'create'])->name('add-location_customer_group_mappning');
    Route::get('/add-supplier_item_mapping',[SupplierItemMappingController::class,'create'])->name('add-supplier_item_mapping');
    Route::get('/add-po_approval',[POApprovalController::class,'create'])->name('add-po_approval');
    Route::get('/add-mis_category',[MisCategoryController::class,'create'])->name('add-mis_category');
    Route::get('/add-purchase_requisition',[PurchaseRequisitionController::class,'create'])->name('add-purchase_requisition');
    Route::get('/add-purchase_requisition_short_close',[PRShortCloseController::class,'create'])->name('add-purchase_requisition_short_close');
    Route::get('/add-qc_approval',[QCApprovalController::class,'create'])->name('add-qc_approval');
    Route::get('/add-sm_approval', [ApprovalController::class, 'create'])->name('add-sm_approval');
    Route::get('/add-state_coordinator_approval', [ApprovalController::class, 'create'])->name('add-state_coordinator_approval');
    Route::get('/add-zsm_approval', [ApprovalController::class, 'create'])->name('add-zsm_approval');
    Route::get('/add-gm_approval', [ApprovalController::class, 'create'])->name('add-gm_approval');
    // Route::get('/add-md_approval', [ApprovalController::class, 'create'])->name('add-md_approval');
    Route::get('/add-quotation', [QuotationController::class, 'create'])->name('add-quotation');
    Route::get('/add-sales_return', [SalesReturnController::class, 'create'])->name('add-sales_return');
    Route::get('/add-item_stock_transfer', [ItemStockTransferController::class, 'create'])->name('add-item_stock_transfer');
    Route::get('/add-grn_verification', [GRNVerificationController::class, 'create'])->name('add-grn_verification');



    /**
     * Store Routes
     * */


    Route::post('/store-company',[CompanyController::class,'store'])->name('store-company');
    Route::post('/store-country',[CountryController::class,'store'])->name('store-country');
    Route::post('/store-village',[VillageController::class,'store'])->name('store-village');
    Route::post('/store-item',[ItemController::class,'store'])->name('store-item');
    Route::post('/store-raw-material',[RawMaterialController::class,'store'])->name('store-raw-material');
    Route::post('/store-transporter',[TransporterController::class,'store'])->name('store-transporter');
    Route::post('/store-item-group',[ItemGroupController::class,'store'])->name('store-item-group');
    Route::post('/store-raw-material-group',[RawMaterialGroupController::class,'store'])->name('store-raw-material-group');
    Route::post('/store-customer_group',[CustomerGroupController::class,'store'])->name('store-customer_group');
    Route::post('/store-state',[StateController::class,'store'])->name('store-state');
    Route::post('/store-district',[CityController::class,'store'])->name('store-district');
    Route::post('/store-customer',[CustomerController::class,'store'])->name('store-customer');
    Route::post('/store-company_year',[CompanyYearController::class,'store'])->name('store-company_year');
    Route::post('/store-hsn_code',[HsnCodeController::class,'store'])->name('store-hsn_code');
    Route::post('/store-supplier',[SupplierController::class,'store'])->name('store-supplier');
    Route::post('/store-taluka',[TalukaController::class,'store'])->name('store-taluka');
    Route::post('/store-location',[LocationController::class,'store'])->name('store-location');
    Route::post('/store-unit',[UnitController::class,'store'])->name('store-unit');
    Route::post('/store-item_item_mapping',[ItemRawMaterialMappingController::class,'store'])->name('store-item-raw-matrial-mapping');
    Route::post('/store-sales_order',[SaleOrderController::class,'store'])->name('store-sales_order');
    Route::post('/store-price_list',[PriceListController::class,'store'])->name('store-price_list');
    Route::post('/store-purchase_order',[PurchaseOrderController::class,'store'])->name('store-purchase_order');
    Route::post('/store-po_short_close',[POShortCloseController::class,'store'])->name('store-po_short_close');
    Route::post('/store-grn_details',[GRNMaterialController::class,'store'])->name('store-grn_details');
    Route::post('/store-supplier_rej_challan',[SupplierRejectionController::class,'store'])->name('store-supplier_rej_challan');
    Route::post('/store-item_issue',[ItemIssueController::class,'store'])->name('store-item_issue');
    Route::post('/store-item_production',[ItemProductionController::class,'store'])->name('store-item_production');
    Route::post('/store-item_assm_production',[ItemAssemblyProductionController::class,'store'])->name('store-item_assm_production');
    Route::post('/store-item_return',[ItemReturnController::class,'store'])->name('store-item_return');
    Route::post('/store-material_request',[MaterialRequestController::class,'store'])->name('store-material_request');
    Route::post('/store-delivery_challan',[DeliveryChallanController::class,'store'])->name('store-delivery_challan');
    Route::post('/store-truck_wise_item',[TruckWiseItemListController::class,'store'])->name('store-truck_wise_item');
    Route::post('/store-customer_return',[CustomerReturnController::class,'store'])->name('store-customer_return');
    Route::post('/store-cr_decision',[CRDisionController::class,'store'])->name('store-cr_decision');
    Route::post('/store-approval',[ApprovalController::class,'store'])->name('store-approval');
    Route::post('/store-dealer',[DealerController::class,'store'])->name('store-dealer');
    Route::post('/store-dispatch_plan',[DispatchPlanController::class,'store'])->name('store-dispatch_plan');
    Route::post('/store-loading_entry',[LoadingEntryController::class,'store'])->name('store-loading_entry');
    Route::post('/store-so_short_close',[SOShortCloseController::class,'store'])->name('store-so_short_close');
    Route::post('/store-transaction_so_short_close',[TransactionSOShortCloseController::class,'store'])->name('store-transaction_so_short_close');
    Route::post('/store-customer_replacement_entry',[CustomerReplacementEntryController::class,'store'])->name('store-customer_replacement_entry');
    Route::post('/store-so_mapping',[SOmappingController::class,'store'])->name('store-so_mapping');
    Route::post('/store-replacement_item_decision',[ReplacementItemDecisionController::class,'store'])->name('store-replacement_item_decision');
    Route::post('/store-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'store'])->name('store-location_customer_group_mappning');
    Route::post('/store-supplier_item_mapping',[SupplierItemMappingController::class,'store'])->name('store-supplier_item_mapping');
    Route::post('/store-po_approval',[POApprovalController::class,'store'])->name('store-po_approval');
    Route::post('/store-grn_against_po_approval',[GrnAgainstPOApprovalController::class,'store'])->name('store-grn_against_po_approval');
    Route::post('/store-mis_category',[MisCategoryController::class,'store'])->name('store-mis_category');
    Route::post('/store-purchase_requisition',[PurchaseRequisitionController::class,'store'])->name('store-purchase_requisition');
    Route::post('/store-purchase_requisition_short_close',[PRShortCloseController::class,'store'])->name('store-purchase_requisition_short_close');
    Route::post('/store-qc_approval',[QCApprovalController::class,'store'])->name('store-qc_approval');
    Route::post('/store-approval_status',[ApprovalstatusController::class,'store'])->name('store-approval_status');
    Route::post('/store-quotation',[QuotationController::class,'store'])->name('store-quotation');
    Route::post('/store-sales_return',[SalesReturnController::class,'store'])->name('store-sales_return');
    Route::post('/store-grn_verification',[GRNVerificationController::class,'store'])->name('store-grn_verification');
    Route::post('/store-item_stock_transfer',[ItemStockTransferController::class,'store'])->name('store-item_stock_transfer');



    /**
     * Get data Routes
     */


    Route::get('/get-company/{id}',[CompanyController::class,'edit'])->name('get-company');
    Route::get('/get-country/{id}',[CountryController::class,'edit'])->name('get-country');
    Route::get('/get-villages/{id}',[VillageController::class,'edit'])->name('get-villages');
    Route::get('/get-items/{id}',[ItemController::class,'edit'])->name('get-items');
    Route::get('/get-raw-materials/{id}',[RawMaterialController::class,'edit'])->name('get-raw-materials');
    Route::get('/get-transporters/{id}',[TransporterController::class,'edit'])->name('get-transporters');
    Route::get('/get-item-groups/{id}',[ItemGroupController::class,'edit'])->name('get-item-groups');
    Route::get('/get-raw-material-groups/{id}',[RawMaterialGroupController::class,'edit'])->name('get-raw-material-groups');
    Route::get('/get-customer-group/{id}',[CustomerGroupController::class,'edit'])->name('get-customer-group');
    Route::get('/get-state/{id}',[StateController::class,'edit'])->name('get-state');
    Route::get('/get-districts/{id}',[CityController::class,'edit'])->name('get-districts');
    Route::get('/get-customer/{id}',[CustomerController::class,'edit'])->name('get-customer');
    Route::get('/get-item_item_mapping/{id}',[ItemRawMaterialMappingController::class,'edit'])->name('get-item-raw-matrial-mapping');
    Route::get('/get-sales_order/{id}',[SaleOrderController::class,'edit'])->name('get-sales_order');
    Route::get('/get-price_list/{id}',[priceListController::class,'edit'])->name('get-price_list');
    Route::get('/get-purchase_order/{id}',[PurchaseOrderController::class,'edit'])->name('get-purchase_order');
    Route::get('/get-po_short_close/{id}',[POShortCloseController::class,'edit'])->name('get-po_short_close');
    Route::get('/get-grn_details/{id}',[GRNMaterialController::class,'edit'])->name('get-grn_details');
    Route::get('get-supplier_rej_challan/{id}',[SupplierRejectionController::class,'edit'])->name('get-supplier_rej_challan');
    Route::get('get-item_issue/{id}',[ItemIssueController::class,'edit'])->name('get-item_issue');
    Route::get('get-item_return/{id}',[ItemReturnController::class,'edit'])->name('get-item_return');
    Route::get('get-item_production/{id}',[ItemProductionController::class,'edit'])->name('get-item_production');
    Route::get('get-item_assm_production/{id}',[ItemAssemblyProductionController::class,'edit'])->name('get-item_assm_production');
    Route::get('get-material_request/{id}',[MaterialRequestController::class,'edit'])->name('get-material_request');
    Route::get('get-delivery_challan/{id}',[DeliveryChallanController::class,'edit'])->name('get-delivery_challan');
    Route::get('get-truck_wise_item/{id}',[TruckWiseItemListController::class,'edit'])->name('get-truck_wise_item');
    Route::get('get-customer_return/{id}',[CustomerReturnController::class,'edit'])->name('get-customer_return');
    Route::get('get-cr_decision/{id}',[CRDisionController::class,'edit'])->name('get-cr_decision');
    Route::get('get-dealer/{id}',[DealerController::class,'edit'])->name('get-dealer');
    Route::get('get-dispatch_plan/{id}',[DispatchPlanController::class,'edit'])->name('get-dispatch_plan');
    Route::get('get-loading_entry/{id}',[LoadingEntryController::class,'edit'])->name('get-loading_entry');
    Route::get('get-so_mapping/{id}',[SOmappingController::class,'edit'])->name('get-so_mapping');
    Route::get('/get-location_customer_group_mappning/{id}',[LocationCustomerGroupMappingController::class,'edit'])->name('get-location_customer_group_mappning');
    Route::get('get-supplier_item_mapping/{id}',[SupplierItemMappingController::class,'edit'])->name('get-supplier_item_mapping');
    Route::get('get-mis_category/{id}',[MisCategoryController::class,'edit'])->name('get-mis_category');
    Route::get('get-purchase_requisition/{id}',[PurchaseRequisitionController::class,'edit'])->name('get-purchase_requisition');
    Route::get('get-qc_approval/{id}',[QCApprovalController::class,'edit'])->name('get-qc_approval');
    Route::get('/get-company_year',[CompanyYearController::class,'makeYear'])->name('get-company_year');
    Route::get('/get-hsn_code/{id}',[HsnCodeController::class,'edit'])->name('get-hsn_code');
    Route::get('/get-taluka/{id}',[TalukaController::class,'edit'])->name('get-taluka');
    Route::get('/get-unit/{id}',[UnitController::class,'edit'])->name('get-unit');
    Route::get('/get-sales_return/{id}',[SalesReturnController::class,'edit'])->name('get-sales_return');
    Route::get('/get-item_stock_transfer/{id}',[ItemStockTransferController::class,'edit'])->name('get-item_stock_transfer');





    Route::get('/get-latest-itemcode',[ItemController::class,'getItemCode'])->name('get-latest-itemcode');
    Route::get('/get-latest_so_no',[SaleOrderController::class,'getLatestSoNo'])->name('get-latest_so_no');
    Route::get('/get-latest_sr_no',[SalesReturnController::class,'getLatestSrNo'])->name('get-latest_sr_no');
    Route::get('/get-so_customer',[SaleOrderController::class,'getSoCustomer'])->name('get-so_customer');
    Route::get('/get-so_reg_no',[SaleOrderController::class,'getRegNo'])->name('get-so_reg_no');
    Route::get('/get-item_data',[SaleOrderController::class,'getItemData'])->name('get-item_data');
    Route::get('/fetch_item_qty',[ItemRawMaterialMappingController::class,'getExistItemQty'])->name('fetch_item_qty');
    Route::get('/get-item_name',[SaleOrderController::class,'getSalesOrderDetails'])->name('fetch_item_qty');
    Route::get('/get-fitting_item_data',[priceListController::class,'getFittingItems'])->name('get-fitting_item_data');
    Route::get('/get-fitting_item_data_for_sr',[SalesReturnController::class,'getFittingItemsForSR'])->name('get-fitting_item_data_for_sr');
    Route::get('/get-fitting_any_item_data',[priceListController::class,'getAnyFittingItems'])->name('get-fitting_any_item_data');
    Route::get('/get-fitting_item_assm_data',[ItemAssemblyProductionController::class,'getFittingItems'])->name('get-fitting_item_assm_data');
    Route::get('/get-fitting_material_request',[MaterialRequestController::class,'getFittingItems'])->name('get-fitting_material_request');
    Route::get('/fetch_item_sales_rate',[priceListController::class,'getExistItemRate'])->name('fetch_item_sales_rate');
    Route::get('/get-latest_po_no',[PurchaseOrderController::class,'getLatestPoNo'])->name('get-latest_po_no');
    Route::get('/get-supplier_contact_person',[PurchaseOrderController::class,'getContactPerson'])->name('get-supplier_contact_person');
    Route::get('/get-last_supplier_details',[PurchaseOrderController::class,'getLastSupplierData'])->name('get-last_supplier_details');
    Route::get('/get-last_so_details',[SaleOrderController::class,'getLastSoData'])->name('get-last_so_details');
    Route::get('/get-latest_grn_no',[GRNMaterialController::class,'getLatestGrnNo'])->name('get-latest_grn_no');
    Route::get('/get-latest_dc_no',[DeliveryChallanController::class,'getLatestDCNo'])->name('get-latest_dc_no');
    Route::get('/get-po_supplier_for_grn',[GRNMaterialController::class,'getPoSupplier'])->name('get-po_supplier_for_grn');
    Route::get('/get-po_list-grn',[GRNMaterialController::class,'getPoListForGrn'])->name('get-po_list-grn');
    Route::get('/get-po_item_list-grn',[GRNMaterialController::class,'getPoItemListForGrn'])->name('get-po_item_list-grn');
    Route::get('/get-latest_src_no',[SupplierRejectionController::class,'getLatestChallanNo'])->name('get-latest_src_no');
    Route::get('/get-pending_item_issue_qty',[ItemIssueController::class,'getLatestItemIssueNo'])->name('get-pending_item_issue_qty');
    Route::get('/get-latest_return_no',[ItemReturnController::class,'getLatestItemReturnNo'])->name('get-latest_return_no');
    Route::get('/get-issue_list-return',[ItemReturnController::class,'getIssueListForReturn'])->name('get-issue_list-return');
    Route::get('/get-pending_item_production_qty',[ItemProductionController::class,'getLatestItemProductionNo'])->name('get-pending_item_production_qty');
    Route::get('/get-pending_item_assm_production_qty',[ItemAssemblyProductionController::class,'getLatestItemAPNo'])->name('get-pending_item_assm_production_qty');
    Route::get('/get-latest_material_request_no',[MaterialRequestController::class,'getLatestMaterialNo'])->name('get-latest_material_request_no');
    // Route::get('/get-pending_material_request',[MaterialRequestController::class,'getLatestMaterialNo'])->name('get-pending_material_request');
    Route::get('/get-issue_parts_data-return',[ItemReturnController::class,'getIssuePartsDataForReturn'])->name('get-issue_parts_data-return');
    Route::get('/get-pending_material_request',[MaterialRequestController::class,'getPendingMaterialRequest'])->name('get-pending_material_request');
    Route::get('/get-pending_material_request_for_pr',[MaterialRequestController::class,'getPendingMaterialRequestPR'])->name('get-pending_material_request_for_pr');
    Route::get('/get-material_parts_data-so',[MaterialRequestController::class,'getMaterialPartsDataForSo'])->name('get-material_parts_data-so');
    Route::get('/get-material_parts_data-pr',[MaterialRequestController::class,'getMaterialPartsDataForPR'])->name('get-material_parts_data-pr');
    Route::get('/get-latest_dispatch_plan_no',[DispatchPlanController::class,'getLatestDispatchNo'])->name('get-latest_dispatch_plan_no');
    Route::get('/get-dispatch_list-loading-entry',[LoadingEntryController::class,'getLoadingListForDispatch'])->name('get-dispatch_list-loading-entry');
    Route::get('/get-dispatch_plan_data',[LoadingEntryController::class,'getDispatchDataForLoading'])->name('get-dispatch_plan_data');
    Route::get('/get-dc_location_for_grn',[GRNMaterialController::class,'getDcLocation'])->name('get-dc_location_for_grn');
    Route::get('/get-dc_list-grn',[GRNMaterialController::class,'getDcListForGrn'])->name('get-dc_list-grn');
    Route::get('/getSearchCustomer',[SaleOrderController::class,'getSearchCustomer'])->name('getSearchCustomer');
    Route::get('/get-oldcustomer',[SaleOrderController::class,'getOldCustomer'])->name('get-oldcustomer');
    Route::get('/get-oldcustomer_so_no',[SaleOrderController::class,'getOldCustomerSoNo'])->name('get-oldcustomer_so_no');
    Route::get('/get-old_so_no',[SaleOrderController::class,'getOldSoNo'])->name('get-old_so_no');
    Route::get('/getSearchSOCustomer',[SaleOrderController::class,'getSearchSOCustomer'])->name('getSearchSOCustomer');
    Route::get('/get-old_item-so',[SaleOrderController::class,'getOldItemSo'])->name('get-old_item-so');
    Route::get('get-customer_replacement_entry/{id}',[CustomerReplacementEntryController::class,'edit'])->name('get-customer_replacement_entry');
    Route::get('/get-latest_cre_no',[CustomerReplacementEntryController::class,'getLatestCRENo'])->name('get-latest_cre_no');
    Route::get('/get-latest_so_mapping_no',[SOmappingController::class,'getLatestMappingNo'])->name('get-latest_so_mapping_no');
    Route::get('/get-customer_for_replacement',[SOmappingController::class,'getReplacementCustomer'])->name('get-customer_for_replacement');
    Route::get('/get-replacement_list-mapping',[SOmappingController::class,'getReplacementListForMapping'])->name('get-replacement_list-mapping');
    Route::get('/get-pending_customer_replacement',[SOmappingController::class,'getPendingCustomerReplacement'])->name('get-pending_customer_replacement');
    Route::get('/get-so_mapping_data',[ReplacementItemDecisionController::class,'getSoMappingData'])->name('get-so_mapping_data');
    Route::get('/get-replacement_item_decision/{id}',[ReplacementItemDecisionController::class,'edit'])->name('get-replacement_item_decision');
    Route::get('/fetch_customer_group',[LocationCustomerGroupMappingController::class,'getExistCustomerGroup'])->name('fetch_customer_group');
    Route::get('/get-items_from_supplier_mapping',[PurchaseOrderController::class,'getItemsFromMapping'])->name('get-items_from_supplier_mapping');
    Route::get('/get-pr_items_from_supplier_mapping',[PurchaseRequisitionController::class,'getPRItemsFromMapping'])->name('get-pr_items_from_supplier_mapping');
    Route::post('/get-material_details',[ApprovalController::class,'getMaterialDetails'])->name('get-material_details');
    Route::post('/get-po_details',[POApprovalController::class,'getPOApprovalDetails'])->name('get-po_details');
    Route::get('/get-Item_rate',[PurchaseOrderController::class,'getRatePerUnit'])->name('get-Item_rate');
    Route::get('/get-exceed_qty-grn',[GRNMaterialController::class,'getexceedGrnQty'])->name('get-exceed_qty-grn');
    Route::get('/get-country_state_for_location',[SaleOrderController::class,'getCountyandStateForLocation'])->name('get-country_state_for_location');
    Route::get('/get-fitting_so_item_data_for_dispatch',[DispatchPlanController::class,'getFittingSoItemForDispatch'])->name('get-fitting_so_item_data_for_dispatch');
    Route::get('/get-secondary_so_item_data_for_dispatch',[DispatchPlanController::class,'getSecondarySoItemForDispatch'])->name('get-secondary_so_item_data_for_dispatch');
    Route::get('/get-production_assembly_so_item_data_for_dispatch',[DispatchPlanController::class,'getAssemblySoItemForDispatch'])->name('get-production_assembly_so_item_data_for_dispatch');

    Route::get('/get-secondary_dispatch_item_data_for_loading',[LoadingEntryController::class,'getSecondaryDispatchItemForLoading'])->name('get-secondary_dispatch_item_data_for_loading');

    Route::get('/get-pending_po_list',[POApprovalController::class,'getPendingPoList'])->name('get-pending_po_list');
    Route::post('/get-so_dealer',[SaleOrderController::class,'getSoDealer'])->name('get-so_dealer');
    Route::get('/get-latest_pr_no',[PurchaseRequisitionController::class,'getLatestPRNo'])->name('get-latest_pr_no');
    Route::get('/get-item_supplier_pr_data',[PurchaseRequisitionController::class,'getItemSupplierData'])->name('get-item_supplier_pr_data');
    Route::get('/get-pr_supplier_for_po',[PurchaseOrderController::class,'getPrSupplierData'])->name('get-pr_supplier_for_po');
    Route::get('/get-pr_list-po',[PurchaseOrderController::class,'getPrListForPO'])->name('get-pr_list-po');
    Route::get('/get-pr_item_list-po',[PurchaseOrderController::class,'getPrItemListForPo'])->name('get-pr_item_list-po');
    Route::get('/get-latest_qc_no',[QCApprovalController::class,'getLatestQCNo'])->name('get-latest_qc_no');
    Route::get('/get-qc_supplier_for_src',[SupplierRejectionController::class,'getsrcsupplier'])->name('get-qc_supplier_for_src');
    Route::get('/get-qc_list-src',[SupplierRejectionController::class,'getQCListForSrc'])->name('get-qc_list-src');
    Route::get('/get-approval_status', [ApprovalstatusController::class, 'getApprovalStatus'])->name('get_approval_status');
    Route::get('/get-latest_quotation_no',[QuotationController::class,'getLatestQuotationNo'])->name('get-latest_quotation_no');
    Route::get('/get-latest_ist_no',[ItemStockTransferController::class,'getLatestISTNo'])->name('get-latest_ist_no');
    Route::post('/get-quot_dealer',[QuotationController::class,'getQuotationDealer'])->name('get-quot_dealer');

    Route::post('/get-dp_number',[SalesReturnController::class,'getDPNumber'])->name('get-dp_number');
    Route::post('/get-dispatch_data',[SalesReturnController::class,'getDispatchDataForSR'])->name('get-dispatch_plan_data');
    Route::get('get-quotation/{id}',[QuotationController::class,'edit'])->name('get-quotation');

    Route::get('/get-items_from_price_list_to_customer',[MaterialRequestController::class,'getItemsFromPriceList'])->name('get-items_from_price_list_to_customer');
    Route::get('/get-items_from_so_customer',[SalesReturnController::class,'getItemsFromSalesOrder'])->name('get-items_from_so_customer');
    Route::get('/get-dp_no_from_dispatch_plan',[SalesReturnController::class,'getDpNoFromDispatchPlan'])->name('get-dp_no_from_dispatch_plan');

    Route::get('/get-item_details_data',[ItemRawMaterialMappingController::class,'getItemsDetails'])->name('get-item_details_data');


     Route::get('/fetch_details_item_qty',[ItemRawMaterialMappingController::class,'getDetailExistItemQty'])->name('fetch_details_item_qty');

    Route::get('/get-deteils_item-raw-matrial-mapping/{id}',[ItemRawMaterialMappingController::class,'editDetails'])->name('get-deteils_item-raw-matrial-mapping');
    
    Route::get('/get-ist_details_items',[ItemStockTransferController::class,'getISTDetailsItems'])->name('get-ist_details_items');
    Route::get('/get-ist_selected_details_items',[ItemStockTransferController::class,'getISTSelectedDetailsItems'])->name('get-ist_selected_details_items');
    Route::get('/get-pending_grn_verification',[GRNVerificationController::class,'getPendingGRNVerification'])->name('get-pending_grn_verification');

    /**
     * Edit Routes
     */

    Route::get('/edit-user_access',[AdminController::class,'showUserAccess'])->name('edit-user_access');
    Route::get('/edit-company/{id}',[CompanyController::class,'show'])->name('edit-company');
    Route::get('/edit-country/{id}',[CountryController::class,'show'])->name('edit-country');
    Route::get('/edit-village/{id}',[VillageController::class,'show'])->name('edit-village');
    Route::get('/edit-item/{id}',[ItemController::class,'show'])->name('edit-item');
    Route::get('/edit-raw-material/{id}',[RawMaterialController::class,'show'])->name('edit-raw_material');
    Route::get('/edit-transporter/{id}',[TransporterController::class,'show'])->name('edit-transporter');
    Route::get('/edit-item-group/{id}',[ItemGroupController::class,'show'])->name('edit-item_group');
    Route::get('/edit-raw-material-group/{id}',[RawMaterialGroupController::class,'show'])->name('edit-raw_material_group');
    Route::get('/edit-customer_group/{id}',[CustomerGroupController::class,'show'])->name('edit-customer_group');
    Route::get('/edit-state/{id}',[StateController::class,'show'])->name('edit-state');
    Route::get('/edit-district/{id}',[CityController::class,'show'])->name('edit-district');
    Route::get('/edit-customer/{id}',[CustomerController::class,'show'])->name('edit-customer');
    Route::get('/edit-hsn_code/{id}',[HsnCodeController::class,'show'])->name('edit-hsn_code');
    Route::get('/edit-supplier/{id}',[SupplierController::class,'show'])->name('edit-supplier');
    Route::get('/edit-taluka/{id}',[TalukaController::class,'show'])->name('edit-taluka');
    Route::get('/edit-location/{id}',[LocationController::class,'show'])->name('edit-location');
    Route::get('/edit-unit/{id}',[UnitController::class,'show'])->name('edit-unit');
    Route::get('/edit-item_item_mapping/{id}',[ItemRawMaterialMappingController::class,'show'])->name('edit-item_raw_material_mapping');
    Route::get('/edit-sales_order/{id}',[SaleOrderController::class,'show'])->name('edit-sales_order');
    Route::get('/edit-price_list/{id}',[priceListController::class,'show'])->name('edit-price_list');
    Route::get('/edit-purchase_order/{id}',[PurchaseOrderController::class,'show'])->name('edit-purchase_order');
    Route::get('/edit-po_short_close/{id}',[POShortCloseController::class,'show'])->name('edit-po_short_close');
    Route::get('/edit-grn_details/{id}',[GRNMaterialController::class,'show'])->name('edit-grn_details');
    Route::get('/edit-grn_location/{id}',[GRNMaterialController::class,'show'])->name('edit-grn_location');
    Route::get('/edit-supplier_rej_challan/{id}',[SupplierRejectionController::class,'show'])->name('edit-supplier_rej_challan');
    Route::get('/edit-item_issue/{id}',[ItemIssueController::class,'show'])->name('edit-item_issue');
    Route::get('/edit-item_return/{id}',[ItemReturnController::class,'show'])->name('edit-item_return');
    Route::get('/edit-item_production/{id}',[ItemProductionController::class,'show'])->name('edit-item_production');
    Route::get('/edit-item_assm_production/{id}',[ItemAssemblyProductionController::class,'show'])->name('edit-item_assm_production');
    Route::get('/edit-material_request/{id}',[MaterialRequestController::class,'show'])->name('edit-material_request');
    Route::get('/edit-delivery_challan/{id}',[DeliveryChallanController::class,'show'])->name('edit-delivery_challan');
    Route::get('/edit-truck_wise_item/{id}',[TruckWiseItemListController::class,'show'])->name('edit-truck_wise_item');
    Route::get('/edit-customer_return/{id}',[CustomerReturnController::class,'show'])->name('edit-customer_return');
    Route::get('/edit-cr_decision/{id}',[CRDisionController::class,'show'])->name('edit-cr_decision');
    Route::get('/edit-dealer/{id}',[DealerController::class,'show'])->name('edit-dealer');
    Route::get('/edit-dispatch_plan/{id}',[DispatchPlanController::class,'show'])->name('edit-dispatch_plan');
    Route::get('/edit-loading_entry/{id}',[LoadingEntryController::class,'show'])->name('edit-loading_entry');
    Route::get('/edit-customer_replacement_entry/{id}',[CustomerReplacementEntryController::class,'show'])->name('edit-customer_replacement_entry');
    Route::get('/edit-so_mapping/{id}',[SOmappingController::class,'show'])->name('edit-so_mapping');
    Route::get('/edit-replacement_item_decision/{id}',[ReplacementItemDecisionController::class,'show'])->name('edit-replacement_item_decision');
    Route::get('/edit-location_customer_group_mappning/{id}',[LocationCustomerGroupMappingController::class,'show'])->name('edit-location_customer_group_mappning');
    Route::get('/edit-supplier_item_mapping/{id}',[SupplierItemMappingController::class,'show'])->name('edit-supplier_item_mapping');
    Route::get('/edit-mis_category/{id}',[MisCategoryController::class,'show'])->name('edit-mis_category');
    Route::get('/edit-purchase_requisition/{id}',[PurchaseRequisitionController::class,'show'])->name('edit-purchase_requisition');
    Route::get('/edit-qc_approval/{id}',[QCApprovalController::class,'show'])->name('edit-qc_approval');
    Route::get('/edit-quotation/{id}',[QuotationController::class,'show'])->name('edit-quotation');
    Route::get('/edit-sales_return/{id}',[SalesReturnController::class,'show'])->name('edit-sales_return');
    Route::get('/edit-item_stock_transfer/{id}',[ItemStockTransferController::class,'show'])->name('edit-item_stock_transfer');



        /**
    * Update Routes
    */


    Route::post('/update-company',[CompanyController::class,'update'])->name('update-company');
    Route::post('/update-country',[CountryController::class,'update'])->name('update-country');
    Route::post('/update-customer_group',[CustomerGroupController::class,'update'])->name('update-customer_group');
    Route::post('/update-state',[StateController::class,'update'])->name('update-state');
    Route::post('/update-district',[CityController::class,'update'])->name('update-district');
    Route::post('/update-village',[VillageController::class,'update'])->name('update-village');
    Route::post('/update-item',[ItemController::class,'update'])->name('update-item');
    Route::post('/update-raw-material',[RawMaterialController::class,'update'])->name('update-raw-material');
    Route::post('/update-transporters',[TransporterController::class,'update'])->name('update-transporter');
    Route::post('/update-item-group',[ItemGroupController::class,'update'])->name('update-item-group');
    Route::post('/update-raw-material-group',[RawMaterialGroupController::class,'update'])->name('update-raw-material-group');
    Route::post('/update-customer',[CustomerController::class,'update'])->name('update-customer');
    Route::post('/update-price_list',[PriceListController::class,'update'])->name('update-price_list');
    Route::post('/update-supplier_rej_challan',[SupplierRejectionController::class,'update'])->name('update-supplier_rej_challan');
    Route::post('/update-item_issue',[ItemIssueController::class,'update'])->name('update-item_issue');
    Route::post('/update-item_return',[ItemReturnController::class,'update'])->name('update-item_return');
    Route::post('/update-item_production',[ItemProductionController::class,'update'])->name('update-item_production');
    Route::post('/update-item_assm_production',[ItemAssemblyProductionController::class,'update'])->name('update-item_assm_production');
    Route::post('/update-material_request',[MaterialRequestController::class,'update'])->name('update-material_request');
    Route::post('/update-delivery_challan',[DeliveryChallanController::class,'update'])->name('update-delivery_challan');
    Route::post('/update-truck_wise_item',[TruckWiseItemListController::class,'update'])->name('update-truck_wise_item');
    Route::post('/update-customer_return',[CustomerReturnController::class,'update'])->name('update-customer_return');
    Route::post('/update-cr_decision',[CRDisionController::class,'update'])->name('update-cr_decision');
    Route::post('/update-dealer',[DealerController::class,'update'])->name('update-dealer');
    Route::post('/update-dispatch_plan',[DispatchPlanController::class,'update'])->name('update-dispatch_plan');
    Route::post('/update-loading_entry',[LoadingEntryController::class,'update'])->name('update-loading_entry');
    Route::post('/update-supplier_item_mapping',[SupplierItemMappingController::class,'update'])->name('update-supplier_item_mapping');
    Route::post('/update-mis_category',[MisCategoryController::class,'update'])->name('update-mis_category');
    Route::post('/update-purchase_requisition',[PurchaseRequisitionController::class,'update'])->name('update-purchase_requisition');
    Route::post('/update-qc_approval',[QCApprovalController::class,'update'])->name('update-qc_approval');
    Route::post('/change-company_year',[CompanyYearController::class,'change'])->name('change-company_year');
    Route::get('/switch-company_year',[CompanyYearController::class,'switchYear'])->name('switch-company_year');
    Route::post('/update-hsn_code',[HsnCodeController::class,'update'])->name('update-hsn_code');
    Route::post('/update-taluka',[TalukaController::class,'update'])->name('update-taluka');
    Route::post('/update-location',[LocationController::class,'update'])->name('update-location');
    Route::post('/update-unit',[UnitController::class,'update'])->name('update-unit');
    Route::post('/update-supplier',[SupplierController::class,'update'])->name('update-supplier');
    Route::post('/update-item_item_mapping',[ItemRawMaterialMappingController::class,'update'])->name('update-item-raw-matrial-mapping');
    Route::post('/update-sales_order',[SaleOrderController::class,'update'])->name('update-sales_order');
    Route::post('/update-purchase_order',[PurchaseOrderController::class,'update'])->name('update-purchase_order');
    Route::post('/update-po_short_close',[POShortCloseController::class,'update'])->name('update-po_short_close');
    Route::post('/update-grn_details',[GRNMaterialController::class,'update'])->name('update-grn_details');
    Route::post('/update-customer_replacement_entry',[CustomerReplacementEntryController::class,'update'])->name('update-customer_replacement_entry');
    Route::post('/update-so_mapping',[SOmappingController::class,'update'])->name('update-so_mapping');
    Route::post('/update-replacement_item_decision',[ReplacementItemDecisionController::class,'update'])->name('update-replacement_item_decision');
    Route::post('/update-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'update'])->name('update-location_customer_group_mappning');
    Route::post('/update-quotation',[QuotationController::class,'update'])->name('update-quotation');
    Route::post('/update-dealer_status_update_utility',[DealerStatusUpdateUtilityController::class,'update'])->name('update-dealer_status_update_utility');
    Route::post('/update-sales_return',[SalesReturnController::class,'update'])->name('update-sales_return');






    /**
     * Delete Routes
     */


    Route::get('/delete-company',[CompanyController::class,'destroy'])->name('remove-company');
    Route::get('/delete-country',[CountryController::class,'destroy'])->name('remove-country');
    Route::get('/delete-village',[VillageController::class,'destroy'])->name('remove-village');
    Route::get('/delete-item',[ItemController::class,'destroy'])->name('remove-item');
    Route::get('/delete-raw-material',[RawMaterialController::class,'destroy'])->name('remove-raw-material');
    Route::get('/delete-transporter',[TransporterController::class,'destroy'])->name('remove-transporter');
    Route::get('/delete-item-group',[ItemGroupController::class,'destroy'])->name('remove-item-group');
    Route::get('/delete-raw-material-group',[RawMaterialGroupController::class,'destroy'])->name('remove-raw-material-group');
    Route::get('/delete-customer_group',[CustomerGroupController::class,'destroy'])->name('remove-customer_group');
    Route::get('/delete-state',[StateController::class,'destroy'])->name('remove-state');
    Route::get('/delete-district',[CityController::class,'destroy'])->name('remove-district');
    Route::get('/delete-customer',[CustomerController::class,'destroy'])->name('remove-customer');
    Route::get('/delete-company_year',[CompanyYearController::class,'destroy'])->name('remove-company_year');
    Route::get('/delete-supplier',[SupplierController::class,'destroy'])->name('remove-supplier');
    Route::get('/delete-taluka',[TalukaController::class,'destroy'])->name('remove-taluka');
    Route::get('/delete-location',[LocationController::class,'destroy'])->name('remove-location');
    Route::get('/delete-unit',[UnitController::class,'destroy'])->name('remove-unit');
    Route::get('/delete-hsn_code',[HsnCodeController::class,'destroy'])->name('remove-hsn_code');
    Route::get('/delete-item_item_mapping',[ItemRawMaterialMappingController::class,'destory'])->name('remove-item-raw-matrial-mapping');
    Route::get('/delete-sales_order',[SaleOrderController::class,'destroy'])->name('delete-sales_order');
    Route::get('/delete-price_list',[PriceListController::class,'destroy'])->name('delete-price_list');
    Route::get('/delete-purchase_order',[PurchaseOrderController::class,'destroy'])->name('delete-purchase_order');
    // Route::get('/delete-po_short_close',[POShortCloseController::class,'destroy'])->name('remove-po_short_close');
    Route::get('/delete-po_short_close',[POShortCloseController::class,'destroy'])->name('delete-po_short_close');
    Route::get('/delete-grn_details',[GRNMaterialController::class,'destroy'])->name('delete-grn_details');
    Route::get('/delete-supplier_rej_challan',[SupplierRejectionController::class,'destroy'])->name('remove-supplier_rej_challan');
    Route::get('/delete-item_issue',[ItemIssueController::class,'destroy'])->name('remove-item_issue');
    Route::get('/delete-item_return',[ItemReturnController::class,'destroy'])->name('remove-item_return');
    Route::get('/delete-item_production',[ItemProductionController::class,'destroy'])->name('remove-item_production');
    Route::get('/delete-item_assm_production',[ItemAssemblyProductionController::class,'destroy'])->name('remove-item_assm_production');
    Route::get('/delete-material_request',[MaterialRequestController::class,'destroy'])->name('remove-material_request');
    Route::get('/delete-delivery_challan',[DeliveryChallanController::class,'destroy'])->name('remove-delivery_challan');
    Route::get('/delete-truck_wise_item',[TruckWiseItemListController::class,'destroy'])->name('remove-truck_wise_item');
    Route::get('/delete-customer_return',[CustomerReturnController::class,'destroy'])->name('remove-customer_return');
    Route::get('/delete-cr_decision',[CRDisionController::class,'destroy'])->name('remove-cr_decision');
    Route::get('/delete-dealer',[DealerController::class,'destroy'])->name('remove-dealer');
    Route::get('/delete-dispatch_plan',[DispatchPlanController::class,'destroy'])->name('remove-dispatch_plan');
    Route::get('/delete-loading_entry',[LoadingEntryController::class,'destroy'])->name('remove-loading_entry');
    Route::get('/delete-so_short_close',[SOShortCloseController::class,'destroy'])->name('delete-so_short_close');
    Route::get('/delete-transaction_so_short_close',[TransactionSOShortCloseController::class,'destroy'])->name('delete-transaction_so_short_close');
    Route::get('/delete-customer_replacement_entry',[CustomerReplacementEntryController::class,'destroy'])->name('remove-customer_replacement_entry');
    Route::get('/delete-so_mapping',[SOmappingController::class,'destroy'])->name('delete-so_mapping');
    Route::get('/delete-replacement_item_decision',[ReplacementItemDecisionController::class,'destroy'])->name('remove-replacement_item_decision');
    Route::get('/delete-location_customer_group_mappning',[LocationCustomerGroupMappingController::class,'destory'])->name('remove-location_customer_group_mappning');
    Route::get('/delete-supplier_item_mapping',[SupplierItemMappingController::class,'destroy'])->name('delete-supplier_item_mapping');
    Route::get('/remove_mr_detail',[ApprovalController::class,'destroyMrDetail'])->name('remove_mr_detail');
    Route::get('/delete-po_approval',[POApprovalController::class,'destroy'])->name('delete-po_approval');
    Route::get('/delete-mis_category',[MisCategoryController::class,'destroy'])->name('remove-mis_category');
    Route::get('/delete-purchase_requisition',[PurchaseRequisitionController::class,'destroy'])->name('remove-purchase_requisition');
    Route::get('/delete-purchase_requisition_short_close',[PRShortCloseController::class,'destroy'])->name('remove-purchase_requisition_short_close');
    Route::get('/delete-qc_approval',[QCApprovalController::class,'destroy'])->name('remove-qc_approval');
    Route::get('/delete-approval',[ApprovalController::class,'destroy'])->name('delete-approval');
    Route::get('/delete-quotation',[QuotationController::class,'destroy'])->name('delete-quotation');
    Route::get('/delete-sales_return',[SalesReturnController::class,'destroy'])->name('delete-sales_return');
    Route::get('/delete-item_stock_transfer',[ItemStockTransferController::class,'destroy'])->name('delete-item_stock_transfer');
    Route::get('/delete-grn_verification',[GRNVerificationController::class,'destroy'])->name('delete-grn_verification');

    Route::get('/delete-details-item-raw-matrial-mapping',[ItemRawMaterialMappingController::class,'destoryDetails'])->name('delete-details-item-raw-matrial-mapping');






    /**
     * Print Routes
     * Print Routes
     */
    Route::get('/print-supplier_rej_challan/{id?}',[SupplierRejectionController::class,'printSupplierRej'])->name('print-supplier_rej_challan');


    Route::post('/upload-docs',[FileController::class,'upload'])->name('upload-docs');
    Route::post('/remove-docs',[FileController::class,'removeTempUpload'])->name('remove-docs');
    Route::post('/copy-docs',[FileController::class,'copyFiles'])->name('copy-docs');


    /**
     * Others Routes
     */
    Route::get('/preview-supplier_rej_challan/{id?}',[SupplierRejectionController::class,'previewSupplierRej'])->name('preview-supplier_rej_challan');

    Route::get('/taluka-relation-field',[TalukaController::class,'getRelationValues'])->name('taluka-relation-field');
    Route::get('/unit-list',[UnitController::class,'existsUnit'])->name('unit-list');

    Route::get('/country-list',[CountryController::class,'existsCountry'])->name('country-list');
    Route::get('/mis-cat_list',[MisCategoryController::class,'existsMisCategory'])->name('mis-cat_list');
    Route::get('/village-list',[VillageController::class,'existsVillage'])->name('village-list');
    Route::get('/item-list',[ItemController::class,'existsVillage'])->name('item-list');
    Route::get('/taluka-list',[TalukaController::class,'existsTaluka'])->name('taluka-list');
    Route::get('/raw-material-list',[RawMaterialController::class,'existsRawMaterial'])->name('item-raw-material');
    Route::get('/transporter-list',[TransporterController::class,'existsTransporter'])->name('transporter-list');
    Route::get('/item-group-list',[ItemGroupController::class,'existsItemGroup'])->name('item-group-list');
    Route::get('/item-list',[ItemController::class,'existsItem'])->name('item-list');
    Route::get('/group-code-list',[ItemGroupController::class,'existsGroupCode'])->name('group-code-list');
    Route::get('/item-raw-material-group',[RawMaterialGroupController::class,'existsRawMaterial'])->name('raw-material-group-list');
    Route::get('/customer-group-list',[CustomerGroupController::class,'existsCustomerGroup'])->name('customer-group-list');
    Route::get('/state-list',[StateController::class,'existsState'])->name('state-list');
    Route::get('/city-list',[CityController::class,'existsCity'])->name('city-list');
    Route::get('/customer-list',[CustomerController::class,'existsCustomer'])->name('customer-list');
    Route::get('/dealer-list',[DealerController::class,'existsDealer'])->name('dealer-list');
    Route::get('/dealer-code_list',[DealerController::class,'existsDealerCode'])->name('dealer-code_list');
    Route::get('/supplier-code_list',[SupplierController::class,'existsSupplierCode'])->name('supplier-code_list');

    Route::get('/customer-list_report',[SuggestReportController::class,'existscustomer'])->name('customer-list_report');
    Route::get('/so_number-list_report',[SuggestReportController::class,'existssonumber'])->name('so_number-list_report');
    Route::get('/dp_number-list_report',[SuggestReportController::class,'existsdpnumber'])->name('dp_number-list_report');
    Route::get('/mr_number-list_report',[SuggestReportController::class,'existsmrnumber'])->name('mr_number-list_report');
    Route::get('/po_number-list_report',[SuggestReportController::class,'existsponumber'])->name('po_number-list_report');
    Route::get('/pr_number-list_report',[SuggestReportController::class,'existsprnumber'])->name('pr_number-list_report');
    Route::get('/order_by-list_report',[SuggestReportController::class,'existsorderby'])->name('order_by-list_report');
    Route::get('/ip_number-list_report',[SuggestReportController::class,'existsipnumber'])->name('ip_number-list_report');
    Route::get('/issue_number-list_report',[SuggestReportController::class,'existsissuenumber'])->name('issue_number-list_report');
    Route::get('/issue_no-list_report',[SuggestReportController::class,'existsissueno'])->name('issue_no-list_report');
    Route::get('/return_number-list_report',[SuggestReportController::class,'existsreturnnumber'])->name('return_number-list_report');
    Route::get('/grn_no-list_report',[SuggestReportController::class,'existsgrnno'])->name('grn_no-list_report');

    Route::get('/payment_terms-list',[CustomerController::class,'existsPaymentTerms'])->name('payment_terms-list');
    Route::get('/customer-designation-list',[CustomerController::class,'existsDesignation'])->name('customer-designation-list');
    Route::get('/customer-relation-field',[CustomerController::class,'getRelationValues'])->name('customer-relation-field');

    Route::get('/ordre_by-list',[PurchaseOrderController::class,'existsOrderBy'])->name('ordre_by-list');
    Route::get('/prepared_by-list',[PurchaseOrderController::class,'existsPreparedBy'])->name('prepared_by-list');

    Route::get('fetchItemCode', [ItemAssemblyProductionController::class,'fetchItemCode'])->name('fetchItemCode');


    Route::get('/city-relation-field',[CityController::class,'getRelationValues'])->name('city-relation-field');
    Route::get('/village-relation-field',[VillageController::class,'getRelationValues'])->name('village-relation-field');
    Route::get('/getRelationDistrict',[VillageController::class,'getRelationDistrict'])->name('getRelationDistrict');
    Route::get('/getTaluka',[VillageController::class,'getTaluka'])->name('getTaluka');
    Route::get('/get-talukas/{id}',[TalukaController::class,'TalukaData'])->name('get-talukas');
    Route::get('/fetch-talukas',[TalukaController::class,'fetchTalukaData'])->name('fetch-talukas');
    Route::get('/get-location/{id}',[LocationController::class,'LocationData'])->name('get-location');
    Route::get('/get-supplier/{id}',[SupplierController::class,'SupplierData'])->name('get-supplier');
    Route::get('/customer-country_relation-field',[CustomerController::class,'getCutomerRelationValue'])->name('customer-country_relation-field');


    Route::get('/customer-relation-field',[CustomerController::class,'getRelationValues'])->name('customer-relation-field');




    Route::get('/hsn_code-list',[HsnCodeController::class,'existsHsnCode'])->name('hsn_code-list');

    Route::get('/supplier-relation-field',[SupplierController::class,'getRelationValues'])->name('supplier-relation-field');

    Route::get('/get-location_for_mr',[MaterialRequestController::class,'getLocationForMR'])->name('get-location_for_mr');
    Route::get('/get-location_for_pr',[MaterialRequestController::class,'getLocationForPR'])->name('get-location_for_pr');

    Route::get('/prepared_by_for_pr-list',[PurchaseRequisitionController::class,'existsPreparedBy'])->name('prepared_by_for_pr-list');

    Route::get('/rejection_reason_for_qc-list',[QCApprovalController::class,'existsRejectionReason'])->name('rejection_reason_for_qc-list');
    /**
     * Get all data without filter
     */



    Route::get('/get-countries',[CountryController::class,'countryData'])->name('get-countries');
    Route::get('/get-customer-groups',[CustomerGroupController::class,'customerGroupData'])->name('get-customer-groups');
    Route::get('/get-states',[StateController::class,'stateData'])->name('get-states');
    Route::get('/get-cities',[CityController::class,'cityData'])->name('get-cities');
    Route::get('/get-villagedata',[VillageController::class,'villageData'])->name('get-villagedata');
    Route::get('/get-item',[ItemController::class,'itemData'])->name('get-item');
    Route::get('/get-raw-material',[RawMaterialController::class,'rawMaterialData'])->name('get-raw-material');
    Route::get('/get-transporter',[TransporterController::class,'transportData'])->name('get-transporter');
    Route::get('/get-item-group',[ItemGroupController::class,'itemGroupData'])->name('get-item-group');
    Route::get('/get-customers',[CustomerController::class,'customerData'])->name('get-customers');
    Route::get('/get-company_years',[CompanyYearController::class,'companyYearData'])->name('get-company_years');
    Route::get('/get-po',[POShortCloseController::class,'getPOData'])->name('get-po');
    Route::get('/get-pr',[PRShortCloseController::class,'getPRData'])->name('get-pr');
    Route::get('/get-soData',[DispatchPlanController::class,'getSOData'])->name('get-soData');
    Route::get('/get-so_item_list-dispatch',[DispatchPlanController::class,'getSODetailData'])->name('get-so_item_list-dispatch');
    Route::get('/get-so_part_data-dispatch',[DispatchPlanController::class,'getSOPartData'])->name('get-so_part_data-dispatch');


    Route::get('/get-hsn_codes',[HsnCodeController::class,'HsnCodeData'])->name('get-hsn_codes');

    Route::get('/get-so_short_close_data',[SOShortCloseController::class,'getSOData'])->name('get-so_short_close_data');
    Route::post('/get-transaction_so_short_close',[TransactionSOShortCloseController::class,'getTransactionSOShortData'])->name('get-transaction_so_short_close');
    Route::get('/get-dealers',[DealerController::class,'DealerData'])->name('get-dealers');
    Route::get('/get-mis_category_list',[MisCategoryController::class,'misCategoryData'])->name('get-mis_category_list');




    /**
     * Excel Routes
     */
    Route::get('/export-Country',[CountryController::class,'exportCountry'])->name('export-Country');
    Route::get('/export-State',[StateController::class,'exportState'])->name('export-State');
    Route::get('/export-City',[CityController::class,'exportCity'])->name('export-City');
    Route::get('/export-Village',[VillageController::class,'exportVillage'])->name('export-Village');
    Route::get('/export-customer-group',[CustomerGroupController::class,'exportCustomerGroup'])->name('export-customer-group');
    Route::get('/export-Customer',[CustomerController::class,'exportCustomer'])->name('export-Customer');
    Route::get('/export-Supplier',[SupplierController::class,'exportSupplier'])->name('export-Supplier');
    Route::get('/export-Transporter',[TransporterController::class,'exportTransporter'])->name('export-Transporter');

    Route::get('/export-HsnCode',[HsnCodeController::class,'exportHsnCode'])->name('export-HsnCode');
    Route::get('/export-PriceList',[PriceListController::class,'exportPriceList'])->name('export-PriceList');
    Route::get('/export-Item-to-Item-Mapping',[ItemRawMaterialMappingController::class,'exportItemtoItemMapping'])->name('export-Item-to-Item-Mapping');
    Route::get('/export-Dealer',[DealerController::class,'exportDealer'])->name('export-Dealer');

    Route::get('/export-Item_Production_Assembly_Consumption',[ItemAssemblyProductionController::class,'exportItemProductionAssemblyConsumption'])->name('export-Item_Production_Assembly_Consumption');

    Route::get('/export-SM_Approval',[ApprovalReportController::class,'exportSMApproval'])->name('export-SM_Approval');
    Route::get('/export-ZSM_Approval',[ApprovalReportController::class,'exportZSMApproval'])->name('export-ZSM_Approval');
    Route::get('/export-State_Coordinator_Approval',[ApprovalReportController::class,'exportStateCoordinatorApproval'])->name('export-State_Coordinator_Approval');
    Route::get('/export-GM_Approval',[ApprovalReportController::class,'exportGMApproval'])->name('export-GM_Approval');

    Route::get('/export-SupplierRejChallan',[SupplierRejectionController::class,'exportSupplierRejChallan'])->name('export-SupplierRejChallan');



    Route::get('/export-Taluka',[TalukaController::class,'exportTaluka'])->name('export-Taluka');

    Route::get('/export-Item',[ItemController::class,'exportItem'])->name('export-Item');
    Route::get('/export-village',[VillageController::class,'exportVillage'])->name('export-village');



    Route::get('/constants',[ConstantController::class,'index'])->name('constants');
    Route::post('/specific-constant',[ConstantController::class,'specific'])->name('specific-constant');



    Route::get('/get-location-states',[LocationController::class,'getStates'])->name('get-location-states');
    Route::get('/get-district',[LocationController::class,'getLocationDistrict'])->name('get-district');
    Route::get('/get-taluka',[LocationController::class,'getTaluka'])->name('get-taluka');
    Route::get('/get-village',[LocationController::class,'getVillage'])->name('get-village');
    Route::get('/get-unitData',[UnitController::class,'getUnitData'])->name('get-unitData');

    Route::get('/get-dist',[SupplierController::class,'getSupplierDistrict'])->name('get-dist');

    Route::get('/get-supplier-name',[SupplierController::class,'getSupplierName'])->name('get-supplier-name');

    Route::get('/get-location-name',[LocationController::class,'getLocationName'])->name('get-location-name');

    Route::get('/get-location-code',[LocationController::class,'getLocationCode'])->name('get-location-code');

    Route::get('/fetch_Groupname_Code_Unit',[ItemRawMaterialMappingController::class,'fetch_Groupname_Code_Unit'])->name('fetch_Groupname_Code_Unit');

    Route::get('/get-latest-customer-code',[CustomerController::class,'getCustomerCode'])->name('get-latest-customer-code');


    Route::get('/get-po_part_data-grn',[GRNMaterialController::class,'getPoPartDataForGrn'])->name('get-po_part_data-grn');

    Route::get('/get-dc_part_data-grn',[GRNMaterialController::class,'getDcPartDataForGrn'])->name('get-dc_part_data-grn');

    Route::post('/getUserPremissionData',[UserAccessController::class,'getUserPremissionData'])->name('getUserPremission');

    Route::get('getStockQty', [PriceListController::class,'getStockQty'])->name('getStockQty');

    Route::get('getSupplierItems', [SupplierItemMappingController::class,'getSupplierItems'])->name('getSupplierItems');

    Route::get('/get-pr_part_data-po',[PurchaseOrderController::class,'getPrPartDataForPo'])->name('get-pr_part_data-po');

    Route::get('/get-grn_data-qc',[QCApprovalController::class,'getGrnDataForQc'])->name('get-grn_data-qc');

    Route::get('/get-grn_part_data-qc',[QCApprovalController::class,'getGrnPartDataForQc'])->name('get-grn_part_data-qc');

    Route::get('/get-qc_part_data-src',[SupplierRejectionController::class,'getQCPartDataForSrc'])->name('get-qc_part_data-src');


    Route::get('/get-supplier_code',[SupplierController::class,'getSupplierCode'])->name('get-supplier_code');
    Route::get('/get-dealer_code',[DealerController::class,'getDealerCode'])->name('get-dealer_code');
    Route::get('/get-so_customer_sub',[SalesReturnController::class, 'getCustomerSubsidy'])->name('get-so_customer_sub');
    Route::get('/get-so_customer_cash_carry',[SalesReturnController::class, 'getCustomerCashCarry'])->name('get-so_customer_cash_carry');
    Route::get('/get-sales_order_all_customer',[SalesReturnController::class, 'getSalesOrderAllCustomer'])->name('get-sales_order_all_customer');




      // check duplication verification for master
      Route::controller(DuplicationVerificationController::class)->group(function(){
        Route::get('/verify-city-data', 'verifyCityData')->name('verify-city-data');
        Route::get('/verify-country', 'verifyCountry')->name('verify-country');
        Route::get('/verify-state-data', 'verifyState')->name('verify-state-data');
        Route::get('/verify-gst-data', 'verifyGst')->name('verify-gst-data');
        Route::get('/verify-customer', 'verifyCustomer')->name('verify-customer');
        Route::get('/verify-dealer', 'verifyDealer')->name('verify-dealer');
        Route::get('/verify-customer-group', 'verifyCustomerGroup')->name('verify-customer-group');
        Route::get('/verify-unit', 'verifyUnit')->name('verify-unit');
        Route::get('/verify-taluka', 'verifyTalukaData')->name('verify-taluka');
        Route::get('/verify-village', 'verifyVillage')->name('verify-village');
        Route::get('/verify-location', 'verifyLocation')->name('verify-location');
        Route::get('/verify-transporter', 'verifyTransporter')->name('verify-transporter');
        Route::get('/verify-hsn_code', 'verifyHSNCode')->name('verify-hsn_code');
        Route::get('/verify-unit', 'verifyUnit')->name('verify-unit');
        Route::get('/verify-item_group', 'verifyItemGroup')->name('verify-item_group');
        Route::get('/verify-item_group_code', 'verifyItemGroupCode')->name('verify-item_group_code');
        Route::get('/verify-item', 'verifyItem')->name('verify-item');
        Route::get('/verify-supplier-name', 'verifySupplierName')->name('verify-supplier-name');
        Route::get('/verify-mis_category', 'verifyMisCategory')->name('verify-mis_category');
        Route::get('/verify-dealer_code', 'verifyDealerCode')->name('verify-dealer_code');
        Route::get('/verify-supplier_code', 'verifySupplierCode')->name('verify-supplier_code');



        // Trnasction Duplication Check Controller


        Route::get('/check-so_no_duplication', 'checkSalesSequnceDuplication')->name('check-so_no_duplication');
        
        Route::get('/check-sr_no_duplication', 'checkSalesReturnSequnceDuplication')->name('check-sr_no_duplication');

        Route::get('/check-po_no_duplication', 'checkPurchaseSequnceDuplication')->name('check-po_no_duplication');

        Route::get('/check-src_no_duplication', 'checkSupplierSequnceDuplication')->name('check-src_no_duplication');

        Route::get('/check-item_issue_no_duplication', 'checkItemIssueSequnceDuplication')->name('check-item_issue_no_duplication');

        Route::get('/check-grn_no_duplication', 'checkGrnSequnceDuplication')->name('check-grn_no_duplication');

        Route::get('/check-ip_no_duplication', 'checkItemProductionSequnceDuplication')->name('check-ip_no_duplication');

        Route::get('/check-iap_no_duplication', 'checkItemAssmProductionSequnceDuplication')->name('check-iap_no_duplication');

        Route::get('/check-item_return_no_duplication', 'checkItemReturnSequnceDuplication')->name('check-item_return_no_duplication');

        Route::get('/check-material_request', 'checkMaterialNumber')->name('check-material_request');


        Route::get('/check-dispatch_no_duplication', 'checkDispatchSequnceDuplication')->name('check-dispatch_no_duplication');

        Route::get('/check-cre_no_duplication', 'checkCRESequnceDuplication')->name('check-cre_no_duplication');

        Route::get('/check-mapping_no_duplication', 'checkMappingSequnceDuplication')->name('check-mapping_no_duplication');

        Route::get('/check-purchase_requisition', 'checkPurchaseRequisitionDuplication')->name('check-purchase_requisition');


        Route::get('/check-qc_approval', 'checkQCApprovalDuplication')->name('check-qc_approval');

        Route::get('/check-quot_no_duplication', 'checkQuotationlDuplication')->name('check-quot_no_duplication');

    });


        Route::get('/check-po_part_in_use',[PurchaseOrderController::class,'isPartInUse'])->name('check-po_part_in_use');

        Route::get('/check-issue_part_in_use',[ItemIssueController::class,'isPartInUse'])->name('check-issue_part_in_use');

        Route::get('/check-dp_part_in_use',[LoadingEntryController::class,'isPartInUse'])->name('check-dp_part_in_use');

        Route::get('/check-mr_part_in_use',[SaleOrderController::class,'isPartInUse'])->name('check-mr_part_in_use');

        Route::get('/check-so_part_in_use',[DispatchPlanController::class,'isPartInUse'])->name('check-so_part_in_use');

        Route::get('get-villageData', [CustomerController::class,'getVillageData'])->name('getVillageData');

        Route::get('checkUserExists',[LocationController::class,'checkUserExists'])->name('checkUserExists');


        Route::get('importview', [TalukaController::class, 'importview']);
        Route::post('import', [TalukaController::class, 'importTaluka'])->name('import');

        Route::get('importview_dealer', [DealerController::class, 'importviewDealer']);
        Route::post('import_dealer', [DealerController::class, 'importDealer'])->name('import_dealer');

    // end master duplication verification


     /**
     * Print Routes
     */


     Route::get('/print-sales_order/{id?}',[PrintSalesOrderController::class,'printSalesOrder'])->name('print-sales_order');
     Route::get('/print-pendding_sales_order/{id?}',[PrintPendingSoForDispatchSoWiseController::class,'printSalesOrder'])->name('print-pendding_sales_order');
     Route::get('/print-purchase_order/{id?}',[PrintPurchaseOrderController::class,'printPurchaseOrder'])->name('print-purchase_order');
     Route::get('/print-dispatch_plan/{id?}',[PrintDispatchPlanController::class,'printDispatchPlan'])->name('print-dispatch_plan');
     Route::get('/print-supplier_rej_challan/{id?}',[PrintSupplierReturnChallanController::class,'printSupplierReturnChallan'])->name('print-supplier_rej_challan');
     Route::get('/print-grn_details/{id?}',[PrintGRNController::class,'printGRN'])->name('print-grn_details');
     Route::get('/print-farmer_dispatch_plan/{id?}',[PrintFarmerDispatchPlanController::class,'printFarmerDispatchPlan'])->name('print-farmer_dispatch_plan');
     Route::get('/print-report',[ReportController::class,'printItemStockReport'])->name('print-report');

     Route::get('/print-sales_order_fitting/{id?}',[PrintSalesOrderFittingController::class,'printSalesOrderFittingItem'])->name('print-sales_order_fitting');

     Route::get('/print-material_request/{id?}',[PrintMaterialRequestController::class,'printMaterialRequest'])->name('print-material_request');

     Route::get('/print-farmer_wise_total_dispatch_plan/{id?}',[PrintFarmerWiseTotalDispatchPlanController::class,'printFarmerWiseTotalDispatchPlan'])->name('print-farmer_wise_total_dispatch_plan');

     Route::get('/print-loading_entry/{id?}',[PrintLoadingEntryController::class,'printLoadingEntry'])->name('print-loading_entry');

     Route::get('/print-purchase_requisition/{id?}',[PrintPurchaseRequisitionController::class,'printPurchaseRequisition'])->name('print-purchase_requisition');

     Route::get('/print-quotation/{id?}',[PrintQuotationController::class,'printQuotation'])->name('print-quotation');

     Route::get('/print-sales_return/{id?}',[PrintSalesReturnController::class,'printSalesReturn'])->name('print-sales_return');

    Route::get('/print-grn_location/{id?}',[PrintGRNLocationController::class,'printGRNLocation'])->name('print-grn_location');

     Route::get('update_item_code', [ItemController::class,'updatedItemCode'])->name('update_item_code');
     
    //  Route::get('create_auto_second_code', [ItemIssueController::class,'createAutoSecondIssue'])->name('create_auto_second_code');
          // Route::get('create_auto_second_code/{id?}', [ItemIssueController::class,'createAutoSecondIssue'])->name('create_auto_second_code');

     Route::get('/clear-cache', function() {
      \Illuminate\Support\Facades\Artisan::call('view:clear');
      \Illuminate\Support\Facades\Artisan::call('cache:clear');
      \Illuminate\Support\Facades\Artisan::call('route:clear');
      \Illuminate\Support\Facades\Artisan::call('config:cache');
      \Illuminate\Support\Facades\Artisan::call('config:clear');
      return "Done";
    });

    Route::get('/check-file_exists/{id?}/{name}/{type}',[CrystalReportController::class,'checkReportExists'])->name('check-file_exists');

    
// Route::get('/check-file_exists',[PrintReportsController::class,'checkReportExists'])->name('check-file_exists');
Route::post('/merge_and_print_report',[PrintReportsController::class,'mergeReports'])->name('merge_and_print_report');
Route::post('/download_single_reports', [PrintReportsController::class, 'downloadSingleMptReports'])->name('download_single_reports');

     // wrong url then get the selectYear page
    // Route::any('{query}',
    // function() { return redirect('/selectLocation'); })
    // ->where('query', '.*');
    Route::any('{query}', function () {
      return redirect('/selectLocation');
    })->where('query', '^(?!storage).*');

     Route::get('/preview-puchase_order/{id?}',[PurchaseOrderController::class,'previewPurchseOrder'])->name('preview-puchase_order');
});