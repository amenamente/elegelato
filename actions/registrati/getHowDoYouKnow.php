<?php
require_once '../../lib/config.php';

$conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
if (mysqli_connect_error()) {
    die("Connessione al database fallita");
}
if (! mysqli_set_charset($conn, "utf8"))
    die("Impossibile impostare il charset");

$toJSON = [];
$sql = "SELECT hCode, description FROM HOW_DO_YOU_KNOW ORDER BY hCode ASC;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 1) {
    while($row = mysqli_fetch_assoc($result)){
        array_push($toJSON, [
            'hCode' => $row['hCode'],
            'description' => $row['description']
        ]);
    }
}
mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($toJSON);
