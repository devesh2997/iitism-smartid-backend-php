<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../utils/validate-jwt.php';

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// instantiate database and user object
$database = new Database();
$db = $database->getConnection();

//api authorization
$jwt = isset($_POST["jwt"]) ? $_POST["jwt"] : "";

$JWT_Verify = new VerifyJWT();
$validJWT = $JWT_Verify->verifyUser($jwt,$db);
if (!$validJWT) {
    http_response_code(401);
    echo json_encode(
        array("message" => "Unauthorized")
    );
    exit();
}

// initialize object
$user = new User($db);

// set ID property of record to read
$user->id = isset($_POST['id']) ? $_POST['id'] : die();

// query users
$stmt = $user->getTransactions();

// check if more than 0 record found
if ($stmt != null) {

    // users array
    $transactions_arr = array();
    $transactions_arr["transactions"] = array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $transaction_item = array(
            "id" => $id,
            "type" => $type,
            "amount"=>$amount,
            "created_at" => $created_at,
            "merchant"=>array(
                "business_name" => $business_name,
                "first_name" => $first_name,
                "middle_name" => $middle_name,
                "last_name" => $last_name,
            )            
        );

        array_push($transactions_arr["transactions"], $transaction_item);
    }

    $transactions_arr['success'] = true;

    // set response code - 200 OK
    http_response_code(200);

    // show users data in json format
    echo json_encode($transactions_arr);
} else {

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("error" => "No transactions found.", "success" => false)
    );
}
