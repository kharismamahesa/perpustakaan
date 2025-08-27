<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.members.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Member::select([
                'id',
                'member_code',
                'name',
                'address',
                'phone',
                'email',
                'registered_date',
                'expired_date'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $today = now()->toDateString();
                    if ($row->expired_date && $row->expired_date < $today) {
                        return '<span class="badge bg-danger">Tidak Aktif</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->addColumn('aksi', function ($member) {
                    return '
                    <button class="btn btn-sm btn-primary btn-edit" 
                        data-id="' . $member->id . '" 
                        data-member_code="' . e($member->member_code) . '" 
                        data-name="' . e($member->name) . '" 
                        data-address="' . e($member->address) . '" 
                        data-phone="' . e($member->phone) . '" 
                        data-email="' . e($member->email) . '" 
                        data-registered_date="' . e($member->registered_date) . '" 
                        data-expired_date="' . e($member->expired_date) . '">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="' . $member->id . '"><i class="fas fa-trash"></i> Hapus</button>
                ';
                })
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }
    }
}
