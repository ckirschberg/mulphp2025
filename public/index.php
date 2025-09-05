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
        // Læs query-parametre: limit og offset
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        // Rimelige grænser/validering
        if ($limit < 1) $limit = 1;
        if ($limit > 100) $limit = 100; // undgå alt for store svar
        if ($offset < 0) $offset = 0;

        // (Valgfrit) total antal rækker til metadata
        $total = (int) $conn->query("SELECT COUNT(*) FROM cats")->fetchColumn();

        // Hent pagineret data – bind som ints!
        $stmt = $conn->prepare("SELECT * FROM cats ORDER BY catsid ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Returnér data + pagination-info
        echo json_encode([
            'data' => $rows,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
                'next_offset' => ($offset + $limit < $total) ? $offset + $limit : null,
                'prev_offset' => ($offset - $limit >= 0) ? $offset - $limit : null
            ]
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
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