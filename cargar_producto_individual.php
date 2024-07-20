<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $id_producto = $_GET['id']; // Make sure to validate/sanitize this input        $query = "SELECT * FROM productos;";
        $query = "SELECT cantidad_total FROM productos WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        
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
        $return_data = json_encode(array("status" => "failed", "message" => "ERRORRRRR"));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}

?>

