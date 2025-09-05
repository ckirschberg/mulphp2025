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

$conn = new PDO("mysql:host=$servername;dbname=mul2025", $username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// echo $uri;

// $uri = explode('/', $uri);


if ($request === 'GET' && $uri === '/pips') {
    try {
        $statement = $conn->query("SELECT * FROM cats");
        $result = $statement->fetchAll();

        echo json_encode($result);
    } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    }
} 
else if ($request === 'POST' && $uri === '/pips') {
    $input = (array) json_decode(file_get_contents('php://input'), true);

    $name = $input["name"];
    $color = $input["color"];

    $length = strlen($color);

    if ($name !== '' ) { // validering: overholde regler for at gemme korrekt data
        if ($length <= 10) {
            $data = [
                'name' => $name,
                'color' => $color
            ];
            $sql = "INSERT INTO cats VALUES (default, :name, :color)";
            $stmt= $conn->prepare($sql);
            $stmt->execute($data);


            $id = $conn->lastInsertId();
            $cat = (object) $input;
            $cat->id = $id;

            echo json_encode($cat);
        }
        else {
            echo json_encode("Color må højst være 10 karakterer");
        }
    } else {
        echo json_encode("Navn skal udfyldes");
    }

    // echo $name;
    

}
// else check if this is a POST request and write "You wrote a POST request" back


?>