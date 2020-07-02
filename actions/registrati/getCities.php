<?php
require_once '../../lib/config.php';

if(!empty($_GET)){
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error()) {
        die("Connessione al database fallita");
    }
    if (! mysqli_set_charset($conn, "utf8"))
        die("Impossibile impostare il charset");
    $toJSON = array();
    if(isset($_GET['provinces'])){
        $sql = "SELECT DISTINCT province FROM CITY ORDER BY province ASC;";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 1) {
            while($row = mysqli_fetch_assoc($result)){
                array_push($toJSON, $row['province']);
            }
        }
    } else if(isset($_GET['province'])){
        if ($stmt = mysqli_prepare($conn, "SELECT DISTINCT cCode, city FROM CITY WHERE province LIKE ? ORDER BY city ASC;")) {
            $province = $_GET['province'];
            if (!mysqli_stmt_bind_param($stmt, "s", $province))
                die("Binding dei parametri fallito");
            if (!mysqli_stmt_execute($stmt))
                die("Esecuzione della query fallita");
            if(!mysqli_stmt_bind_result($stmt, $cCode, $city))
                die("Binding dei risultati fallito");
            while (mysqli_stmt_fetch($stmt))
                array_push($toJSON, json_encode(array(
                    'cCode' => $cCode, 
                    'comune' => $city
                )));
            mysqli_stmt_close($stmt);
            
        }
    }
    mysqli_close($conn);
    header('Content-type: application/json');
    echo json_encode($toJSON);
} else {
    http_response_code(400);
    exit;
}