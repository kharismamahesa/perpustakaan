<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\Request;
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
                        <button class="btn btn-sm btn-primary btn-edit" 
                            data-id="' . $row->id . '" 
                            data-name="' . e($row->name) . '" 
                            data-email="' . e($row->description) . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        BookCategory::create($request->all());

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $data = BookCategory::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $data = BookCategory::findOrFail($id);
        $data->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        BookCategory::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
