@extends('layouts.page')

@section('title', 'Transaksi Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-users'></i> Module: <span class='fw-300'>Transaksi </span>
        <small>
            Module for manage Transaksi .
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>
                    Transaksi  <span class="fw-300"><i>List</i></span>
                </h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('transaksi.create')}}"><i class="fal fa-plus-circle">
                        </i>
                        <span class="nav-link-text">Add New</span>
                    </a>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="form-group col-md-3 mb-3">
                    <label>Tanggal Dari</label>
                    <input type="text" class="form-control js-bg-target" placeholder="Tanggal Dari"
                            id="from" name="from" autocomplete="off">
                </div>
                <div class="form-group col-md-3 mb-3">
                    <label>Tanggal Sampai</label>
                    <input type="text" class="form-control js-bg-target" placeholder="Tanggal Sampai"
                            id="to" name="to" autocomplete="off">
                </div>
                <div id="" class="form-group col-md-5 mb-3">
                    <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                </div>
            <div class="panel-content">
                <!-- datatable start -->
                <table id="datatable" class="table table-bordered table-hover table-striped w-100">
        <thead>
            <tr>
                <th>No</th>
                <th>No Referensi</th>
                <th>Nama Customer</th>
                <th>Bank Tujuan</th>
                <th>No Rekening</th>
                <th>Nama Rekening</th>
                <th>Dari Bank</th>
                <th>Biaya Admin</th>
                <th>Admin Bank</th>
                <th>Keterangan</th>
                <th>Nominal</th>
                <th>Created At</th>
                <th>Created By</th>
                <th>Edited By</th>
                <th width="120px">Action</th>
                </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<form action="" method="POST" class="delete-form">
    {{ csrf_field() }}
    <!-- Delete modal center -->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Confirmation
                        <small class="m-0 text-muted">
                        </small>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure want to delete data?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary remove-data-from-delete-form"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Delete Data</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script src="{{asset('js/datagrid/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script>
    $(document).ready(function(){
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
     
        $('#from').datepicker({
            orientation: "bottom left",
            format:'yyyy-mm-dd', // Notice the Extra space at the beginning
            todayHighlight:'TRUE',
            autoclose: true,
            todayBtn: "linked",
            clearBtn: true,
        });

        $('#to').datepicker({
            orientation: "bottom left",
            format:'yyyy-mm-dd', // Notice the Extra space at the beginning
            todayHighlight:'TRUE',
            autoclose: true,
            todayBtn: "linked",
            clearBtn: true,
        });

       var table = $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                url:'{{route('transaksi.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
            },
            "columns": [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'no_ref', name: 'no_ref'},
            {data: 'customer', name: 'customer'},
            {data: 'bank_tujuan', name: 'bank_tujuan'},
            {data: 'no_rek', name: 'no_rek'},
            {data: 'nama_rekening', name: 'nama_rekening'},
            {data: 'bank_uuid', name: 'bank_uuid'},
            {data: 'biaya_admin', name: 'biaya_admin'},
            {data: 'admin_bank', name: 'admin_bank'},
            {data: 'keterangan', name: 'keterangan'},
            {data: 'nominal', name: 'nominal'},
            {data: 'created_at', name: 'created_at'},
            {data: 'created_by', name: 'created_by'},
            {data: 'edited_by', name: 'edited_by'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    // Delete Data
    $('#datatable').on('click', '.delete-btn[data-url]', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            var token = $(this).attr('data-token');
            console.log(id,url,token);
            
            $(".delete-form").attr("action",url);
            $('body').find('.delete-form').append('<input name="_token" type="hidden" value="'+ token +'">');
            $('body').find('.delete-form').append('<input name="_method" type="hidden" value="DELETE">');
            $('body').find('.delete-form').append('<input name="id" type="hidden" value="'+ id +'">');
        });
        // Clear Data When Modal Close
        $('.remove-data-from-delete-form').on('click',function() {
            $('body').find('.delete-form').find("input").remove();
        });
    });
</script>
@endsection