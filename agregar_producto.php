<?php


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $cliente_id = $_SESSION['cliente_id']; // Assume cliente_id is stored in session
  $producto_id = $_POST['producto_id'];
  $cantidad = $_POST['cantidad'];
  
  try {
      require_once 'conexion.php'; 
      // Check if cart exists for user
      $cart_info_stmt = makeQuery($pdo, 
                      "SELECT id FROM carritos WHERE cliente_id = ?",
                      [$cliente_id]);
      $cart = $cart_info_stmt->fetch();

      if ($cart) {
          $carrito_id = $cart['id'];
      } else {
          // Create new cart
          makeQuery($pdo, 
                  "INSERT INTO carritos (cliente_id) VALUES (?)",
                  [$cliente_id]);
          $carrito_id = $pdo->lastInsertId();
      }
  
      // Check if product already in cart
      $stmt = makeQuery($pdo, 
          "SELECT id FROM items_carrito WHERE carrito_id = ? AND producto_id = ?", 
          [$carrito_id, $producto_id]);
      $cart_item = $stmt->fetch();
  
      if ($cart_item) {
          // Update cantidad
          $stmt = makeQuery($pdo, 
                  "UPDATE items_carrito SET cantidad = cantidad + ? WHERE carrito_id = ? AND producto_id = ?", 
                  [$cantidad, $carrito_id, $producto_id]);

      } else {
          // Add new item to cart
          $stmt = makeQuery($pdo, 
                "INSERT INTO items_carrito (carrito_id, producto_id, cantidad) VALUES (?, ?, ?)", 
                [$carrito_id, $producto_id, $cantidad]);
      }
  
      echo "Product added to cart successfully!";
      } catch (PDOException $e) {
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode(), $e->getMessage());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
      }


} else{
    header("Location: ./index.php");

}

?>

