@extends('layouts.login')

@section('content')

<div class="loginpanel">
    <div class="loginpanelinner">
        {{-- <div class="logo animate0 bounceIn"><h3>BHUMI IRRIGATION</h3></div> --}}
        <a href="{{ url('/') }}"><img src="{{asset('images/logo/bhumi_logo.jpg')}}"  alt="Atlanta"></a> 
        <form id="login" action="{{ route('login') }}" method="post">
            @csrf
                @error('wrong_details')
                    <div class="inputwrapper">
                        <div class="alert alert-danger w-220">{{ $message }}</div>
                    </div>
                @enderror
            <div class="inputwrapper animate1 bounceIn">
                <input type="text" name="user_name" id="user_name" placeholder="Enter User Name" autofocus/>
                @error('user_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="inputwrapper animate2 bounceIn">
                <input type="password" name="password" id="password" placeholder="Enter Password" />
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="inputwrapper animate3 bounceIn">
                <button name="submit">LogIn</button>
            </div>
            {{-- <div class="inputwrapper animate4 bounceIn">
                <label><input class="remember" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Keep me logged in</label>
            </div> --}}
            
        </form>
    </div><!--loginpanelinner-->
</div><!--loginpanel-->
@endsection

@section('scripts')
<script>
jQuery("#login").validate({
    rules: {
        user_name: "required",
        password: "required"			
    },
    messages: {
        user_name: "Please enter username",
        password: "Password is required"
    },
    errorPlacement: function(error, element) {
        jQuery('.cuterror-'+element.attr('name')).remove();
        let closest_div = element.closest('div');
        let custom_error = `<div class="inputwrapper text-white cuterror-${element.attr('name')}">${error.text()}</div>`;
        jQuery(custom_error).insertAfter(closest_div);
           
    },
    highlight: function(label) {
        jQuery(label).closest('.control-group').addClass('error');
    },
    success: function(label) {
        label.remove();
    }
});
</script>
@endsection