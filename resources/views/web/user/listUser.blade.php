@extends('adminlte::page')

@section('title', 'Intranet | Registros De Usuarios')

@section('content_header')
    <h1>Listado de Usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-success" onclick="window.location='{{ route('usuario.excel') }}'">
                    Exportar a Excel
                </button>
                <button class="btn btn-danger" onclick="window.location='{{ route('usuario.pdf') }}'">
                    Exportar a PDF
                </button>
                <button class="btn btn-primary" onclick="window.location='{{ route('usuario.create') }}'">
                    Nuevo Usuario
                </button>
            </div>
            <div class="table-responsive" id="scroll-footer-table" style="margin-bottom: 20px;">
                <table class="table table-bordered" id="datatableUsuario">
                    <thead class="bg-warning">
                        <tr>
                            <th>NOMBRE COMPLETO</th>
                            <th>CORREO ELECTRÓNICO</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#userModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('usuario.edit', $user->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm"
                                            onclick="confirmarEliminacionDelUsuario('{{ $user->id }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="userModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="card-body">
                                            <strong>Nombre Completo</strong>
                                            <p class="text-muted">{{ $user->name }} </p>
                                            <hr>
                                            <strong>Correo electrónico</strong>
                                            <p class="text-muted">{{ $user->email }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <div class="float-right d-none d-sm-inline">
        Intranet
    </div>
    <strong>Copyright © <a class="text-primary">nandresdev</a>.</strong>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap4.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            const datatable = $("#datatableUsuario").DataTable({
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"],
                ],
                language: {
                    processing: "Traitement en cours...",
                    search: "Buscar",
                    lengthMenu: "Mostrar_MENU_ Registros",
                    info: "Mostrar desde _START_ hasta _END_ de _TOTAL_ registros",
                    infoEmpty: "Opcion no disponible",
                    infoFiltered: "",
                    infoPostFix: "",
                    loadingRecords: "Cargandos registros.",
                    zeroRecords: "No hay datos disponibles en la tabla",
                    emptyTable: "No hay datos disponibles en la tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Ultimo",
                    },
                },
            });
        });
    </script>

    <script>
        function mostrarUsuario(userId) {
            const userDetails = $('#userDetails' + userId).html();
            $('#userModalLabel').html('Detalles del Usuario');
            $('#userModalBody').html(userDetails);
            $('#userModal').modal('show');
        }

        function confirmarEliminacionDelUsuario(idUsuario) {
            Swal.fire({
                title: '¿Esta seguro?',
                text: "Este usuario se eliminara definitivamente de la plataforma",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Si, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarUsuario(idUsuario);
                    window.location.href = '{{ route('usuario.index') }}';
                }
            })
        }

        function eliminarUsuario(idUsuario) {
            const url = '{{ route('usuario.destroy', [':idUsuario']) }}';
            url = url.replace(':idUsuario', idUsuario);
            const csrf = '{{ csrf_token() }}';

            $.ajax({
                type: 'DELETE',
                datatype: 'json',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                success: function(data) {
                    if (data.estado == "eliminado") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: 'El usuario ' + data.nombre + ' se elimino con éxito',
                            confirmButtonColor: "#448aff",
                            confirmButtonText: "Confirmar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('usuario.index') }}';
                            }
                        });
                    }
                },
                error: function(data) {}
            })
        }
    </script>
@stop
