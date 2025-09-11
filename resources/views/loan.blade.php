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
                    <h4>List Buku</h4>
                </div>
                <div class="card-body">
                    <div id="loan-books" class="d-flex overflow-auto" style="gap: 1rem; white-space: nowrap;">
                        <p id="no-books" class="text-muted m-0">Belum ada buku dipilih</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card" id="form-loan">
                <div class="card-header">
                    <h4>Form Peminjaman</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih Anggota</label>
                        <select id="member_id" name="member_id" class="form-control select2" style="width: 100%">
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

                    <button id="submit-loan" class="btn btn-primary btn-lg btn-block"><i class="fas fa-paper-plane"></i>
                        Proses Peminjaman</button>
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

                    <div id="book-list" class="mt-3">
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
    <script src="{{ asset('assets/js/stisla.js') }}"></script>
    <script>
        function checkEmptyBooks() {
            if ($("#loan-books .book-card").length === 0) {
                if ($("#no-books").length === 0) {
                    $("#loan-books").append('<p id="no-books" class="text-muted m-0">Belum ada buku dipilih</p>');
                }
            } else {
                $("#no-books").remove();
            }
        }

        $(document).ready(function() {
            checkEmptyBooks();

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


            $(document).on('click', '.add-book', function() {
                let id = $(this).data('id');
                let title = $(this).data('title');
                let author = $(this).data('author') || '';
                let year = $(this).data('year') || '';
                let category = $(this).data('category') || '';
                let image = $(this).data('image') || '/images/no-cover.png';

                // cek apakah sudah ada
                if ($("#book-card-" + id).length > 0) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Buku sudah ditambahkan.',
                        position: 'topCenter'
                    });
                    return;
                }

                let card = `
                    <div class="book-card col-6 col-sm-4 col-md-3 col-lg-2 mb-3" id="book-card-${id}">
                        <div class="card h-100 shadow-sm position-relative">
                            <img src="${image}" class="card-img-top" alt="${title}" style="height: 150px; object-fit: cover;">

                            <button type="button" class="btn btn-danger remove-book position-absolute" 
                                    data-id="${id}" 
                                    style="top:5px; right:5px; border-radius:50%;">
                                <i class="fas fa-trash"></i>
                            </button>

                            <div class="card-body d-flex flex-column">
                                <input type="hidden" name="book_ids[]" value="${id}">
                                <h6 class="card-title text-truncate">${title}</h6>
                                <p class="card-text text-muted text-truncate small mb-0">${year} - ${author}</p>
                            </div>
                        </div>
                    </div>
                `;

                $("#loan-books").append(card);
                checkEmptyBooks();
            });

            // hapus card
            $(document).on('click', '.remove-book', function() {
                let id = $(this).data('id');
                $("#book-card-" + id).remove();
                checkEmptyBooks();
            });

            $('#submit-loan').on('click', function() {
                let member_id = $('#member_id').val();
                let loan_date = $('#loan_date').val();
                let book_ids = [];
                $("input[name='book_ids[]']").each(function() {
                    book_ids.push($(this).val());
                });

                if (!member_id) {
                    iziToast.error({
                        title: 'Error!',
                        message: 'Pilih anggota terlebih dahulu.',
                        position: 'topCenter'
                    });
                    return;
                } else if (!loan_date) {
                    iziToast.error({
                        title: 'Error!',
                        message: 'Pilih tanggal peminjaman.',
                        position: 'topCenter'
                    });
                    return;
                } else if (book_ids.length === 0) {
                    iziToast.error({
                        title: 'Error!',
                        message: 'Pilih minimal 1 buku untuk dipinjam.',
                        position: 'topCenter'
                    });
                    return;
                } else {
                    $.ajax({
                        url: "{{ route('loans.store') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            member_id: member_id,
                            loan_date: loan_date,
                            book_ids: book_ids
                        },
                        beforeSend: function() {
                            $('#submit-loan').attr('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin"></i> Processing...');
                        },
                        success: function(response) {
                            iziToast.success({
                                title: 'Success!',
                                message: response.message,
                                position: 'topCenter'
                            });
                            // reset form
                            // $('#member_id').val('').trigger('change');
                            // $('#loan_date').val('');
                            // $('#loan-books').empty();
                            // fetch_data(1, '');
                        },
                        error: function(xhr) {
                            iziToast.error({
                                title: 'Error!',
                                message: xhr.responseJSON.message ||
                                    'Terjadi kesalahan.',
                                position: 'topCenter'
                            });
                        },
                        complete: function() {
                            $('#submit-loan').attr('disabled', false).html(
                                '<i class="fas fa-paper-plane"></i> Proses Peminjaman');
                        }
                    });
                }
            });
        });
    </script>
@endpush
