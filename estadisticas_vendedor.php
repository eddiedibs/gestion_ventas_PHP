<!DOCTYPE html>
<html>
<head>
    <title>Estadísticas de Vendedor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Estadísticas de Vendedor</h2>

    <div class="form-group">
        <label for="vendedor">Seleccionar Vendedor:</label>
        <select class="form-control" id="vendedor">
            <!-- Los vendedores se llenarán dinámicamente desde la base de datos -->
        </select>
    </div>

    <button type="button" class="btn btn-primary" id="ver_estadisticas">Ver Estadísticas</button>

    <div id="modal_estadisticas" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estadísticas del Vendedor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Total de Ventas: <span id="total_ventas"></span></p>
                    <p>Número de Ventas: <span id="num_ventas"></span></p>
                    <p>Productos Más Vendidos:</p>
                    <ul id="productos_mas_vendidos"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    cargarVendedores();

    $('#ver_estadisticas').on('click', function() {
        var vendedor_id = $('#vendedor').val();
        $.post('obtener_estadisticas.php', { vendedor_id: vendedor_id }, function(response) {
            var datos = JSON.parse(response);
            $('#total_ventas').text(datos.total_ventas);
            $('#num_ventas').text(datos.num_ventas);
            $('#productos_mas_vendidos').empty();
            datos.productos_mas_vendidos.forEach(function(producto) {
                $('#productos_mas_vendidos').append('<li>' + producto.nombre + ': ' + producto.cantidad + '</li>');
            });
            $('#modal_estadisticas').modal('show');
        });
    });
});

function cargarVendedores() {
    $.get('cargar_vendedores.php', function(data) {
        $('#vendedor').html(data);
    });
}
</script>

</body>
</html>

