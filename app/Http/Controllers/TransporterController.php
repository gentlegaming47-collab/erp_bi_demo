<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Admin;
use App\Models\SupplierRejection;
use App\Models\GRNMaterial;
use App\Models\LoadingEntry;


use Illuminate\Http\Response;
// use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use Date;
use App\Models\Transporter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTransporter;

class TransporterController extends Controller
{
    public function transportData()
    {
        $transporer = Transporter::where('status','=','active')->orderBy('transporter_name', 'ASC')->get();
        if($transporer)
        {
            return response()->json([
                'transporer' => $transporer,
                'response_code' => '1'
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }
    }

    public function manage()
    {
        return view('manage.manage-transporter');
    }

    public function index(Transporter $transporter,Request $request,DataTables $dataTables)
    {
        $transporer = Transporter::select([
            'transporters.id',
            'transporters.transporter_name',
            'transporters.company_id',
            'transporters.created_by_user_id',
            'transporters.created_on',
            'transporters.last_by_user_id',
            'transporters.last_on',
            'transporters.pan',
            'transporters.gstin',
            'transporters.contact_person',
            'transporters.contact_person_mobile',
            'transporters.contact_person_email_id',
            'transporters.payment_terms',
            'transporters.status',
            'transporters.approval_status',
            'created_user.user_name as created_by_name',
            'last_user.user_name as last_by_name'
        ])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'transporters.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'transporters.last_by_user_id');

        return DataTables::of($transporer)
        ->editColumn('created_by_user_id', function($transporer){ 
            if($transporer->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$transporer->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('last_by_user_id', function($transporer){ 
            if($transporer->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$transporer->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })
        ->editColumn('transporter_name', function($transporer){ 
            if($transporer->transporter_name != ''){
                $transporter_name = ucfirst($transporer->transporter_name);
                return $transporter_name;
            }else{
                return '';
            }
            //return Str::limit($transporer->transporter_name, 50);
        })
        ->editColumn('approval_status', function($transporer){
            if($transporer->approval_status != ''){
                if($transporer->approval_status == 'deactive_approval_pending'){
                    $status = 'Deactive Approval Pending';
                }elseif($transporer->approval_status == 'approval_pending'){
                    $status = 'Active Approval Pending';
                }elseif($transporer->approval_status == 'active'){
                    $status = 'Active';
                }elseif($transporer->approval_status == 'deactive'){
                    $status = 'Deactive';
                }else{
                    $status = '';
                }
                // $status = ucfirst($transporer->status);
                return $status;
            }else{
                return '';
            }
        })
        ->filterColumn('transporters.approval_status', function ($query, $keyword) {
            $globalSearch = request()->input('search.value');
            $searchValue = $globalSearch != '' ? $globalSearch : $keyword;
            $lowerKeyword = strtolower(trim($searchValue));
            if (str_contains($lowerKeyword, 'deactive a')) {
                $searchStatus = 'deactive_approval_pending';
            } 
            elseif (str_contains($lowerKeyword, 'active a')) {
                $searchStatus = 'approval_pending'; 
            }
            elseif ($lowerKeyword === 'active') {
                $searchStatus = 'active';
            } 
            elseif ($lowerKeyword === 'deactive') {
                $searchStatus = 'deactive';
            } 
            if (!empty($searchStatus)) {
                $query->where('transporters.approval_status', '=', $searchStatus);
            } 
            else {
                $dbFormatKeyword = str_replace(' ', '_', $lowerKeyword);
                $query->where('transporters.approval_status', 'like', "$dbFormatKeyword%");
            }
        })
        ->editColumn('created_on', function($transporer){ 
            if ($transporer->created_on != null) { 
                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $transporer->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 
            }else{
                return '';
            }
        })
        ->filterColumn('transporters.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(transporters.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->editColumn('last_on', function($transporer){ 
            if ($transporer->last_on != null) {
                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $transporer->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 
            }else{
                return '';
            }
        })
        ->filterColumn('transporters.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(transporters.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })
        ->addColumn('options',function($transporer){ 
            $action = "<div>";
            // if($transporer->id != 1){
                if(hasAccess("transporter","edit")){
                $action .="<a id='edit_a' href='".route('edit-transporter',['id' => base64_encode($transporer->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($transporer->id != 1){
                if(hasAccess("transporter","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','transporter_name','options'])
        ->make(true);
    }

    public function create()
    {
        return view('add.add-transporter');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transporter_name'=>'required|max:255|unique:transporters',
        ],
        [
            'transporter_name.required' => 'Please enter Transporter',
            'transporter_name.unique'       => 'The Transporter Name Has Already Been Taken',    
            'transporter_name.max' => 'Maximum 255 characters allowed',
        ]);
        DB::beginTransaction();
        try{
            $transporter_data =  Transporter::create([
                'transporter_name' => $request->transporter_name,
                'address' => $request->address,
                'pan' => $request->pan,
                'gstin' => $request->gstin,
                'type_of_vehicle' => $request->type_of_vehicle,
                'contact_person' => $request->contact_person,
                'contact_person_mobile' => $request->contact_person_mobile,
                'contact_person_email_id' => $request->contact_person_email_id,
                'payment_terms' => $request->payment_terms,
                'status'             => 'approval_pending',
                'approval_status'=>'approval_pending',        
                'company_id' => Auth::user()->company_id,
                'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'created_by_user_id' => Auth::user()->id
            ]);

            DB::commit();
            if($transporter_data->save()){              
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Inserted Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Inserted',
                ]);
            }
        }catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Inserted',
                'original_error' => $e->getMessage()
            ]);
        }
    }

    public function show(Transporter $transporter, $id)
    {
        return view('edit.edit-transporter')->with('id',$id);
    }

    public function edit(Transporter $transporter,Request $request,$id)
    {
        
        $transporter_data = Transporter::where('id','=',$id)->first();

        if($transporter_data){
            return response()->json([
                'transporter_data' => $transporter_data,
                'response_code' => '1',
                'response_message' => '',
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Record Does Not Exists',
            ]);
        }
    }

    public function update(Request $request, Transporter $transporter)
    {
        $validated = $request->validate([
            'transporter_name'=>['required','max:255',Rule::unique('transporters')->ignore($request->id, 'id')],                        
        ],
        [
            'transporter_name.required' => 'Please enter Transporter',
            'transporter_name.unique'       => 'The Transporter Name Has Already Been Taken',    
            'transporter_name.max' => 'Maximum 255 characters allowed',            
        ]);

        if($request->status == $request->transporter_status){
            $approval_status =  $request->transporter_status;
        }else{
            $approval_status = $request->transporter_status == 'active' ?  'approval_pending' :  'deactive_approval_pending' ;
        }
      

        DB::beginTransaction();
        try{
            $transporter_data=  Transporter::where('id','=',$request->id)->update([
                'transporter_name' => $request->transporter_name,       
                'address' => $request->address,
                'pan' => $request->pan,
                'gstin' => $request->gstin,
                'type_of_vehicle' => $request->type_of_vehicle,
                'contact_person' => $request->contact_person,
                'contact_person_mobile' => $request->contact_person_mobile,
                'contact_person_email_id' => $request->contact_person_email_id,
                'payment_terms' => $request->payment_terms,        
                'status'             => $request->status,        
                'approval_status'             => $approval_status,        
                'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),
                'last_by_user_id' => Auth::user()->id
            ]);
            DB::commit();
            if($transporter_data){
                return response()->json([
                    'response_code' => '1',
                    'response_message' => 'Record Updated Successfully.',
                ]);
            }else{
                return response()->json([
                    'response_code' => '0',
                    'response_message' => 'Record Not Updated',
                ]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'response_code' => '0',
                'response_message' => 'Error Occured Record Not Updated',
                'original_error' => $e->getMessage()
            ]);  
        }
    }

    public function existsTransporter(Request $request){
        if($request->term != ""){
            $fdTransporter = Transporter::select('transporter_name')->where('transporter_name', 'LIKE', $request->term.'%')->groupBy('transporter_name')->get();
            if($fdTransporter != null){
                // $output = [];

                // foreach($fdState as $dsKey){
                //     array_push($output ,$dsKey->state);
                // }
                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';
                foreach($fdTransporter as $dsKey){

                    $output .= '<li parent-id="transporter_name" list-id="transporter_name_list" class="list-group-item" tabindex="0">'.$dsKey->transporter_name.'</li>';
                }
                $output .= '</ul>';

                return response()->json([
                    'transporterList' => $output,
                    'response_code' => 1,
                ]);
            }else{
                return response()->json([
                    'response_message' => 'No State available',
                    'response_code' => 0,
                ]);
            }
        }else{
            return response()->json([
                'menuList' => '',
                'response_code' => 1,
            ]);
        }

    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            
            $src_transporter_id = SupplierRejection::where('transporter_id',$request->id)->get();
            if($src_transporter_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Transporter Is Used In Supplier Return Challan.",
                ]);
            }

            $le_transporter_id = LoadingEntry::where('transporter_id',$request->id)->get();
            if($le_transporter_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Transporter Is Used In Loading Entry.",
                ]);
            }

            $grn_transporter_id = GRNMaterial::where('transporter_id',$request->id)->get();
            if($grn_transporter_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Transporter Is Used In GRN.",
                ]);
            }

            Transporter::destroy($request->id);
            DB::commit();
            return response()->json([
                'response_code' => '1',
                'response_message' => 'Record Deleted Successfully.',
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451){
                $error_msg = "This is used somewhere, you can't delete";
            }else{
                $error_msg = "Record Not Deleted";
            }
            return response()->json([
                'response_code' => '0',
                'response_message' => $error_msg,
            ]);
        }
    }

    public function exportTransporter(Request $request)
    {
        $searchData = [];
        $global = $request->input('global');
        $columns = $request->input('columns', []);

        if ($global && is_string($global)) {
            $searchData['global'] = trim($global);
        }
        if (is_array($columns)) {
            foreach ($columns as $idx => $val) {
                if (is_string($val) && strlen($val) <= 255) {
                    $searchData['columns'][$idx] = trim($val);
                }
            }
        }

        return Excel::download(new ExportTransporter($searchData), 'Transporter.xlsx');
    }
}