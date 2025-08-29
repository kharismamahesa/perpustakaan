<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'registered_date' => 'required|date',
            'expired_date' => 'nullable|date|after:registered_date',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'phone.required' => 'No HP wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'registered_date.required' => 'Tanggal Registrasi wajib diisi.',
            'expired_date.after' => 'Tanggal Expired harus setelah Tanggal Registrasi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $validated = $validator->validated();

        try {
            $lastMember = Member::orderBy('member_code', 'desc')->first();
            if ($lastMember && $lastMember->member_code) {
                $lastNumber = (int)substr($lastMember->member_code, 1);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $member_code = 'M' . str_pad($newNumber, 10, '0', STR_PAD_LEFT);

            Member::create([
                'member_code' => $member_code,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'registered_date' => $validated['registered_date'],
                'expired_date' => $validated['expired_date'],
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ]);
        }
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
