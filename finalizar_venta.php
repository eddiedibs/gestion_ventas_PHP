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

    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode(), $e->getMessage());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}
?>

