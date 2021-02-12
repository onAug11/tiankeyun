<?php
header('Content-type:application/json');
require_once('./class/dati.class.php');

$username = $_GET['username'];
$password = $_GET['password'];

$dati = new Dati();
$res = $dati->dati($username, $password);
echo json_encode($res);
?>