<?php
include 'conexion.php';

$cliente_id = $_POST['cliente_id'];
$productos = $_POST['productos'];
$subtotal = $_POST['subtotal'];
$impuesto = $_POST['impuesto'];
$total = $_POST['total'];
$vendedor_id = 1; // Ejemplo, esto debería ser dinámico basado en el vendedor autenticado
$fecha = date('Y-m-d');

$sql = "INSERT INTO ventas (cliente_id, vendedor_id, fecha, subtotal, impuesto, total) VALUES ('$cliente_id', '$vendedor_id', '$fecha', '$subtotal', '$impuesto', '$total')";

if (mysqli_query($conn, $sql)) {
    $venta_id = mysqli_insert_id($conn);

    foreach ($productos as $producto) {
        $producto_id = $producto['id'];
        $cantidad = $producto['cantidad'];
        $precio = $producto['precio'];

        $sql_detalle = "INSERT INTO detalles_ventas (venta_id, producto_id, cantidad, precio) VALUES ('$venta_id', '$producto_id', '$cantidad', '$precio')";
        mysqli_query($conn, $sql_detalle);
    }

    echo "Venta finalizada exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

