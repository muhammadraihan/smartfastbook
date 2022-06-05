<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaldoKeluar;
use App\Models\Kas_toko;
use Carbon\Carbon;

use Auth;
use DataTables;
use DB;
use File;
use Hash;
use Image;
use Response;
use URL;
use PDF;
use Helper;

class SaldoKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $user = Auth::user();
            $roles = $user->getRoleNames();
            if ($roles[0] == "kasir") {
                $data = SaldoKeluar::all();
            // $data = SaldoKeluar::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('kas_uuid', function($row){
                    return $row->kas->bank_uuid;
                })
                ->editColumn('created_at',function($row){
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    ;
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }
        $data = SaldoKeluar::all();
            // $data = SaldoKeluar::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('nominal', function($row){
                    return $row->nominal ? 'Rp.'.' '.number_format($row->nominal,2) : '';
                })
                ->editColumn('kas_uuid', function($row){
                    return $row->kas->bank_uuid;
                })
                ->editColumn('created_at',function($row){
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->editColumn('created_by', function($row){
                    return $row->userCreate->name;
                })
                ->editColumn('edited_by', function($row){
                    return $row->userEdit->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    ;
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('saldoKeluar.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
