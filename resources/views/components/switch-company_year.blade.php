@extends('layouts.app',['pageTitle' => 'Switch Year'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Switch Year</li>
</ul>

@endsection

@section('content')

<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
       
        </div>
        <h4 class="widgettitle">Switch Year</h4>
    </div>
    <div class="widgetcontent">
        <form id="switchCompanyYearForm" class="stdform" method="post">
            @csrf
                <div class="row">
                    <div class="span-6">
                        <div class="par control-group form-control">
                                 <label class="control-label" for="company_year">Select Company Year </label>
                                    <span class="formwrapper">
                                        <div class="controls">
                                            <select class="form-control chzn-select" data-placeholder="Select Company Year" id="company_year" name="company_year" autofocus>
                                                <option value=""></option>
                                                @forelse ($company_years as $company_year)
                                                    <option value="{{ $company_year->id }}" {{ $company_year['id'] == session('default_year_id') ? 'selected=selected' : "" }}>{{ $company_year->year }}</option>
                                                    @empty
                                                @endforelse  
                                            </select>
                                        </div>
                                    </span>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="span-6">            
                        <div class="par control-group form-control">
                            <label class="control-label"></label>
                            <div class="controls">
                                    <span class="formwrapper"> 
                                        <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
            
                </div>


                {{-- <p class="stdformbutton">
                    <button class="btn btn-primary">Update</button>
                </p> --}}
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
@endsection

@section('scripts')
<script>

jQuery(document).ready(function(){
    setTimeout(() => {
            jQuery('#company_year').trigger('liszt:activate');
        }, 100);

var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}'};

var validator = jQuery("#switchCompanyYearForm").validate({
		rules: {
            company_year: {
                required: true
            }			
		},
		messages: {
            company_year: {
                required: "Please select company year"
            }
		},
        submitHandler: function(form) {
            var formdata = jQuery('#switchCompanyYearForm').serialize();
            jQuery.ajax({
                url: "{{ route('change-company_year') }}",
                type: 'POST',
                data: formdata,
                headers:headerOpt,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    if(data.response_code == 1){                            
                        toastSuccess(data.response_message,redirectFn);
                        function redirectFn(){
                            window.location.href = RouteBasePath+"/dashboard";
                        }                  
                    }else{
                        jAlert(data.response_message);
                    }   
                },
                error: function (jqXHR, textStatus, errorThrown){
                    var errMessage = JSON.parse(jqXHR.responseText);
                
                    if(errMessage.errors){
                        validator.showErrors(errMessage.errors);
                        
                    }else if(jqXHR.status == 401){
        
                        jAlert(jqXHR.statusText);
                    }else{
    
                        jAlert('Something went wrong!');
                        console.log(JSON.parse(jqXHR.responseText));
                    }
                }
            });
        }
	});

});
</script>
@endsection