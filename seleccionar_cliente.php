<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        

        $cedula_rif = $_GET['cedula_rif'];

        $cliente_info_stmt = makeQuery($pdo, 
                        "SELECT * FROM clientes WHERE cedula_rif = ?",
                        [$cedula_rif]);
        
        $cliente_info = $cliente_info_stmt->fetch(PDO::FETCH_ASSOC);
        // Check if $cliente_info is false
        if (!$cliente_info) {
            $return_data = json_encode(array(
                "status" => "failed",
                "message" => "El cliente con numero de documento ".$cedula_rif." no se encuentra registrado.",
            ));
            die($return_data);
        }else{
            // Check if cart exists for user
            $stmt = makeQuery($pdo, 
                        "SELECT id FROM carritos WHERE cliente_id = ?",
                        [$cliente_info["id"]]);
            $cart = $stmt->fetch();

            if ($cart) {
                $carrito_id = $cart['id'];
            } else {
                // Create new cart
                makeQuery($pdo, 
                        "INSERT INTO carritos (cliente_id) VALUES (?)",
                        [$cliente_info["id"]]);
                $carrito_id = $pdo->lastInsertId();
            }
            $_SESSION['carrito_id'] = $carrito_id; // Assume cliente_id is stored in session
            header('Content-Type: application/json');
            $return_data = json_encode(array(
                "status" => "success",
                "message" => "El cliente con numero de documento ".$cedula_rif." ha sido seleccionado.",
                "cliente_info" => $cliente_info,
            ));
                
            $pdo = null;
            $cliente_info_stmt = null;
    
            $_SESSION['cliente_id'] = $cliente_info["id"];
            die($return_data);
        }


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

