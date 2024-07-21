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

      $resp_status = "";
      $resp_msg = "";
      if ($cantidad > $cantidad_total_producto){
        $resp_status = "failed";
        $resp_msg = "La cantidad del producto ingresado '$nombre_producto' excede la cantidad total existente ($cantidad_total_producto)";
      }
      
      if(!$item_carrito && $accion == "agregar_producto"){
        $stmt = makeQuery($pdo, 
                    "INSERT INTO items_carrito (carrito_id, producto_id, cantidad) VALUES (?, ?, ?)", 
                    [$carrito_id, $producto_id, $cantidad]);

        $resp_status = "success";
        $resp_msg = "Producto agregado con exito";

      }elseif(!$item_carrito && $accion == "eliminar_producto"){
        $resp_status = "failed";
        $resp_msg = "No dispones de productos en carrito.";

        
      } elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] <= $cantidad){
        // Update cantidad
        $stmt = makeQuery($pdo, 
                "DELETE FROM items_carrito WHERE id = ?", 
                [$item_carrito["id"]]);

        $resp_status = "success";
        $resp_msg = "Producto eliminado con exito.";

      } elseif ($item_carrito && $accion == "agregar_producto" && $item_carrito['cantidad'] + $cantidad > $producto['cantidad_total']){

        $resp_status = "failed";
        $resp_msg = "La cantidad del producto ingresado '$nombre_producto' excede la cantidad total existente ($cantidad_total_producto)";

      }elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] + $cantidad > $producto['cantidad_total']){

        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad - ? WHERE carrito_id = ? AND producto_id = ?", 
                [$producto['cantidad_total'], $carrito_id, $producto_id]);

        $resp_status = "success";
        $resp_msg = "Producto eliminado con exito.";
                
      } elseif ($item_carrito && $accion == "eliminar_producto" && $item_carrito['cantidad'] + $cantidad <= $producto['cantidad_total']){

        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad - ? WHERE carrito_id = ? AND producto_id = ?", 
                [$cantidad, $carrito_id, $producto_id]);
        $resp_status = "success";
        $resp_msg = "Producto eliminado con exito.";

      } else{
        // Update cantidad
        $stmt = makeQuery($pdo, 
                "UPDATE items_carrito SET cantidad = cantidad + ? WHERE carrito_id = ? AND producto_id = ?", 
                [$cantidad, $carrito_id, $producto_id]);
   
        $resp_status = "success";
        $resp_msg = "Producto agregado con exito.";
      }

      $pdo = null;
      $stmt = null;
      header('Content-Type: application/json');
      $return_data = json_encode(array(
          "status" => $resp_status,
          "message" => $resp_msg,
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

