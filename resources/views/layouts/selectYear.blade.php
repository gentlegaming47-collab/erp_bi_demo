@extends('layouts.login')

@section('content')

<div class="loginpanel">
    <div class="loginpanelinner">
        
        <a href="{{ route('selectLocation') }}"><img src="{{asset('images/logo/bhumi_logo.jpg')}}"  alt="Atlanta"></a> 
        <form id="selectYear" action="{{ route('getUserPremission') }}" method="post">
            @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">                       

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
               @endif
           
            
                @php
                if(Auth::id() != 1)
                {
                    $getLocations =  App\Models\Location::join('user_locations', 'user_locations.company_unit_id', 'locations.id')->where('user_locations.user_id', Auth::id())->select('user_locations.company_unit_id', 'locations.id', 'locations.location_name')->orderBy('locations.location_name', 'Asc')->distinct('location_name')->get();
                }else{
                    $getLocations = App\Models\Location::orderBy('locations.location_name', 'Asc')->get();
                }
            @endphp
            

            

                {{-- <div class="col-sm-12">                     --}}
                    <div class="inputwrapper animate1 bounceIn">
                             
                    <select name="user_location_id" id="user_location_id"  class="locationSelectDroipDown">    
                        @if(count($getLocations) > 1) 

                        <option value="">Select Location...</option>
                        
                        @endif
                    @foreach ($getLocations as $getLocationsData)    
                     
                     <option value="{{ $getLocationsData->id }}">{{ $getLocationsData->location_name }}</option>


                  
                     @endforeach
                 </select>     
                </div>                           
            {{-- </div> --}}
         

            <div class="inputwrapper animate3 bounceIn">
                <button name="submit">LogIn</button>
            </div>
            {{-- <div class="inputwrapper animate4 bounceIn tohide">
                <label><input class="remember" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Keep me logged in</label>
            </div> --}}
            
        </form>
    </div><!--loginpanelinner-->
</div><!--loginpanel-->
@endsection

@section('scripts')
<script>
jQuery("#selectYear").validate({
    rules: {
        user_location_id: "required",        
    },
    messages: {
        user_location_id: "Please Select User Location",
        
    },
    errorPlacement: function(error, element) {
        jQuery('.cuterror-'+element.attr('user_location_id')).remove();
        let closest_div = element.closest('div');
        let custom_error = `<div class="inputwrapper text-white cuterror-${element.attr('user_location_id')}">${error.text()}</div>`;
        jQuery(custom_error).insertAfter(closest_div);
           
    },
    highlight: function(label) {
        jQuery(label).closest('.control-group').addClass('error');
    },
    success: function(label) {
        label.remove();
    }
});

//function DisbledKeepMeLogIn(){
//    jQuery(document).on('change','#user_location_id',function(){
        //jQuery('.tohide').hide();
    //});
//}
</script>
@endsection