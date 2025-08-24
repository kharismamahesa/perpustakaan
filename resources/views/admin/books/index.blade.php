@extends('layouts.app')

@section('title', 'Buku')

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
                <table class="table table-striped" id="book-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun Terbit</th>
                            <th>ISBN</th>
                            <th>Deskripsi</th>
                            <th>Cover</th>
                            <th>Qty</th>
                            <th>Kategori</th>
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
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <div class="form-group">
                                <label for="title">Judul</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Harry Potter vol 1">
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="a story about harry potter">
                            </div>
                            <div class="form-group">
                                <label for="cover_image">Cover</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image"
                                    placeholder="Cover">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="preview_cover">Preview Cover</label>
                            <div class="border rounded p-2 text-center"
                                style="min-height: 220px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <img id="preview_cover" src="#" alt="Preview"
                                    style="max-height: 200px; display: none; border-radius: 6px;" class="img-fluid" />
                            </div>
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="author">Pengarang</label>
                            <input type="text" class="form-control" id="author" name="author"
                                placeholder="J.K. Rowling">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="publisher">Penerbit</label>
                            <input type="text" class="form-control" id="publisher" name="publisher"
                                placeholder="Gramedia">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="qty">Jumlah</label>
                            <input type="text" class="form-control" id="qty" name="qty" placeholder="999">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="year">Tahun Terbit</label>
                            <input type="text" class="form-control" id="year" name="year" placeholder="2010">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="isbn">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="description">Kategori Buku</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
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
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        let table;

        function clear_form() {
            $('#id').val('');
            $('#title').val('');
            $('#description').val('');
            $('#author').val('');
            $('#publisher').val('');
            $('#year').val('');
            $('#isbn').val('');
            $('#cover_image').val('');
            $('#preview_cover').attr('src', '#').hide();
            $('#qty').val('');
            $('#category').val('').trigger('change');
        }

        function editData(id) {
            clear_form();
            $.ajax({
                url: `/admin/books/${id}/edit`,
                method: 'GET',
                success: function(response) {
                    $('#id').val(response.id);
                    $('#title').val(response.title);
                    $('#description').val(response.description);
                    $('#author').val(response.author);
                    $('#publisher').val(response.publisher);
                    $('#year').val(response.year);
                    $('#isbn').val(response.isbn);
                    if (response.cover_image) {
                        $('#preview_cover')
                            .attr('src', '/storage/' + response.cover_image)
                            .show();
                    } else {
                        $('#preview_cover')
                            .attr('src', '#')
                            .hide();
                    }
                    $('#qty').val(response.quantity);
                    $('#category').val(response.category_id).trigger('change');
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
                        url: `/admin/books/${id}`,
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
            $('#cover_image').on('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#preview_cover')
                            .attr('src', e.target.result)
                            .show();
                    }

                    reader.readAsDataURL(file);
                } else {
                    $('#preview_cover').hide();
                }
            });


            table = $('#book-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('books.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'author',
                        name: 'author'
                    },
                    {
                        data: 'publisher',
                        name: 'publisher'
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'isbn',
                        name: 'isbn'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'cover_image',
                        name: 'cover_image',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'category',
                        name: 'category.name'
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
                const title = $('#title').val();
                const description = $('#description').val();
                const author = $('#author').val();
                const publisher = $('#publisher').val();
                const year = $('#year').val();
                const isbn = $('#isbn').val();
                const cover_image = $('#cover_image').val();
                const qty = $('#qty').val();
                const category = $('#category').val();

                if (!title || !description || !author || !publisher || !year || !cover_image || !
                    qty || !category) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Semua field harus diisi.',
                        position: 'topCenter'
                    });
                } else {
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('title', title);
                    formData.append('description', description);
                    formData.append('author', author);
                    formData.append('publisher', publisher);
                    formData.append('year', year);
                    formData.append('isbn', isbn);
                    formData.append('quantity', qty);
                    formData.append('category', category);

                    let fileInput = $('#cover_image')[0].files[0];
                    if (fileInput) {
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(fileInput.type)) {
                            iziToast.error({
                                title: 'Gagal!',
                                message: 'Format gambar harus PNG, JPG, atau JPEG',
                                position: 'topCenter'
                            });
                            return;
                        }
                        formData.append('cover_image', fileInput);
                    }

                    $.ajax({
                        url: `/admin/books`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
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
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let messages = Object.values(xhr.responseJSON.errors).flat()
                                    .join('<br>');
                                swal('Gagal', messages, 'error');
                            } else {
                                swal('Gagal', 'Terjadi kesalahan saat menyimpan data.',
                                    'error');
                            }
                        }
                    });
                }
            });

            $('#btn-update').on('click', function() {
                const id = $('#id').val();
                const title = $('#title').val();
                const description = $('#description').val();
                const author = $('#author').val();
                const publisher = $('#publisher').val();
                const year = $('#year').val();
                const isbn = $('#isbn').val();
                const qty = $('#qty').val();
                const category = $('#category').val();

                if (!title || !description || !author || !publisher || !year || !qty || !category) {
                    iziToast.warning({
                        title: 'Peringatan!',
                        message: 'Field bertanda * wajib diisi.',
                        position: 'topCenter'
                    });
                    return;
                }

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT'); // Laravel butuh ini untuk update
                formData.append('title', title);
                formData.append('description', description);
                formData.append('author', author);
                formData.append('publisher', publisher);
                formData.append('year', year);
                formData.append('isbn', isbn);
                formData.append('quantity', qty);
                formData.append('category', category);

                let fileInput = $('#cover_image')[0].files[0];
                if (fileInput) {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(fileInput.type)) {
                        iziToast.error({
                            title: 'Gagal!',
                            message: 'Format gambar harus PNG, JPG, atau JPEG',
                            position: 'topCenter'
                        });
                        return;
                    }
                    formData.append('cover_image', fileInput);
                }

                $.ajax({
                    url: `/admin/books/${id}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
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
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let messages = Object.values(xhr.responseJSON.errors).flat().join(
                                '<br>');
                            swal('Gagal', messages, 'error');
                        } else {
                            swal('Gagal', 'Terjadi kesalahan saat memperbarui data.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
