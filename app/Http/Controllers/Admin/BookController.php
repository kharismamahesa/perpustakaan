<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
                        return '<img src="' . asset('storage/' . $row->cover_image) . '" height="100" >';
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'isbn' => 'nullable|string|unique:books,isbn',
            'quantity' => 'required|numeric',
            'category' => 'required|exists:book_categories,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'author.required' => 'Pengarang wajib diisi.',
            'publisher.required' => 'Penerbit wajib diisi.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.digits' => 'Tahun terbit harus berupa 4 digit angka.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.min' => 'Tahun terbit minimal adalah 1900.',
            'year.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'quantity.required' => 'Jumlah buku wajib diisi.',
            'quantity.numeric' => 'Jumlah buku harus berupa angka.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.exists' => 'Kategori tidak valid.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $validated = $validator->validated();

        try {
            Book::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'author' => $validated['author'],
                'publisher' => $validated['publisher'],
                'year' => $validated['year'],
                'isbn' => $validated['isbn'] ?? null,
                'cover_image' => $coverPath,
                'quantity' => $validated['quantity'],
                'category_id' => $validated['category'],
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
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'isbn' => 'nullable|string|unique:books,isbn,' . $book->id . ',id',
            'quantity' => 'required|numeric',
            'category' => 'required|exists:book_categories,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'author.required' => 'Pengarang wajib diisi.',
            'publisher.required' => 'Penerbit wajib diisi.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.digits' => 'Tahun terbit harus berupa 4 digit angka.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.min' => 'Tahun terbit minimal adalah 1900.',
            'year.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'quantity.required' => 'Jumlah buku wajib diisi.',
            'quantity.numeric' => 'Jumlah buku harus berupa angka.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.exists' => 'Kategori tidak valid.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $validated = $validator->validated();

        $coverPath = $book->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        try {
            $book->update([
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'author'      => $validated['author'],
                'publisher'   => $validated['publisher'],
                'year'        => $validated['year'],
                'isbn'        => $validated['isbn'] ?? null,
                'cover_image' => $coverPath,
                'quantity'    => $validated['quantity'],
                'category_id' => $validated['category'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Book::findOrFail($id)->delete();
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
}
