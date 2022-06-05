@extends('layouts.page')

@section('title', 'E-Wallet Create')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/dropzone/dropzone.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Add New <span class="fw-300"><i>E-Wallet </i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('ewallet.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => 'ewallet.store','method' => 'POST','class' =>
                    'needs-validation','novalidate', 'enctype' => 'multipart/form-data']) !!}
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('customer','Nama Customer',['class' => 'required form-label'])}}
                        {{ Form::text('customer',null,['placeholder' => 'Name Customer','class' => 'form-control '.($errors->has('customer') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('customer'))
                        <div class="invalid-feedback">{{ $errors->first('customer') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('no_hp','No HP',['class' => 'required form-label'])}}
                        {{ Form::text('no_hp',null,['placeholder' => 'No HP','class' => 'form-control '.($errors->has('no_hp') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('no_hp'))
                        <div class="invalid-feedback">{{ $errors->first('no_hp') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('pemilik','Pemilik',['class' => 'required form-label'])}}
                        {{ Form::text('pemilik',null,['placeholder' => 'Pemilik','class' => 'form-control '.($errors->has('pemilik') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('pemilik'))
                        <div class="invalid-feedback">{{ $errors->first('pemilik') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('ewallet','Nama E-Wallet',['class' => 'required form-label'])}}
                        {!! Form::select('ewallet', $payment, '', ['id' =>
                        'ewallet','class' =>
                        'ewallet form-control'.($errors->has('ewallet') ? 'is-invalid':''), 'required'
                        => '', 'placeholder' => 'Pilih Nama E-Wallet']) !!} @if ($errors->has('ewallet'))
                        <div class="help-block text-danger">{{ $errors->first('ewallet') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('bank_uuid','Jenis Pembayaran',['class' => 'required form-label'])}}
                        {!! Form::select('bank_uuid', $bank, '', ['id' =>
                        'bank','class' =>
                        'bank form-control'.($errors->has('bank_uuid') ? 'is-invalid':''), 'required'
                        => '', 'placeholder' => 'Pilih Jenis Pembayaran']) !!} @if ($errors->has('bank_uuid'))
                        <div class="help-block text-danger">{{ $errors->first('bank_uuid') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('jenis_pembayaran','Jenis Kas',['class' => 'required form-label'])}}
                        {!! Form::select('jenis_pembayaran', $kas, '', ['id' =>
                        'kas','class' =>
                        'kas form-control'.($errors->has('jenis_pembayaran') ? 'is-invalid':''), 'required'
                        => '', 'placeholder' => 'Pilih Jenis Kas']) !!} @if ($errors->has('jenis_pembayaran'))
                        <div class="help-block text-danger">{{ $errors->first('jenis_pembayaran') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('biaya_admin','Biaya Admin',['class' => 'required form-label'])}}
                        <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Rp.
                                    </span>
                                </div>
                        {{ Form::text('biaya_admin',null,['placeholder' => '','class' => 'form-control '.($errors->has('biaya_admin') ? 'is-invalid':''),'required', 'autocomplete' => 'off', 'data-inputmask' => "'alias': 'currency','prefix': ''"])}}
                        @if ($errors->has('biaya_admin'))
                        <div class="invalid-feedback">{{ $errors->first('biaya_admin') }}</div>
                        @endif
                        </div>
                    </div>                 
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('nominal','Nominal',['class' => 'required form-label'])}}
                        <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Rp.
                                    </span>
                                </div>
                        {{ Form::text('nominal',null,['placeholder' => '','class' => 'form-control '.($errors->has('nominal') ? 'is-invalid':''),'required', 'autocomplete' => 'off', 'data-inputmask' => "'alias': 'currency','prefix': ''"])}}
                        @if ($errors->has('nominal'))
                        <div class="invalid-feedback">{{ $errors->first('nominal') }}</div>
                        @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('keterangan','Keterangan',['class' => 'required form-label'])}}
                        {{ Form::text('keterangan',null,['placeholder' => 'Keterangan','class' => 'form-control '.($errors->has('keterangan') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">{{ $errors->first('keterangan') }}</div>
                        @endif
                    </div>
                <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
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
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        
        $('.ewallet').select2();
        $('.payment').select2();
        $('.kas').select2();
        $('.bank').select2();
        $('#room_type').select2();
        $(':input').inputmask();

        $("#payment").on('change', function(e){
            var payment = $(this).val();
            $('#bank').attr('hidden',true);
            if(payment == '1'){
                $('#bank').attr('hidden',false);
            }
        });
        // Create a new password
        $(".getNewPass").click(function(){
            var field = $('#password').closest('div').find('input[name="password"]');
            field.val(randString(field));
        });
        
    });
</script>
@endsection