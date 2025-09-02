<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Judul & Deskripsi</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>ISBN</th>
                <th>Kategori</th>
                <th>Tersedia</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $index => $book)
                <tr>
                    <td>{{ $books->firstItem() + $index }}</td>
                    <td>
                        {{ $book->title }}
                        <br>
                        {{ $book->description }}
                    </td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->publisher }}</td>
                    <td>{{ $book->year }}</td>
                    <td>{{ $book->isbn }}</td>
                    <td>{{ $book->category_name }}</td>
                    <td>{{ $book->quantity }}</td>
                    <td><button onclick="tambahbuku({{ $book->id }})" class="btn btn-sm btn-primary"><i
                                class="fas fa-plus"></i></button></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {!! $books->links() !!}
</div>
