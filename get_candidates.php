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
    
    // Realiza una consulta para obtener la lista de candidatos
    $query = "SELECT ID_CANDIDATO, NOMBRE_CANDIDATO FROM candidato";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . mysqli_error($connection));
    }

    // Crea un array para almacenar los datos de los candidatos
    $candidates = array();
    
    // Recorre el resultado de la consulta y agregar los datos al array
    while ($row = mysqli_fetch_assoc($result)) {
        $candidates[] = $row;
    }
    
    // Cierra la conexión
    mysqli_close($connection);

    // Devuelve la lista de candidatos como JSON
    
    $jsonCandidates = json_encode($candidates, JSON_UNESCAPED_UNICODE);
    if ($jsonCandidates === false) {
        throw new Exception("Error en la codificación JSON: " . json_last_error_msg());
    }
    
    echo $jsonCandidates;
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}
?>


