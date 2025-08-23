<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BookCategoryController extends Controller
{
    public function index()
    {
        return view('admin.book_categories.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = BookCategory::select(['id', 'name', 'description']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary" onclick="editData(' . $row->id . ')"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id . ')"><i class="fas fa-trash"></i> Hapus</button>
                ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            BookCategory::create($request->only('name', 'description'));

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

    public function edit($id)
    {
        try {
            $data = BookCategory::findOrFail($id);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500'
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'description.required' => 'Deskripsi kategori wajib diisi.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = BookCategory::findOrFail($id);
            $data->update($request->only('name', 'description'));

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diubah.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah data.'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            BookCategory::findOrFail($id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ]);
        }
    }

    public function getCategories()
    {
        $categories = BookCategory::select('id', 'name')->orderBy('name')->get();
        return response()->json($categories);
    }
}
