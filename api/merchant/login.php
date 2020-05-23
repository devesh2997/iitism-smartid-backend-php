<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/merchant.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate merchant object
$merchant = new Merchant($db);


$merchant->id = isset($_POST['id']) ? $_POST['id'] : die();
$password = isset($_POST['password']) ? $_POST['password'] : die();

$merchant_exists = $merchant->merchantExists();



// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


// check if merchant exists and if password is correct
// if ($merchant_exists && $merchant->verifyPassword($password)) {
if ($merchant_exists) {

    $merchant_item = array(
        "id" => $merchant->id,
        "business_name" => $merchant->business_name,
        "created_at" => $merchant->created_at,
        "first_name" => $merchant->first_name,
        "middle_name" => $merchant->middle_name,
        "last_name" => $merchant->last_name,
        "email" => $merchant->email,
        "mobile_no" => $merchant->mobile_no,
        "updated_at" => $merchant->updated_at,
    );

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $merchant->id,
            "auth_id" => $merchant->auth_id
        )
    );

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "merchant" => $merchant_item,
            "jwt" => $jwt,
            "success" => true
        )
    );
    exit();
} else {
    //login failed

    // set response code
    http_response_code(401);

    // tell the merchant login failed
    echo json_encode(array("error" => "Login failed.", "success" => false));
    exit();
}
