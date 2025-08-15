<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get(); // Kecuali admin
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status_user' => $request->status_user
        ]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = User::select(['id', 'name', 'email', 'role', 'status_user'])
                ->where('role', '!=', 'admin');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return ucfirst($row->role);
                })
                ->addColumn('aksi', function ($user) {
                    return '
                        <button class="btn btn-sm btn-info btn-edit-password" data-id="' . $user->id . '"><i class="fas fa-lock"></i> Ubah Password</button>    
                        <button class="btn btn-sm btn-primary btn-edit" 
                            data-id="' . $user->id . '" 
                            data-name="' . e($user->name) . '" 
                            data-email="' . e($user->email) . '" 
                            data-role="' . $user->role . '" 
                            data-status="' . $user->status_user . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '"><i class="fas fa-trash"></i> Hapus</button>
                    ';
                })
                ->editColumn('status_user', function ($row) {
                    $badgeClass = match ($row->status_user) {
                        'verified' => 'success',
                        'blocked' => 'danger',
                        default => 'warning',
                    };

                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->status_user) . '</span>';
                })
                ->rawColumns(['status_user', 'aksi'])
                ->make(true);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'id.required' => 'ID user wajib diisi.',
            'id.exists' => 'User tidak ditemukan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah untuk user: ' . $user->name]);
    }
}
