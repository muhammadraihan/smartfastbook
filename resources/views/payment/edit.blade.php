@extends('layouts.page')

@section('title', 'Payment Methode Edit')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>Edit <span class="fw-300"><i>{{$payment->name}}</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('payment.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => ['payment.update',$payment->uuid],'method' => 'PUT','class' =>
                    'needs-validation','novalidate', 'enctype' => 'multipart/form-data']) !!}
                    <div class="form-group col-md-3 mb-3">
                        {{ Form::label('name','Nama Payment Methode',['class' => 'required form-label'])}}
                        {{ Form::text('name',$payment->name,['placeholder' => 'Nama Payment Methode','class' => 'form-control '.($errors->has('name') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
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
        
        $('.bank').select2();
        $('#room_type').select2();
        $(':input').inputmask();

           $('#search').click(function (e){
            var norek = $('#norek').val();
            // console.log(norek);
            var bank = $('#tujuan').val();

            $.ajax({
                            url:'{{route('get.cekrekening')}}',
                            //timeout:6000,
                            type:'POST',
                            dataType:'json',
                            data:'norek='+norek+'&bank='+bank,
                            beforeSend: function() {
                            $('#loading').html("PROSES CEK NAMA ......");
                            },
                            success: function(hasil)
                            { 		
                                console.log(hasil);		
                                document.getElementById("nama").value = hasil.data.name;
                                $('#loading').html("");
                                //alert(norek+'<br>'+bank);
                            },
                            error: function (hasil) {
                                // This callback function will trigger on unsuccessful action   
                                console.log(bank,norek);
                                // document.getElementById("nama").value = hasil.data.name;
                                $('#loading').html(""); 
                                alert('Koneksi Server Off');
                            }
                        });               	
            });
            
        
        // Create a new password
        $(".getNewPass").click(function(){
            var field = $('#password').closest('div').find('input[name="password"]');
            field.val(randString(field));
        });
        
    });
</script>
@endsection