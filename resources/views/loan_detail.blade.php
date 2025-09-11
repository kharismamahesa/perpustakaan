@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/izitoast/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Data Peminjaman</h4>
                </div>
                <div class="card-body">
                    <h5>Detail Peminjaman</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Peminjaman</th>
                            <td>{{ $loan->loan_code }}</td>
                        </tr>
                        <tr>
                            <th>Anggota</th>
                            <td>{{ $loan->member->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <td>{{ $loan->loan_date }}</td>
                        </tr>
                        <tr>
                            <th>Jatuh Tempo</th>
                            <td>{{ $loan->due_date }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($loan->status == 'late')
                                    <span class="badge badge-danger">Terlambat</span>
                                @elseif($loan->status == 'returned')
                                    <span class="badge badge-success">Dikembalikan</span>
                                @else
                                    <span class="badge badge-warning">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <h5>Detail Buku</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->book->title ?? '-' }}</td>
                                    <td>{{ $detail->book->author ?? '-' }}</td>
                                    <td>{{ $detail->book->publisher ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/modules/izitoast/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
