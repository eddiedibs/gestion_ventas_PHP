<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $id_producto = $_GET['id']; // Make sure to validate/sanitize this input        $query = "SELECT * FROM productos;";
        $stmt = makeQuery($pdo, 
                    "SELECT cantidad_total FROM productos WHERE id = ?",
                    [$id_producto]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $producto_cantidad_disponible = $resultado['cantidad_total'];
        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "producto_cantidad_disponible" => $producto_cantidad_disponible,
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

?>

