@extends('layouts.app')

@section('title', 'Manajemen User')

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
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="users-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
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
    {{-- modal untuk edit user --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Ubah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">-Pilih Role-</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="member">Member</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_user">Status User</label>
                        <select class="form-control" id="status_user" name="status_user">
                            <option value="">-Pilih Status User-</option>
                            <option value="verified">Verified</option>
                            <option value="blocked">Block</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-update" class="btn btn-success"><i class="fas fa-edit"></i> Ubah
                        Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk ubah password --}}
    <div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-lock"></i> Ubah Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_edit_password" name="id_edit_password">
                    <div class="form-group">
                        <label for="name">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="email">Ketik Ulang Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-update-password" class="btn btn-info"><i class="fas fa-lock"></i>
                        Ubah
                        Password</button>
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

        function clear_form_update_data() {
            $('#id').val('');
            $('#name').val('');
            $('#email').val('');
            $('#role').val('');
            $('#status_user').val('');
        }

        function clear_form_update_password() {
            $('#id_edit_password').val('');
            $('#password').val('');
            $('#password_confirmation').val('');
        }

        $(function() {
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.data') }}",
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'status_user',
                        name: 'status_user'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                swal({
                    title: "Hapus Data?",
                    text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: `/admin/users/${id}`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                swal('Berhasil!', 'User berhasil dihapus.',
                                    'success');
                            },
                            error: function(xhr) {
                                swal('Gagal', 'Gagal menghapus user.', 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const role = $(this).data('role');
                $('#id').val(id);
                $('#name').val(name);
                $('#email').val(email);
                $('#role').val(role);
                $('#editModal').modal('show');
            });

            $(document).on('click', '#btn-update', function() {
                const id = $('#id').val();
                const name = $('#name').val();
                const email = $('#email').val();
                const role = $('#role').val();
                const status_user = $('#status_user').val();
                if (!id) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Terjadi kesalahan. ID kosong.',
                        position: 'topCenter'
                    });
                } else if (!name || !email || !role || !status_user) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: `/admin/users/${id}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PUT',
                            name: name,
                            email: email,
                            role: role,
                            status_user: status_user
                        },
                        success: function(response) {
                            $('#editModal').modal('hide');
                            table.ajax.reload();
                            swal('Berhasil!', 'User berhasil diubah.', 'success');
                        },
                        error: function(xhr) {
                            swal('Gagal', 'Gagal mengubah user.', 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-edit-password', function() {
                clear_form_update_password();
                $('#id_edit_password').val($(this).data('id'));
                $('#editPasswordModal').modal('show');
            });

            $(document).on('click', '#btn-update-password', function() {
                const id_edit_password = $('#id_edit_password').val();
                const password = $('#password').val();
                const password_confirmation = $('#password_confirmation').val();
                if (!id_edit_password) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Terjadi kesalahan. ID kosong.',
                        position: 'topCenter'
                    });
                } else if (!password || !password_confirmation) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else if (password !== password_confirmation) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Password dan Konfirmasi Password tidak cocok.',
                        position: 'topCenter'
                    });
                } else {
                    $.ajax({
                        url: `/admin/users/update-password`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id_edit_password,
                            password: password,
                            password_confirmation: password_confirmation
                        },
                        success: function(response) {
                            if (response.status === 'error') {
                                iziToast.error({
                                    title: 'Peringatan!',
                                    message: response.message,
                                    position: 'topCenter'
                                });
                            } else {
                                $('#editPasswordModal').modal('hide');
                                table.ajax.reload();
                                swal('Berhasil!', 'Password berhasil diubah.', 'success');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.message;
                                const firstKey = Object.keys(errors)[0];
                                const firstError = errors[firstKey][0];
                                iziToast.warning({
                                    title: 'Peringatan!',
                                    message: firstError,
                                    position: 'topCenter'
                                });
                            } else {
                                iziToast.error({
                                    title: 'Peringatan!',
                                    message: 'Gagal mengubah password.',
                                    position: 'topCenter'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
