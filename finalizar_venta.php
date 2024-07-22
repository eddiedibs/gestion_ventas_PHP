<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $cliente_id = $_SESSION['cliente_id'];
        $vendedor_id = $_POST['vendedor_id']; // Ejemplo, esto debería ser dinámico basado en el vendedor autenticado
        $subtotal = $_POST['subtotal'];
        $impuesto = $_POST['impuesto'];
        $productos = $_POST['productos'];
        $total = $_POST['total'];

        require_once 'conexion.php'; 
        $stmt = makeQuery($pdo, 
                    "INSERT INTO ventas (cliente_id, vendedor_id, subtotal, impuesto, total) VALUES (?, ?, ?, ?, ?)", 
                    [$cliente_id, $vendedor_id, $subtotal, $impuesto, $total]);
        $venta_id = $pdo->lastInsertId();
        foreach ($productos as $producto) {
            $stmt = makeQuery($pdo, 
                        "INSERT INTO productos_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)", 
                        [$venta_id, $producto["id"], $producto["cantidad"], floatval($producto["subtotal"])]);

            $stmt = makeQuery($pdo, 
                        "UPDATE productos SET cantidad_total = cantidad_total - ? WHERE id = ?", 
                        [$producto["cantidad"], $producto["id"]]);
            
            $stmt = makeQuery($pdo, 
                        "DELETE FROM items_carrito WHERE producto_id = ?",
                        [$producto["id"]]);
        }


        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "status" => "success",
            "message" => "Venta realizada correctamente.",
        ));
            
        $pdo = null;
        $stmt = null;

        die($return_data);
        // header("Location: ./index.php");

    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode(), $e->getMessage());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}
// $sql = "INSERT INTO ventas (cliente_id, vendedor_id, fecha, subtotal, impuesto, total) VALUES ('$cliente_id', '$vendedor_id', '$fecha', '$subtotal', '$impuesto', '$total')";

// try {
//     require_once 'conexion.php'; 
//     $stmt = makeQuery($pdo, 
//             "INSERT INTO clientes (nombre, cedula_rif, telefono, direccion) VALUES (?,?,?,?);", 
//             [$nombre,$cedula_rif,$telefono,$direccion]);
//     $cliente_id = $pdo->lastInsertId();

//     header('Content-Type: application/json');
//     $return_data = json_encode(array(
//         "status" => "success",
//         "message" => "Cliente '$cedula_rif' creado exitosamente."
//     ));
//     // Create new cart
//     makeQuery($pdo, 
//             "INSERT INTO carritos (cliente_id) VALUES (?)",
//             [$cliente_id]);
//     $carrito_id = $pdo->lastInsertId();
//     $_SESSION['cliente_id'] = $cliente_id; // Assume cliente_id is stored in session
//     $_SESSION['carrito_id'] = $carrito_id; // Assume cliente_id is stored in session
//     $_SESSION['cedula_rif'] = $cedula_rif;
//     $pdo = null;
//     $stmt = null;
//     die($return_data);
//     // header("Location: ./index.php");

// } catch (PDOException $e){
//     require_once 'error_handler.php'; 
//     $error_msg = handleError($e->getCode(), $e->getMessage());
//     $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
//     die($return_data);
// }

// mysqli_close($conn);
?>

