<?php

$dsn = "mysql:host=localhost;dbname=gestion_ventas";
$dbusername = "root";
$dbpassword = "123456";



try{
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    echo "Connection failed: ". $e->getMessage();
};

function makeQuery($pdo, $queryInput, $param = null){
    if ($param != null){
        $query = $queryInput;
        $stmt = $pdo->prepare($query);
        $stmt->execute($param);
        return $stmt;
    } else{

    }

};


?>

