@extends('layouts.page')

@section('title','Dashboard')

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='fal fa-info-circle'></i> Introduction
        <small>
            A brief introduction to this {{env('APP_NAME')}}
        </small>
    </h1>
</div>
<div class="fs-lg fw-300 p-5 bg-white border-faded rounded mb-g">
    <h3 class="mb-g">
        Hi {{Auth::user()->name}},
    </h3>
    <p>
        SmartFastBook adalah sebuah aplikasi akuntansi yang dapat membantu anda dalam merekap setiap transaksi di outlet anda ! 
        SmartFastBook hadir dengan beberapa fitur yang dapat mempermudah anda. silahkan menikmati fitur dari aplikasi kami!
    </p>
    <p>
        Sincerely,<br>
        {{env('APP_DEVELOPER')}} Team<br>
    </p>
</div>
@endsection