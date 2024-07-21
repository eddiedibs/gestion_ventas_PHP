<!DOCTYPE html>
<html>
<head>
    <title>Registro de Venta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div id="messageContainer" class="fixed-top alert alert-success d-none" role="alert">
</div>
<div class="container">
    <h2>Registro de Venta</h2>

    <!-- Formulario para la información del cliente -->
    <form id="form_cliente">
        <div class="form-group">
            <label for="nombre">Nombre del Cliente:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="cedula_rif">Cédula/RIF:</label>
            <input type="text" class="form-control" id="cedula_rif" name="cedula_rif" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <textarea class="form-control" id="direccion" name="direccion"></textarea>
        </div>
        <button type="button" class="btn btn-primary" id="guardar_cliente">Guardar Cliente</button>
        <button type="button" class="btn btn-dark" id="seleccionar_cliente">Seleccionar Cliente</button>
    </form>

    <hr>

    <!-- Formulario para agregar productos a la venta -->
    <form id="form_producto">
        <div class="form-group">
            <label for="producto">Producto:</label>
            <select class="form-control" id="producto" name="producto" required>
                <!-- Los productos se llenarán dinámicamente desde la base de datos -->
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
        </div>
    
        <!-- Quantity Up Button -->

        <button class="btn-producto btn btn-primary" type="button" id="agregar_producto">Agregar Producto</button>
        <button class="btn-producto btn btn-danger" type="button" id="eliminar_producto">Eliminar Producto</button>
    </form>

    <hr>

    <!-- Tabla de productos agregados -->
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Base</th>
                <th>Descuento</th>
                <th>IVA</th>
                <th>Subtotal (USD)</th>
            </tr>
        </thead>
        <tbody id="lista_productos">
            <!-- Los productos agregados se mostrarán aquí -->
        </tbody>
    </table>

    <hr>

    <!-- Mostrar total de la venta -->
    <div class="form-group">
        <label for="subtotal">Subtotal:</label>
        <input type="text" class="form-control" id="subtotal_input" readonly>
        
    </div>
    <div class="form-group">
        <label for="impuesto">IVA (16%):</label>
        <input type="text" class="form-control" id="impuesto_input" readonly>
    </div>
    <div class="form-group">
        <label for="total">Total:</label>
        <input type="text" class="form-control" id="total_input" readonly>
    </div>

    <button type="button" class="btn btn-success" id="finalizar_venta">Finalizar Venta</button>
</div>

<script>
// Función para cargar productos desde la base de datos
$(document).ready(function() {
    cargarProductos();
    $('#subtotal_input').val("");
    $('#impuesto_input').val("");
    $('#total_input').val("");

    $('#seleccionar_cliente').on('click', function() {
        // Guardar cliente usando AJAX
        // var data = $('#form_cliente');
        var data = {
            nombre: $('#nombre').val(),
            cedula_rif: $('#cedula_rif').val(),
            telefono: $('#telefono').val(),
            direccion: $('#direccion').val()
        };

        // Check if cedula_rif is empty
        if (data.cedula_rif === '') {
            $('#messageContainer').text("Cedula o rif no puede estar vacio.");
            $('#messageContainer').addClass('alert-danger');
            $('#messageContainer').removeClass('d-none');
            
            // Optionally, clear the input field or set a default value
            $('#cedula_rif').val('');
            setTimeout(function() {
                    $('#messageContainer').addClass('d-none');
                    $('#messageContainer').removeClass('alert-success');
                    $('#messageContainer').removeClass('alert-danger');
            }, 5000);
            
            // Prevent further processing if necessary
            return; // or throw an error, etc.
        }
        
        $.get('seleccionar_cliente.php', data, function(_, _, xhr) {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);
                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    // Update the message content and remove 'd-none' class for visibility
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-success');
                    $('#nombre').val(jsonResponse.cliente_info.nombre);
                    $('#telefono').val(jsonResponse.cliente_info.telefono);
                    $('#direccion').val(jsonResponse.cliente_info.direccion);
                    
                } else {
                    // Update the message content and style for error
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-danger');
                }

                // Always make the message visible
                $('#messageContainer').removeClass('d-none');


                // Set up a timeout to dismiss the message after 3 seconds
                setTimeout(function() {
                    $('#messageContainer').addClass('d-none');
                    $('#messageContainer').removeClass('alert-success');
                    $('#messageContainer').removeClass('alert-danger');
                    if (jsonResponse.status === 'success'){
                        obtenerProductos();
                    }
                }, 2000);

            }
        });
    });

    $('#guardar_cliente').on('click', function() {
        $('#subtotal_input').val("");
        $('#impuesto_input').val("");
        $('#total_input').val("");
        // Guardar cliente usando AJAX
        var data = {
            nombre: $('#nombre').val(),
            cedula_rif: $('#cedula_rif').val(),
            telefono: $('#telefono').val(),
            direccion: $('#direccion').val()
        };

        $.post('guardar_cliente.php', data, function(_, _, xhr) {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);

                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    // Update the message content and remove 'd-none' class for visibility
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-success');
                } else {
                    // Update the message content and style for error
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-danger');
                }

                // Always make the message visible
                $('#messageContainer').removeClass('d-none');

                // Initialize the Bootstrap Alert component
                // var messageAlert = new bootstrap.Alert(document.getElementById('messageContainer'));

                // Set up a timeout to dismiss the message after 3 seconds
                setTimeout(function() {
                    $('#messageContainer').addClass('d-none');
                    $('#messageContainer').removeClass('alert-success');
                    $('#messageContainer').removeClass('alert-danger');
                    if (jsonResponse.status === 'success'){
                        obtenerProductos();
                    }
                }, 5000);

            }
        });
    });


    // Prevent typing in the input field
    $('#cantidad').on('keydown', function(e) {
        e.preventDefault();
    });

    $('#producto').on('change', function() {
        var producto_id = $(this).val();
        $.get('cargar_producto_individual.php', { id: producto_id }, function(data) {
            $('#cantidad').attr('max', data.producto_cantidad_disponible); // Update the max attribute
            if (data.producto_cantidad_disponible < $('#cantidad').val()){
                $('#cantidad').val(`${data.producto_cantidad_disponible}`);
            };
        });
    });


    $('.btn-producto').on('click', function(e) {
        e.preventDefault();
        // Agregar producto a la lista de productos
        var producto_id = $('#producto').val();
        var cantidadIngresada = $("#cantidad").val(); // Obtener la cantidad ingresada por el usuario
        var botonId = $(this).attr('id');
        $.post('handler_producto.php', { producto_id: producto_id, cantidad: cantidadIngresada, accion: botonId}, function(_, _, xhr) {
            if (xhr.status === 200) {
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);
                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    // Always make the message visible
                    $('#messageContainer').removeClass('d-none');
                    // Update the message content and remove 'd-none' class for visibility
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-success');
                    // Set up a timeout to dismiss the message after 3 seconds
                    setTimeout(function() {
                        $('#messageContainer').addClass('d-none');
                        $('#messageContainer').removeClass('alert-success');
                        $('#messageContainer').removeClass('alert-danger');
                        obtenerProductos();

                    }, 1000);
                } else {
                    // Always make the message visible
                    $('#messageContainer').removeClass('d-none');
                    // Update the message content and style for error
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-danger');
                    setTimeout(function() {
                        $('#messageContainer').addClass('d-none');
                        $('#messageContainer').removeClass('alert-success');
                        $('#messageContainer').removeClass('alert-danger');
                        obtenerProductos();

                    }, 3000);
                }





            }

        });
        
        


    });

    $('#finalizar_venta').on('click', function() {
        // Finalizar la venta
        var data = {
            cliente_id: $('#cedula_rif').val(),
            productos: obtenerProductos(),
            subtotal: $('#subtotal_input').val(),
            impuesto: $('#impuesto_input').val(),
            total: $('#total_input').val()
        };
        $.post('finalizar_venta.php', data, function(response) {
            alert(response);
        });
    });
});

function cargarProductos() {
    $.get('cargar_productos.php', function(data) {
        // Clear any existing options
        $('#producto').empty();
        
        // Iterate over the products array
        for(let i = 0; i < data.products.length; i++) {
            // Create a new option element
            let option = $('<option></option>').val(data.products[i].id).text(data.products[i].nombre);
            // Append the option to the select
            $('#producto').append(option);
        }
        var producto_id = $("#producto").val();
        $.get('cargar_producto_individual.php', { id: producto_id }, function(data) {
            $('#cantidad').attr('max', data.producto_cantidad_disponible); // Update the max attribute

        });
    });

}

function obtenerProductos() {
    $.get('obtener_items_carrito.php', function(_, _, xhr) {
            // Clear any existing options
            $('#lista_productos').empty();

            
            if (xhr.status === 200) {
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);

                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    // Update the message content and remove 'd-none' class for visibility
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-success');
                    // Assume data is a JSON array of product objects
                    for(let i = 0; i < jsonResponse.productos.length; i++) {
                        let product = jsonResponse.productos[i];
                        let row = $('<tr></tr>');
                        row.append($('<td></td>').text(product.nombre));
                        row.append($('<td></td>').text(product.cantidad));
                        row.append($('<td></td>').text(product.precio_base));
                        row.append($('<td></td>').text(product.descuento));
                        row.append($('<td></td>').text(product.IVA));
                        row.append($('<td></td>').text(product.subtotal));
                        $('#lista_productos').append(row);
                    }
                    $('#subtotal_input').val(jsonResponse.subtotal_final);
                    $('#impuesto_input').val(jsonResponse.total_iva);
                    $('#total_input').val(jsonResponse.total);
                } else {
                    // Update the message content and style for error
                    $('#messageContainer').text(jsonResponse.message);
                    $('#messageContainer').addClass('alert-danger');
                }

                // Always make the message visible
                $('#messageContainer').removeClass('d-none');

                // Set up a timeout to dismiss the message after 3 seconds
                setTimeout(function() {
                    $('#messageContainer').addClass('d-none');
                    $('#messageContainer').removeClass('alert-success');
                    $('#messageContainer').removeClass('alert-danger');
                }, 7000);

            }



        });
    // return productos;
}

function actualizarTotal() {
    var subtotal = 0;
    $('#lista_productos tr').each(function() {
        var subtotalProducto = parseFloat($(this).find('.subtotal').text());
        subtotal += subtotalProducto;
    });
    var impuesto = subtotal * 0.16;
    var total = subtotal + impuesto;
    $('#subtotal_input').val(subtotal.toFixed(2));
    $('#impuesto_input').val(impuesto.toFixed(2));
    $('#total_input').val(total.toFixed(2));
}
</script>

</body>
</html>



