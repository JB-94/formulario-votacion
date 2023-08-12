<?php
// Realiza la conexión a la base de datos
$connection = mysqli_connect("localhost", "root", "root", "db_formulariovotacion");
mysqli_set_charset($connection, "utf8mb4");
//indica que se deben informar todos los tipos de errores
error_reporting(E_ALL);
//habilita la visualización de errores en pantalla
ini_set('display_errors', 1);
//Interpreta los datos en formato JSON
header('Content-Type: application/json');

try {
    
    if (!$connection) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }
    
    // Verifica si se ha proporcionado el parámetro ID_REGION en la URL
    if (!isset($_GET['ID_REGION'])) {
        throw new Exception("Se requiere el parámetro ID_REGION.");
    }
    
    $idRegion = mysqli_real_escape_string($connection, $_GET['ID_REGION']);

    $query = "SELECT ID_COMUNA, NOMBRE_COMUNA FROM comuna WHERE ID_REGION = $idRegion";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . mysqli_error($connection));
    }

    $comunas = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $comunas[] = $row;
    }
    
    mysqli_close($connection);

    echo json_encode($comunas);
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}
