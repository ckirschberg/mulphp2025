<?php
require '../.env';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, XRequested-With");


$servername = "localhost";
$username = "root";
$password = getenv('PASSWORD');

$request = $_SERVER['REQUEST_METHOD'];

// Create an if statement to check if this is a GET request

try {
  $conn = new PDO("mysql:host=$servername;dbname=mul2025", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $statement = $conn->query("SELECT * FROM cats");
  $result = $statement->fetchAll();

  echo json_encode($result);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// else check if this is a POST request and write "You wrote a POST request" back


?>