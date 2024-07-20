<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $carrito_id = $_GET['carrito_id'];
        $producto_id = $_GET['producto_id'];
        $query = "SELECT * FROM items_carrito WHERE carrito_id = :carrito_id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':carrito_id', $carrito_id, PDO::PARAM_INT);
        $stmt->execute();
        header('Content-Type: application/json');

        $return_data = json_encode(array(
            "products" => $stmt->fetchAll(),
        ));
            
        $pdo = null;
        $stmt = null;

        die($return_data);

    } catch (PDOException $e){
        $return_data = json_encode(array("status" => "failed", "message" => "ERRORRRRR"));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}

?>

