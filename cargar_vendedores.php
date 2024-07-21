<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $query = "SELECT * FROM vendedores;";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "vendedores" => $stmt->fetchAll(),
        ));
            
        $pdo = null;
        $stmt = null;

        die($return_data);
        // header("Location: ./index.php");

    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}

?>

