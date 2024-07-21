<?php


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $carrito_id = $_SESSION['carrito_id'];
  $cliente_id = $_SESSION['cliente_id']; // Assume cliente_id is stored in session
  $producto_id = $_POST['producto_id'];
  $cantidad = $_POST['cantidad'];
  $accion = $_POST['accion'];
  
  try {
      require_once 'conexion.php'; 

  
      $stmt = makeQuery($pdo, 
                "SELECT nombre, cantidad_total FROM productos WHERE id = ?",
                [$producto_id]);
      $producto = $stmt->fetch();
      $nombre_producto = $producto['nombre'];
      $cantidad_total_producto = $producto['cantidad_total'];
      // Check if product already in cart
      $stmt = makeQuery($pdo, 
                "SELECT id, producto_id, cantidad FROM items_carrito WHERE carrito_id = ? AND producto_id = ?", 
                [$carrito_id, $producto_id]);
      $item_carrito = $stmt->fetch();
      if ($cantidad > $cantidad_total_producto){
        $return_data = json_encode(array(
            "status" => "failed",
            "message" => "La cantidad del producto ingresado '$nombre_producto' excede la cantidad total existente ($cantidad_total_producto)",
        ));
        // $pdo = null;
        $stmt = null;
        die($return_data);
      }
      
      if(!$item_carrito && $accion == "agregar_producto"){
        $stmt = makeQuery($pdo, 
                    "INSERT INTO items_carrito (carrito_id, producto_id, cantidad) VALUES (?, ?, ?)", 
                    [$carrito_id, $producto_id, $cantidad]);

        
      }elseif(!$item_carrito && $accion == "eliminar_producto"){
          $return_data = json_encode(array(
            "status" => "failed",
            "message" => "No dispones de productos en carrito.",
        ));
        // $pdo = null;
        $stmt = null;
        die($return_data);

        
      } elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] <= $cantidad){
        // Update cantidad
        $stmt = makeQuery($pdo, 
                "DELETE FROM items_carrito WHERE id = ?", 
                [$item_carrito["id"]]);
        $stmt = null;
        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "status" => "success",
            "message" => "Producto eliminado con exito.",
        ));
        die($return_data);

      } elseif ($item_carrito && $accion == "agregar_producto" && $item_carrito['cantidad'] + $cantidad > $producto['cantidad_total']){

        $return_data = json_encode(array(
            "status" => "failed",
            "message" => "La cantidad del producto ingresado '$nombre_producto' excede la cantidad total existente ($cantidad_total_producto)",
        ));
        // $pdo = null;
        $stmt = null;
        die($return_data);

      }elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] + $cantidad > $producto['cantidad_total']){

        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad - ? WHERE carrito_id = ? AND producto_id = ?", 
                [$producto['cantidad_total'], $carrito_id, $producto_id]);

      } elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] + $cantidad <= $producto['cantidad_total']){

        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad - ? WHERE carrito_id = ? AND producto_id = ?", 
                [$cantidad, $carrito_id, $producto_id]);

      } else{
        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad + ? WHERE carrito_id = ? AND producto_id = ?", 
                [$cantidad, $carrito_id, $producto_id]);
      }

      $pdo = null;
      $stmt = null;
      header('Content-Type: application/json');
      $return_data = json_encode(array(
          "status" => "success",
          "message" => "Producto agregado con exito.",
      ));
      die($return_data);
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

