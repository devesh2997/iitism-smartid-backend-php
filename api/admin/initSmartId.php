<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/user.php';

include_once '../utils/validate-jwt.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

//api authorization
$jwt = isset($_POST["jwt"]) ? $_POST["jwt"] : "";

$JWT_Verify = new VerifyJWT();
$validJWT = $JWT_Verify->verifyAdmin($jwt,$db);
if (!$validJWT) {
    http_response_code(401);
    echo json_encode(
        array("message" => "Unauthorized")
    );
    exit();
}


// instantiate user object
$user = new User($db);

$smartid_no = isset($_POST['smartid_no']) ? $_POST['smartid_no'] : die();
$id = isset($_POST['id']) ? $_POST['id'] : die();
$user->id = $id;

$user_exists = $user->userExists();

// check if user exists and if password is correct
if ($user_exists && $user->initializeSmartId($id, $smartid_no)) {


    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(array("message" => "SmartID record was created.", "success" => true));
} else {
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("error" => "Unable to create user.", "success" => false));
}
