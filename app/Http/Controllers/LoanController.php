<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $members = Member::all();

        $query = Book::select('books.*', 'book_categories.name as category_name')
            ->leftJoin('book_categories', 'books.category_id', '=', 'book_categories.id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('book_categories.name', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.books.partials.list', compact('books'))->render();
        }

        return view('loan', compact('members', 'books'));
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
        $request->validate(
            [
                'member_id' => 'required|exists:members,id',
                'book_ids' => 'required|array|min:1',
                'book_ids.*' => 'exists:books,id',
            ],
            [
                'member_id.required' => 'Pilih anggota terlebih dahulu.',
                'member_id.exists' => 'Anggota tidak ditemukan.',
                'book_ids.required' => 'Pilih minimal satu buku.',
                'book_ids.array' => 'Data buku tidak valid.',
                'book_ids.min' => 'Pilih minimal satu buku.',
                'book_ids.*.exists' => 'Buku tidak ditemukan.',
            ]
        );

        DB::beginTransaction();
        try {
            // set tanggal pinjam & jatuh tempo
            $loanDate = Carbon::now();
            $dueDate = Carbon::now()->addDays(7); // default 7 hari

            // simpan master loan
            $loan = Loan::create([
                'user_id'    => $request->member_id,
                'loan_date'  => $loanDate->toDateString(),
                'due_date'   => $dueDate->toDateString(),
                'fine_amount' => 0,
                'status'     => 'borrowed',
            ]);

            // simpan detail buku
            foreach ($request->book_ids as $bookId) {
                LoanDetail::create([
                    'loan_id'  => $loan->id,
                    'book_id'  => $bookId,
                    'quantity' => 1, // masih default 1
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disimpan.',
                'loan_id' => $loan->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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
}
