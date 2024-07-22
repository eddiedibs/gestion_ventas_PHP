<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once "validator_handler.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if vendedor_id is provided
        if (isset($_POST['vendedor_id'])) {
            $vendedor_id = $_POST['vendedor_id'];
            
            // Include database connection file
            require_once 'conexion.php'; 

            // Prepare and execute SQL queries using PDO
            $stmt = makeQuery($pdo, "SELECT id, total FROM ventas WHERE vendedor_id = ?",
                                     [$vendedor_id]);

            $row_total_ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $num_ventas = count($row_total_ventas);
            $productos_mas_vendidos = [];
            $productos_ids = [];
            $productos_vendidos = [];
            $total_ventas = 0;
            foreach ($row_total_ventas as $venta) {
                $stmt = makeQuery($pdo, 
                            "SELECT producto_id, cantidad FROM productos_ventas WHERE venta_id = ?;",
                        [$venta["id"]]);
    
                $total_ventas = $total_ventas + $venta["total"];
                
                $productos_de_venta = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($productos_de_venta as $producto_venta) {
                    $productos_vendidos[] = $producto_venta["cantidad"]; // Dynamically add the producto_id to the array
                    $productos_ids[] = $producto_venta["producto_id"]; // Dynamically add the producto_id to the array
                }
            }

            for ($i = 0; $i < count($productos_ids); $i++) {
                $stmt = makeQuery($pdo, "SELECT id, nombre FROM productos WHERE id = ?;",
                                    [$productos_ids[$i]]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                $product_data = array(
                    "id" => $producto["id"],
                    "nombre" => $producto["nombre"],
                    "cantidad" => $productos_vendidos[$i],
                );
                $productos_mas_vendidos[] = $product_data;

            }

            // Set header and return JSON response
            header('Content-Type: application/json');
            $return_data = json_encode(array(
                "status" => "success",
                'total_ventas' => stripDecimal($total_ventas)."$",
                'num_ventas' => $num_ventas,
                'productos_mas_vendidos' => $productos_mas_vendidos
            ));

            // Clean up
            $pdo = null;
            $stmt = null;

            die($return_data);

        } 

    } catch (PDOException $e) {
        // Handle PDO errors
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode(), $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "failed",
            "message" => $error_msg
        ]);
    }
} else {
    header("Location: ./index.php");
}
?>
