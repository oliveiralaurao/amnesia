<?php

$mysqli = new mysqli("localhost", "root", "", "amnesia");

if($mysqli->connect_errno) {
    echo "Falha na conexão: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
}

$conn->begin_transaction();

?>
