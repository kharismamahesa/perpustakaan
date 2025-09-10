@extends('layouts.app')

@section('title', 'Manajemen Member')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/izitoast/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.css') }}">
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
                <table class="table table-striped" id="members-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Member</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Tgl Registrasi</th>
                            <th>Status</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group" id="member_code_group">
                        <label for="name">Kode Member</label>
                        <input type="text" class="form-control" id="member_code" name="member_code"
                            placeholder="Kode Member" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama Member">
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Alamat">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="phone">No HP</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="08xxxx">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="email@domain.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="registered_date">Tanggal Registrasi</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="registered_date" name="registered_date"
                                    readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="expired_date">Tanggal Expired</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="expired_date" name="expired_date" disabled>
                            </div>
                        </div>
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
    <script src="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        let table;

        function clear_form() {
            $('#id').val('');
            $('#member_code').val('');
            $('#name').val('');
            $('#address').val('');
            $('#phone').val('');
            $('#email').val('');
            $('#registered_date').val('');
            $('#expired_date').val('');
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
                        url: `/admin/members/${id}`,
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

        function editData(id) {
            $.ajax({
                url: `/admin/members/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    $('#member_code_group').show();
                    $('#id').val(response.id);
                    $('#member_code').val(response.member_code);
                    $('#name').val(response.name);
                    $('#address').val(response.address);
                    $('#phone').val(response.phone);
                    $('#email').val(response.email);
                    $('#registered_date').val(response.registered_date);
                    $('#expired_date').val(response.expired_date);
                    $('.modal-title').html("<i class='fas fa-edit'></i> Ubah Data");
                    $('#btn-save').hide();
                    $('#btn-update').show();
                    $('#formModal').modal('show');
                }
            });
        }

        $(document).ready(function() {
            $('#registered_date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                singleDatePicker: true,
            }, function(start) {
                let registered = start.clone();
                let expired = registered.add(6, 'months').format('YYYY-MM-DD');
                $('#expired_date').val(expired);
            });
            $('#expired_date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                singleDatePicker: true,
            });

            table = $('#members-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('members.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'member_code',
                        name: 'member_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'registered_date',
                        name: 'registered_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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
                $('#member_code_group').hide();
                $('.modal-title').html("<i class='fas fa-plus'></i> Tambah Data");
                $('#btn-save').show();
                $('#btn-update').hide();
                $('#formModal').modal('show');
            });

            $('#btn-save').on('click', function() {
                const name = $('#name').val();
                const address = $('#address').val();
                const phone = $('#phone').val();
                const email = $('#email').val();
                const registered_date = $('#registered_date').val();
                const expired_date = $('#expired_date').val();
                if (!name || !address || !email || !phone || !registered_date || !expired_date) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: '/admin/members',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            name: name,
                            address: address,
                            phone: phone,
                            email: email,
                            registered_date: registered_date,
                            expired_date: expired_date,
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
                const address = $('#address').val();
                const phone = $('#phone').val();
                const email = $('#email').val();
                const registered_date = $('#registered_date').val();
                const expired_date = $('#expired_date').val();
                if (!name || !address || !email || !phone || !registered_date || !expired_date) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: `/admin/members/${id}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT',
                            name: name,
                            address: address,
                            phone: phone,
                            email: email,
                            registered_date: registered_date,
                            expired_date: expired_date,
                        },
                        success: function(response) {
                            if (response.success == true) {
                                $('#formModal').modal('hide');
                                table.ajax.reload(null, false);
                                iziToast.success({
                                    title: 'Berhasil!',
                                    message: 'Data berhasil diubah.',
                                    position: 'topCenter'
                                });
                            } else {
                                console.log('gagal');
                                // swal('Gagal', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            swal('Gagal', 'Gagal mengubah data.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
