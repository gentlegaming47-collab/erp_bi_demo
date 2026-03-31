<?php



namespace App\Http\Controllers;



use App\Models\Unit;

use App\Models\Admin;

use App\Models\Item;

use App\Models\UserUnit;

use Illuminate\Http\Response;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

use Carbon\Carbon;

use DataTables;

use Date;



class UnitController extends Controller

{

    /**

     * Return all company data without filter

     */

    public function unitData($forBlade = false)

    {

        if(Auth::user()->id != 1){

            $usunits = UserUnit::where('user_id','=',Auth::user()->id)->get();

        

            if($usunits != null){

                

                $unit_ids = [];

                

                foreach($usunits as $unkey => $unval){

                    array_push($unit_ids,$unval->company_unit_id);

                }

                

                $units = Unit::whereIn('id',$unit_ids)->orderBy('company_unit_name', 'ASC')->get();

                

                if($units){

                    

                    if($forBlade == false){

                        return response()->json([

                            'units' => $units,

                            'response_code' => '1'

                        ]);

                    }else{

                        return $units;

                    }

                }else{

                    if($forBlade == false){

                        return response()->json([

                            'response_code' => '0',

                            'response_message' => 'No Data Avilable',

                        ]);

                    }else{

                        return [];

                    }

                }

              

            }else{

                if($forBlade == false){

                    return response()->json([

                        'response_code' => '0',

                        'response_message' => 'No Data Avilable',

                    ]);

                }else{

                    return [];

                }

            }

        }else{

            $units = Unit::orderBy('company_unit_name', 'ASC')->get();

            

            if($units){

                if($forBlade == false){

                    return response()->json([

                        'units' => $units,

                        'response_code' => '1'

                    ]);

                }else{

                    return $units;

                }

            }else{

                if($forBlade == false){

                    return response()->json([

                        'response_code' => '0',

                        'response_message' => 'No Data Avilable',

                    ]);

                }else{

                    return [];

                }

            }

        }

        

        

        

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function manage()

    {

        return view('manage.manage-unit');

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Unit $Unit,Request $request,DataTables $dataTables)

    {

        $unit_data = Unit::select(['units.unit_name','units.id','units.created_by_user_id','units.created_on','units.last_by_user_id','units.last_on','created_user.user_name as created_by_name','last_user.user_name as last_by_name'])
        ->leftJoin('admin AS created_user', 'created_user.id', '=', 'units.created_by_user_id')
        ->leftJoin('admin AS last_user', 'last_user.id', '=', 'units.last_by_user_id');



        return DataTables::of($unit_data)
        
        ->editColumn('created_by_user_id', function($country_data){ 
            if($country_data->created_by_user_id != null){
                $created_by_user_id = Admin::where('id','=',$country_data->created_by_user_id)->first('user_name'); 
                return isset($created_by_user_id->user_name) ? $created_by_user_id->user_name : '';
            }else{
                return '';
            }
        })
        ->filterColumn('created_by_user_id', function ($query, $keyword) {
            $query->where('created_user.user_name', 'like', "%{$keyword}%");
        })

        ->editColumn('last_by_user_id', function($unit_data){ 
            if($unit_data->last_by_user_id != null){
                $last_by_user_id = Admin::where('id','=',$unit_data->last_by_user_id)->first('user_name'); 
                return isset($last_by_user_id->user_name) ? $last_by_user_id->user_name : '';
            }else{
                return '';
            }
        
        })
        ->filterColumn('last_by_user_id', function ($query, $keyword) {
            $query->where('last_user.user_name', 'like', "%{$keyword}%");
        })


        ->editColumn('unit_name', function($unit_data){ 
            if($unit_data->unit_name != ''){
                $company_unit_name = ucfirst($unit_data->unit_name);
                // dd($company_unit_name);
                return $company_unit_name;
            }else{
                return '';
            }

            //return Str::limit($unit_data->company_unit_name, 50);

        })

        ->editColumn('created_on', function($unit_data){ 

            if ($unit_data->created_on != null) { 

                $formatedDate1 = Date::createFromFormat('Y-m-d H:i:s', $unit_data->created_on)->format(DATE_TIME_FORMAT); return $formatedDate1; 

            }else{

                return '';

            }

        })
        ->filterColumn('units.created_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(units.created_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->editColumn('last_on', function($unit_data){ 

            if ($unit_data->last_on != null) {

                $formatedDate2 = Date::createFromFormat('Y-m-d H:i:s', $unit_data->last_on)->format(DATE_TIME_FORMAT); return $formatedDate2; 

            }else{

                return '';

            }

        })
        ->filterColumn('units.last_on', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(units.last_on, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"]);
        })

        ->addColumn('options',function($unit_data){ 
            $action = "<div>";
            // if($unit_data->id != 1){
                if(hasAccess("unit","edit")){
                $action .="<a id='edit_a' href='".route('edit-unit',['id' => base64_encode($unit_data->id)]) ."' data-placement='top' data-original-title='Edit' title='Edit'><i class='iconfa-pencil action-icon'></i></a>";
                }
            // }
            // if($unit_data->id != 1){
                if(hasAccess("unit","delete")){
                $action .= "<i id='del_a' data-placement='top' data-original-title='Delete' title='Delete' class='iconfa-trash action-icon'></i>";
                }
            // }
            $action .= "</div>";
            return $action;
        })

      
        ->rawColumns(['created_by_user_id','created_on','last_by_user_id','last_on','unit_name','options'])

        ->make(true);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('add.add-unit');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $validated = $request->validate([

            'unit_name'=> 'required|max:255|unique:units',            

        ],

        [

            'unit_name.required' => 'Please enter unit',
            'unit_name.unique'       => 'The Unit Name Has Already Been Taken',    
            'company_unit_name.max' => 'Maximum 255 characters allowed',            

        ]);



        $unit_data=  Unit::create([

            'unit_name' => $request->unit_name,           

            'company_id' => Auth::user()->company_id,

            'created_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),

            'created_by_user_id' => Auth::user()->id

        ]);



        if($unit_data->save()){

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

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\Models\Unit  $unit

     * @return \Illuminate\Http\Response

     */

    public function show(Unit $unit,$id)

    {

        return view('edit.edit-unit')->with('id',$id);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Models\Unit  $unit

     * @return \Illuminate\Http\Response

     */

    public function edit(Unit $unit,Request $request,$id)

    {

        $unit_data = Unit::where('id','=',$id)->first();



        if($unit_data){

            return response()->json([

                'unit' => $unit_data,

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



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Models\Unit  $unit

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, Unit $unit)

    {

        $validated = $request->validate([

            'unit_name'=>['required','max:255',Rule::unique('units')->ignore($request->id, 'id')]
        ],

        [

            'unit_name.required' => 'Please enter unit',
            'unit_name.unique'       => 'The Unit Name Has Already Been Taken',    
            'unit_name.max' => 'Maximum 255 characters allowed',


        ]);



        $unit_data=  Unit::where('id','=',$request->id)->update([

            'unit_name' => $request->unit_name,

            'last_on' => Carbon::now('Asia/Kolkata')->toDateTimeString(),

            'last_by_user_id' => Auth::user()->id

        ]);



        if($unit_data){

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



    public function existsUnit(Request $request){

        if($request->term != ""){

            $fdUnit = Unit::select('unit_name')->where('unit_name', 'LIKE', $request->term.'%')->groupBy('unit_name')->get();

            if($fdUnit != null){

                // $output = [];



                // foreach($fdUnit as $dsKey){

                //     array_push($output ,$dsKey->unit);

                // } 

                $output = '<ul class="list-group" style="display: block; position: relative;" tabindex="-1">';

                foreach($fdUnit as $dsKey){



                    $output .= '<li parent-id="unit_name" list-id="unit_name" class="list-group-item" tabindex="0">'.$dsKey->unit_name.'</li>';

                } 

                $output .= '</ul>';



                return response()->json([

                    'unitList' => $output,

                    'response_code' => 1,

                ]);

            }else{

                return response()->json([

                    'response_message' => 'No Unit available',

                    'response_code' => 0,

                ]);

            }

        }else{

            return response()->json([

                'unitList' => '',

                'response_code' => 1,

            ]);

        }

    

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Models\Unit  $unit

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request)

    {
        DB::beginTransaction();
        try{

            $item_unit_id = Item::where('unit_id',$request->id)->get();
            if($item_unit_id->isNotEmpty()){ 
                return response()->json([
                    'response_code' => '0',
                    'response_message' => "You Can't Delete, Unit Is Used In Item.",
                ]);
            }
            Unit::destroy($request->id);
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

    public function getUnitData()
    {
        $unit = Unit::orderBy('unit_name', 'ASC')->get();

        if($unit)
        {
            return response()->json([
                'unit' => $unit,
                'response_code' => "1"
            ]);
        }else{
            return response()->json([
                'response_code' => '0',
                'response_message' => 'No Data Avilable',
            ]);
        }
    }


}