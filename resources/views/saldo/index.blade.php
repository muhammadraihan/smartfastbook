@extends('layouts.page')

@section('title', 'Saldo Masuk Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css')}}">
<link rel="stylesheet" media="screen, print" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css">
<link rel="stylesheet" media="screen, print" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-users'></i> Module: <span class='fw-300'>Saldo Masuk</span>
        <small>
            Module for manage Saldo Masuk.
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>
                    Saldo  Masuk<span class="fw-300"><i>List</i></span>
                </h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('saldo.create')}}"><i class="fal fa-plus-circle">
                        </i>
                        <span class="nav-link-text">Add New</span>
                    </a>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
            <div class="panel-content">
                <!-- datatable start -->
                <table id="datatable" class="table table-bordered table-hover table-striped w-100">
        <thead>
            <tr>
                <th>No</th>
                <th>No Referensi</th>
                <th>Kas</th>
                <th>Nominal</th>
                <th>Keterangan</th>
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
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function(){
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var date = new Date();

        $('#start_date').datepicker({
            orientation: "bottom left",
            format:'yyyy-mm-dd', // Notice the Extra space at the beginning
            todayHighlight:'TRUE',
            autoclose: true,
            todayBtn: "linked",
            clearBtn: true,
        });

        $('#end_date').datepicker({
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
            "ajax": {
                url:'{{route('saldo.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                }
            },
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'print'
            ],
            "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'no_ref', name: 'no_ref'},
                {data: 'kas_uuid', name: 'kas_uuid'},
                {data: 'nominal', name: 'nominal'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'created_at', name: 'created_at'},
                {data: 'created_by', name: 'created_by'},
                {data: 'edited_by', name: 'edited_by'},
                {data: 'action',width:'10%',searchable:false}    
            ]
        });

        $('#filter').click(function (e){
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            
           $('#datatable').DataTable({
               "destroy": true,
               "processing": true,
               "serverSide": true,
               "responsive": true,
               "searching": false,
               "lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"] ],
               "order": [[ 0, "asc" ]],
               "ajax": {
                   url:'{{route('get.filter')}}',
                   type : "GET",
                   data: {
                       start_date: start_date,
                       end_date: end_date,
                    },
                    dataType: 'json',
                    error: function(data) {
                        console.log(data);
                    }
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"+ "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-danger btn-sm mr-1',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    }
                ],
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'no_ref', name: 'no_ref'},
                    {data: 'kas_uuid', name: 'kas_uuid'},
                    {data: 'nominal', name: 'nominal'},
                    {data: 'keterangan', name: 'keterangan'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'edited_by', name: 'edited_by'},
                    {data: 'action',width:'10%',searchable:false}
                ]
            });
        });
        
        $('#resetFilter').click(function(e){
            $('#from').val('').datepicker("update");
            $('#to').val('').datepicker("update");
            $('#filter').attr('disabled',true);
            $('#resetFilter').attr('disabled',true);
            $('#datatable').DataTable({
                "destroy" : true,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "searching": false,
                "lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"] ],
                "order": [[ 0, "asc" ]],
                "ajax": {
                    url:'{{route('saldo.index')}}',
                    type : "GET",
                    dataType: 'json',
                    error: function(data){
                        console.log(data);
                    }
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"+ "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-danger btn-sm mr-1',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    }
                ],
                "columns": [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'no_ref', name: 'no_ref'},
                    {data: 'kas_uuid', name: 'kas_uuid'},
                    {data: 'nominal', name: 'nominal'},
                    {data: 'keterangan', name: 'keterangan'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'edited_by', name: 'edited_by'},
                    {data: 'action',width:'10%',searchable:false}    
                ]
            });
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