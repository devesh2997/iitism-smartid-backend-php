<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);


$user->id = isset($_POST['id']) ? $_POST['id'] : die();
$password = isset($_POST['password']) ? $_POST['password'] : die();

$user_exists = $user->userExists();



// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


// check if user exists and if password is correct
// if ($user_exists && $user->verifyPassword($password)) {
if ($user_exists) {

    $user_item = array(
        "id" => $user->id,
        "auth_id" => $user->auth_id,
        "created_date" => $user->created_date,
        "status" => $user->status,
        "remark" => $user->remark,
        "first_name" => $user->first_name,
        "middle_name" => $user->middle_name,
        "last_name" => $user->last_name,
        "sex" => $user->sex,
        "category" => $user->category,
        "allocated_category" => $user->allocated_category,
        "dob" => $user->dob,
        "email" => $user->email,
        "photopath" => $user->photopath,
        "marital_status" => $user->marital_status,
        "physically_challenged" => $user->physically_challenged,
        "dept_id" => $user->dept_id,
        "updated" => $user->updated,
        "smartid_no" => $user->smartid_no,
        "balance" => $user->balance
    );

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "auth_id" => $user->auth_id
        )
    );

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "user" => $user_item,
            "jwt" => $jwt,
            "success" => true
        )
    );
    exit();
} else {
    //login failed

    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("error" => "Login failed.", "success" => false));
    exit();
}
