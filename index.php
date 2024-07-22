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
<div class="container card shadow-sm p-5">
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
            <label for="vendedor">Vendedor:</label>
            <select class="form-control" id="vendedor" name="vendedor" required>
                <!-- Los vendedores se llenarán dinámicamente desde la base de datos -->
            </select>
        </div>
        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <select class="form-control" id="categoria" name="categoria" required>
                <!-- Las categorias se llenarán dinámicamente desde la base de datos -->
            </select>
        </div>
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

    <div class="d-flex justify-content-center align-items-center">
        <button type="button" class="btn btn-success mr-3" id="finalizar_venta">Finalizar Venta</button>
        <button type="button" class="btn btn-primary ml-3" id="estadisticas_vista">Ver estadisticas</button>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="estadisticasModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Estadísticas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label for="vendedor">Vendedor:</label>
            <select class="form-control" id="vendedor_modal" name="vendedor" required>
                <!-- Los vendedores se llenarán dinámicamente desde la base de datos -->
            </select>
        <!-- Aquí van las estadísticas. -->
        <div class="modal-body">
                    <p>Total de Ventas: <span id="total_ventas"></span></p>
                    <p>Número de Ventas: <span id="num_ventas"></span></p>
                    <p>Productos Más Vendidos:</p>
                    <ul id="productos_mas_vendidos"></ul>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Función para cargar productos desde la base de datos
$(document).ready(function() {
    $('#estadisticas_vista').click(function() {
      $('#estadisticasModal').modal('show');
      cargarEstadisticas();
    });
    $('#categoria').ready(async function(){
        await cargarCategorias();
        cargarProductos($('#categoria').val());
        cargarEstadisticas();
        
    })
    cargarVendedores("#vendedor");
    cargarVendedores("#vendedor_modal");
    limpiarAll();

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
                    
                    mostrarMensaje(jsonResponse.message, "success", 1000);
                    $('#nombre').val(jsonResponse.cliente_info.nombre);
                    $('#telefono').val(jsonResponse.cliente_info.telefono);
                    $('#direccion').val(jsonResponse.cliente_info.direccion);
                    
                } else {
                    mostrarMensaje(jsonResponse.message, "failed", 1000);
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
                console.log(jsonResponse);
                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    mostrarMensaje(jsonResponse.message, "success", 1000);
                } else {
                    // Update the message content and style for error
                    mostrarMensaje(jsonResponse.message, "failed", 1000);
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

    

    $('#vendedor_modal').on('change', function() {
        cargarEstadisticas();
    });

    $('#categoria').on('change', function() {
        var categoria_id = $(this).val();
        cargarProductos(categoria_id);
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
                    mostrarMensaje(jsonResponse.message, "success", 3000);
                    obtenerProductos();

                } else {
                    mostrarMensaje(jsonResponse.message, "failed", 3000);

                }
            }

        });
        
        


    });

    $('#finalizar_venta').on('click', async function() {
        let productos_lista = await obtenerProductos();
        // Finalizar la venta
        var data = {
            subtotal: parseFloat($('#subtotal_input').val().replace('$', '')),
            impuesto: parseFloat($('#impuesto_input').val().replace('$', '')),
            total: parseFloat($('#total_input').val().replace('$', '')),
            vendedor_id: $('#vendedor').val(),
            productos: productos_lista
        };
        $.post('finalizar_venta.php', data, function(_,_,xhr) {
            if (xhr.status === 200) {
                limpiarAll();
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);
                // Assuming the response contains a 'status' field indicating success
                if (jsonResponse.status === 'success') {
                    mostrarMensaje(jsonResponse.message, "success", 3000);

                } else {
                    mostrarMensaje(jsonResponse.message, "failed", 3000);

                }
            }


        });
    });
});

function mostrarMensaje(mensaje, tipo, delay){

    if (tipo == "failed"){
        // Always make the message visible
        $('#messageContainer').removeClass('d-none');
        // Update the message content and style for error
        $('#messageContainer').text(mensaje);
        $('#messageContainer').addClass('alert-danger');
        setTimeout(function() {
            $('#messageContainer').addClass('d-none');
            $('#messageContainer').removeClass('alert-success');
            $('#messageContainer').removeClass('alert-danger');

        }, delay);
    } else {
        // Always make the message visible
        $('#messageContainer').removeClass('d-none');
        // Update the message content and remove 'd-none' class for visibility
        $('#messageContainer').text(mensaje);
        $('#messageContainer').addClass('alert-success');
        // Set up a timeout to dismiss the message after 3 seconds
        setTimeout(function() {
            $('#messageContainer').addClass('d-none');
            $('#messageContainer').removeClass('alert-success');
            $('#messageContainer').removeClass('alert-danger');

        }, delay);
    }

}

function cargarProductos(categoria_id) {
    $.get('cargar_productos.php', {categoria_id: categoria_id}, function(_, _, xhr) {
        // Clear any existing options
        $('#producto').empty();
        if (xhr.status === 200) {
            // Parse the response text to JSON
            var jsonResponse = JSON.parse(xhr.responseText);
            // Assuming the response contains a 'status' field indicating success
            if (jsonResponse.status === 'success') {
                // Iterate over the products array
                for(let i = 0; i < jsonResponse.products.length; i++) {
                    // Create a new option element
                    let option = $('<option></option>').val(jsonResponse.products[i].id).text(jsonResponse.products[i].nombre);
                    // Append the option to the select
                    $('#producto').append(option);
                }
                var producto_id = $("#producto").val();
                $.get('cargar_producto_individual.php', { id: producto_id }, function(data) {
                    $('#cantidad').attr('max', data.producto_cantidad_disponible); // Update the max attribute

                });

            } else {
                mostrarMensaje(jsonResponse.message, "failed", 3000);

            }
        }

    });

}

async function cargarCategorias() {
    const jsonResponse = await fetchUtil("cargar_categorias");    

    // Clear any existing options
    $('#categoria').empty();
    // Iterate over the products array
    for(let i = 0; i < jsonResponse.categorias.length; i++) {
        // Create a new option element
        let categoria_option = $('<option></option>').val(jsonResponse.categorias[i].id).text(jsonResponse.categorias[i].nombre);
        // Append the option to the select
        $('#categoria').append(categoria_option);
    }

}

function limpiarAll() {
    $('#subtotal_input').val("");
    $('#impuesto_input').val("");
    $('#total_input').val("");
    $('#lista_productos').empty();
}

function cargarEstadisticas(){
    var vendedor_id = $('#vendedor_modal').val();
    $.post('obtener_estadisticas.php', { vendedor_id: vendedor_id }, function(_,_,xhr) {
        if (xhr.status === 200) {
            // Parse the response text to JSON
            var jsonResponse = JSON.parse(xhr.responseText);
            // Assuming the response contains a 'status' field indicating success
            if (jsonResponse.status === 'success') {


            } else {
                mostrarMensaje(jsonResponse.message, "failed", 3000);

            }
        }
        $('#total_ventas').text(jsonResponse.total_ventas);
        $('#num_ventas').text(jsonResponse.num_ventas);
        $('#productos_mas_vendidos').empty();
        jsonResponse.productos_mas_vendidos.forEach(function(producto) {
            $('#productos_mas_vendidos').append('<li>' + producto.nombre + ': ' + producto.cantidad + '</li>');
        });
    });
}

function cargarVendedores(vendedor_id) {
    $.get('cargar_vendedores.php', function(data) {
        // Clear any existing options
        $(vendedor_id).empty();
        
        // Iterate over the products array
        for(let i = 0; i < data.vendedores.length; i++) {
            // Create a new option element
            let option = $('<option></option>').val(data.vendedores[i].id).text(data.vendedores[i].nombre);
            // Append the option to the select
            $(vendedor_id).append(option);
        }
    });

}

function fetchUtil(file) {
    return new Promise((resolve, reject) => {
        $.get(`${file}.php`, function(_, _, xhr) {
            if (xhr.status === 200) {
                // Parse the response text to JSON
                var jsonResponse = JSON.parse(xhr.responseText);
                resolve(jsonResponse);

            } else {
                reject('Failed to fetch data');
            }
        });
    });
}

async function obtenerProductos() {
    const jsonResponse = await fetchUtil("obtener_items_carrito");    
    // Clear any existing options
    $('#lista_productos').empty();

    // Assuming the response contains a 'status' field indicating success
    if (jsonResponse.status === 'success') {
        // Update the message content and remove 'd-none' class for visibility
        mostrarMensaje(jsonResponse.message, "success", 3000);
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
        
        return jsonResponse.productos;

    } else {
        // Update the message content and style for error
        mostrarMensaje(jsonResponse.message, "failed", 3000);
    }

    // Always make the message visible
    $('#messageContainer').removeClass('d-none');

    // Set up a timeout to dismiss the message after 3 seconds
    setTimeout(function() {
        $('#messageContainer').addClass('d-none');
        $('#messageContainer').removeClass('alert-success');
        $('#messageContainer').removeClass('alert-danger');
    }, 7000);


    // return productos;
}

</script>

</body>
</html>



