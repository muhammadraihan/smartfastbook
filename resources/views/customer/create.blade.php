@extends('layouts.page')

@section('title', 'Customer Create')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/dropzone/dropzone.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Add New <span class="fw-300"><i>Customer </i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('customer.index')}}"><i class="fal fa-arrow-alt-left">
                        </i>
                        <span class="nav-link-text">Back</span>
                    </a>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="panel-tag">
                        Form with <code>*</code> can not be empty.
                    </div>
                    {!! Form::open(['route' => 'customer.store','method' => 'POST','class' =>
                    'needs-validation','novalidate', 'enctype' => 'multipart/form-data']) !!}
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('name','Nama Customer',['class' => 'required form-label'])}}
                        {{ Form::text('name',null,['placeholder' => 'Name Customer','class' => 'form-control '.($errors->has('name') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>  
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('telephone','Telephone',['class' => 'required form-label'])}}
                        {{ Form::text('telephone',null,['placeholder' => 'Telephone','class' => 'form-control '.($errors->has('telephone') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('telephone'))
                        <div class="invalid-feedback">{{ $errors->first('telephone') }}</div>
                        @endif
                    </div>  
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('alamat','Alamat',['class' => 'required form-label'])}}
                        {{ Form::text('alamat',null,['placeholder' => 'Alamat','class' => 'form-control '.($errors->has('alamat') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('alamat'))
                        <div class="invalid-feedback">{{ $errors->first('alamat') }}</div>
                        @endif
                    </div>                         
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('piutang','Piutang',['class' => 'required form-label'])}}
                        <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Rp.
                                    </span>
                                </div>
                        {{ Form::text('piutang',null,['placeholder' => '','class' => 'form-control '.($errors->has('piutang') ? 'is-invalid':''),'required', 'autocomplete' => 'off', 'data-inputmask' => "'alias': 'currency','prefix': ''"])}}
                        @if ($errors->has('piutang'))
                        <div class="invalid-feedback">{{ $errors->first('piutang') }}</div>
                        @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('status','Status',['class' => 'required form-label'])}}
                        {!! Form::select('status', array('piutang' => 'Piutang', 'lunas' => 'Lunas'), '',
                        ['id'=>'status','class'
                        => 'custom-select'.($errors->has('status') ? 'is-invalid':''), 'required'
                        => '', 'placeholder' => 'Select Status ...']) !!}
                        @if ($errors->has('status'))
                        <div class="invalid-feedback">{{ $errors->first('status') }}</div>
                        @endif
                    </div>
                <div
                    class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
                    <button class="btn btn-primary ml-auto" type="submit">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/dropzone/dropzone.js')}}"></script>
<script src="{{asset('js/formplugins/inputmask/inputmask.bundle.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#status').select2();
        $('#room_type').select2();
        $(':input').inputmask();
        $('#photo').change(function(){
            
            let reader = new FileReader();
         
            reader.onload = (e) => { 
         
              $('#preview-image-before-upload').attr('src', e.target.result); 
            }
         
            reader.readAsDataURL(this.files[0]); 
           
           });
        
        // Generate a password string
        function randString(){
            var chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNP123456789";
            var string_length = 8;
            var randomstring = '';
            for (var i = 0; i < string_length; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                randomstring += chars.substring(rnum, rnum + 1);
            }
            return randomstring;
        }
        
        // Create a new password
        $(".getNewPass").click(function(){
            var field = $('#password').closest('div').find('input[name="password"]');
            field.val(randString(field));
        });
        
    });
</script>
@endsection