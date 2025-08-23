<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BookCategory::all();
        return view('admin.books.index', compact('categories'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Book::with('category')->select([
                'id',
                'title',
                'author',
                'publisher',
                'year',
                'isbn',
                'description',
                'cover_image',
                'quantity',
                'category_id'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('cover_image', function ($row) {
                    if ($row->cover_image) {
                        return '<img src="' . asset('storage/' . $row->cover_image) . '" height="300" class="img-thumbnail">';
                    }
                    return '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '<button class="btn btn-sm btn-primary" onclick="editData(' . $row->id . ')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id . ')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>';
                })
                ->rawColumns(['cover_image', 'aksi'])
                ->make(true);
        }
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
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'isbn' => 'required|unique:books,isbn',
            'quantity' => 'required|numeric',
            'category' => 'required|exists:book_categories,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        Book::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'],
            'year' => $validated['year'],
            'isbn' => $validated['isbn'],
            'cover_image' => $coverPath,
            'quantity' => $validated['quantity'],
            'category_id' => $validated['category'],
        ]);

        return response()->json(['success' => true]);
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
        try {
            $data = Book::findOrFail($id);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
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
}
