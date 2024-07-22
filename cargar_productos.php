<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        if ($_GET["categoria_id"]){
            $categoria_id = $_GET["categoria_id"];
            require_once 'conexion.php'; 
            $stmt = makeQuery($pdo, "SELECT * FROM productos WHERE categoria_id = ?;",
                            [$categoria_id]);
            header('Content-Type: application/json');
            $return_data = json_encode(array(
                "status" => "success",
                "products" => $stmt->fetchAll(),
            ));
                
            $pdo = null;
            $stmt = null;
    
            die($return_data);
        }


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

