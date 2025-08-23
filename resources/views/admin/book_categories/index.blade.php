@extends('layouts.app')

@section('title', 'Kategori Buku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/izitoast/css/iziToast.min.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>List Data</h4>
            <div class="card-header-action">
                <button id="btn-add-data" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Data</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="book-categories-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-save" class="btn btn-success"><i class="fas fa-save"></i> Simpan
                        Data</button>
                    <button type="button" id="btn-update" class="btn btn-info"><i class="fas fa-edit"></i> Ubah
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/modules/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/modules/izitoast/js/iziToast.min.js') }}"></script>
    <script>
        let table;

        function clear_form() {
            $('#id').val('');
            $('#name').val('');
            $('#description').val('');
        }

        function editData(id) {
            $.ajax({
                url: `/admin/book-categories/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    $('#id').val(response.id);
                    $('#name').val(response.name);
                    $('#description').val(response.description);
                    $('.modal-title').html("<i class='fas fa-edit'></i> Ubah Data");
                    $('#btn-save').hide();
                    $('#btn-update').show();
                    $('#formModal').modal('show');
                }
            });
        }

        function deleteData(id) {
            swal({
                title: "Hapus Data?",
                text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: `/admin/book-categories/${id}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success == true) {
                                table.ajax.reload(null, false);
                                iziToast.success({
                                    title: 'Berhasil!',
                                    message: 'Data berhasil dihapus.',
                                    position: 'topCenter'
                                });
                            } else {
                                swal('Gagal', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            swal('Gagal', 'Gagal menghapus user.', 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            table = $('#book-categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "/admin/book-categories/data",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#btn-add-data').on('click', function() {
                clear_form();
                $('.modal-title').html("<i class='fas fa-plus'></i> Tambah Data");
                $('#btn-save').show();
                $('#btn-update').hide();
                $('#formModal').modal('show');
            });

            $('#btn-save').on('click', function() {
                const name = $('#name').val();
                const description = $('#description').val();
                if (!name || !description) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: '/admin/book-categories',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            name: name,
                            description: description,
                        },
                        success: function(response) {
                            if (response.success == true) {
                                $('#formModal').modal('hide');
                                table.ajax.reload(null, false);
                                iziToast.success({
                                    title: 'Berhasil!',
                                    message: 'Data berhasil disimpan.',
                                    position: 'topCenter'
                                });
                            } else {
                                swal('Gagal', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            swal('Gagal', 'Gagal menyimpan data.', 'error');
                        }
                    });
                }
            });

            $('#btn-update').on('click', function() {
                const id = $('#id').val();
                const name = $('#name').val();
                const description = $('#description').val();

                if (!name || !description) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: `/admin/book-categories/${id}`,
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            name: name,
                            description: description,
                        },
                        success: function(response) {
                            if (response.success == true) {
                                $('#formModal').modal('hide');
                                table.ajax.reload(null, false);
                                iziToast.success({
                                    title: 'Berhasil!',
                                    message: 'Data berhasil diperbarui.',
                                    position: 'topCenter'
                                });
                            } else {
                                swal('Gagal', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            swal('Gagal', 'Gagal memperbarui data.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
