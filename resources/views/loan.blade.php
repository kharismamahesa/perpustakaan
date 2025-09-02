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
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4>Form Peminjaman</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih Anggota</label>
                        <select id="user_id" name="user_id" class="form-control select2" style="width: 100%">
                            <option value="">-- Pilih Anggota --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Peminjaman</label>
                        <input type="text" id="loan_date" name="loan_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>List Data</h4>
                </div>
                <div class="card-body">
                    <input type="text" id="search" class="form-control form-control-lg w-100"
                        placeholder="Cari judul / penulis / penerbit / tahun / isbn ...">

                    <div id="book-list">
                        @include('admin.books.partials.list', ['books' => $books])
                    </div>

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
    <script>
        $(document).ready(function() {
            $('#loan_date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                singleDatePicker: true,
            });
            $('.select2').select2();

            function fetch_data(page, query) {
                $.ajax({
                    url: "{{ route('loans.index') }}?page=" + page + "&search=" + encodeURIComponent(
                        query || ''),
                    success: function(html) {
                        $('#book-list').html(html);
                    }
                });
            }

            $('#search').on('keyup', function() {
                const q = $(this).val();
                fetch_data(1, q);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = new URL($(this).attr('href')).searchParams.get('page');
                const q = $('#search').val();
                fetch_data(page, q);
            });

        });

        function tambahbuku(id) {
            console.log(id);
        }
    </script>
@endpush
