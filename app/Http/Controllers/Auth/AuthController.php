<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CompanyYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register ( Request $request ,Admin $admin){

        $fields = $request->validate([
            'user_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'required'
        ]);

        $user = Admin::create([
            'role_id' => $fields['role_id'],
            'user_name' => $fields['user_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'created_on' => Carbon::now()->toDateTimeString(),
            'created_by' => '1',
            'auth_token' => '',
            'last_on' => Carbon::now()->toDateTimeString(),
            'last_by' => '1',
            'locaked_on' => Carbon::now()->toDateTimeString(),
            'locked_by' => '1',
        ]);

        $response = [
            'user' => $user
        ];

        return response( $response, 201);
    }
    public function dashboard()
    {
        return view('dashboard');
    }
    public function login ( Request $request ,Admin $admin){
        
        $fields = $request->validate([
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('user_name', 'password');
        
        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            if(Auth::user()->status == 'active'){        

                $defYear = CompanyYear::select('id')->orderBy('sequence','desc')->first();
                
                 $user->save();
                session(['default_year_id' => $defYear->id]);

                $checkUserType = Auth::user()->user_type;
                
                return redirect('selectLocation')->withSuccess('Signed in');
                // if($checkUserType == "operator")
                // {
                //     return redirect('selectLocation')->withSuccess('Signed in');
                // }else{
                //     if($checkUserType == "state_manager"){
                //         return redirect(route('manage-sm_approval'));       
                //     }else if($checkUserType == "zonal_manager"){
                //         return redirect(route('manage-zsm_approval'));       
                //     }else if($checkUserType == "director"){
                //         return redirect(route('manage-md_approval'));       
                //     }
                //     // manage-sm_approval
                //     // return redirect(route('dashboard'));              
                // }
              
                    // return redirect('selectLocation')->withSuccess('Signed in');
            }else{
                
                Session::flush();
                Auth::logout();

                return redirect("login")->withErrors(['wrong_details' => 'Yor profile is not active, please contact Administrator']);
            }      
        }
       return redirect("login")->withErrors(['wrong_details' => 'Invalid username or password']);
    }
        
    

    public function logout( Request $request ){

        Session::flush();
        Auth::logout();
        
        return redirect('login');
    }

    public function selectYear()
    {
        return view('layouts.selectYear');
    }
    
}