<?php
include 'conexion.php';

$vendedor_id = $_POST['vendedor_id'];

$sql_total_ventas = "SELECT SUM(total) as total_ventas, COUNT(*) as num_ventas FROM ventas WHERE vendedor_id = $vendedor_id";
$result_total_ventas = mysqli_query($conn, $sql_total_ventas);
$row_total_ventas = mysqli_fetch_assoc($result_total_ventas);

$sql_productos_mas_vendidos = "SELECT p.nombre, SUM(dv.cantidad) as cantidad 
                               FROM detalles_ventas dv 
                               JOIN productos p ON dv.producto_id = p.id 
                               JOIN ventas v ON dv.venta_id = v.id 
                               WHERE v.vendedor_id = $vendedor_id 
                               GROUP BY p.id 
                               ORDER BY cantidad DESC 
                               LIMIT 5";
$result_productos_mas_vendidos = mysqli_query($conn, $sql_productos_mas_vendidos);

$productos_mas_vendidos = [];
while ($row = mysqli_fetch_assoc($result_productos_mas_vendidos)) {
    $productos_mas_vendidos[] = $row;
}

$datos = [
    'total_ventas' => $row_total_ventas['total_ventas'],
    'num_ventas' => $row_total_ventas['num_ventas'],
    'productos_mas_vendidos' => $productos_mas_vendidos
];

echo json_encode($datos);

mysqli_close($conn);
?>

