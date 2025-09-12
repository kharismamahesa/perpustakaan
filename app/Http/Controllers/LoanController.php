<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('loan_list');
    }

    public function data()
    {
        $query = Loan::with('member')->select('loans.*');
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('member', function ($loan) {
                return $loan->member->name ?? '-';
            })
            ->editColumn('loan_date', function ($loan) {
                return $loan->loan_date;
            })
            ->editColumn('due_date', function ($loan) {
                return $loan->due_date;
            })
            ->editColumn('status', function ($loan) {
                return ucfirst($loan->status);
            })
            ->editColumn('aksi', function ($loan) {
                return '<a class="btn btn-sm btn-primary" href="' . route('loans.show', $loan->id) . '">
                            <i class="fas fa-list"></i> Detail
                        </a>';
            })
            ->rawColumns(['cover_image', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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
            $loanDate = Carbon::now();
            $dueDate = Carbon::now()->addDays(7);

            $lastLoan = Loan::orderBy('id', 'desc')->first();
            $nextNumber = $lastLoan && $lastLoan->loan_code
                ? ((int) substr($lastLoan->loan_code, 1)) + 1
                : 1;
            $loanCode = 'P' . str_pad($nextNumber, 10, '0', STR_PAD_LEFT);

            $loan = Loan::create([
                'loan_code'  => $loanCode,
                'member_id'    => $request->member_id,
                'loan_date'  => $loanDate->toDateString(),
                'due_date'   => $dueDate->toDateString(),
                'fine_amount' => 0,
                'status'     => 'borrowed',
            ]);

            foreach ($request->book_ids as $bookId) {
                $book = Book::findOrFail($bookId);

                // hitung jumlah buku yang sedang dipinjam (belum dikembalikan)
                $borrowedCount = LoanDetail::where('book_id', $bookId)
                    ->whereHas('loan', function ($q) {
                        $q->where('status', 'borrowed');
                    })
                    ->count();

                // jika stok habis
                if ($borrowedCount >= $book->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Buku '{$book->title}' sedang habis dipinjam."
                    ], 400);
                }

                // simpan detail buku
                LoanDetail::create([
                    'loan_id'  => $loan->id,
                    'book_id'  => $bookId,
                    'quantity' => 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disimpan.',
                'loan_id' => $loan->id,
                'loan_code' => $loan->loan_code
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
        $loan = Loan::with(['member', 'details.book'])->findOrFail($id);
        return view('loan_detail', compact('loan'));
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
