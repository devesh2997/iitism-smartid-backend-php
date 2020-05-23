<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/admin.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate admin object
$admin = new Admin($db);


$admin->id = isset($_POST['id']) ? $_POST['id'] : die();
$password = isset($_POST['password']) ? $_POST['password'] : die();

$admin_exists = $admin->adminExists();



// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


// check if admin exists and if password is correct
// if ($admin_exists && $admin->verifyPassword($password)) {
if ($admin_exists) {

    $hasAuth = $admin->checkSmartIdAuth($admin->id);

    if (true) {
        $admin_item = array(
            "id" => $admin->id,
            "auth_id" => $admin->auth_id,
            "created_date" => $admin->created_date,
            "status" => $admin->status,
            "remark" => $admin->remark,
            "first_name" => $admin->first_name,
            "middle_name" => $admin->middle_name,
            "last_name" => $admin->last_name,
            "sex" => $admin->sex,
            "category" => $admin->category,
            "allocated_category" => $admin->allocated_category,
            "dob" => $admin->dob,
            "email" => $admin->email,
            "photopath" => $admin->photopath,
            "marital_status" => $admin->marital_status,
            "physically_challenged" => $admin->physically_challenged,
            "dept_id" => $admin->dept_id,
            "updated" => $admin->updated,
        );

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
                "id" => $admin->id,
                "auth_id" => $admin->auth_id
            )
        );

        // set response code
        http_response_code(200);

        // generate jwt
        $jwt = JWT::encode($token, $key);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "admin" => $admin_item,
                "jwt" => $jwt,
                "success" => true
            )
        );
        exit();
    } else {
        //admin does not have the required auth.

        // set response code
        http_response_code(401);

        // tell the admin login failed
        echo json_encode(array("error" => "admin does not have the required auth", "success" => false));
        exit();
    }
} else {
    //login failed

    // set response code
    http_response_code(401);

    // tell the admin login failed
    echo json_encode(array("error" => "Login failed.", "success" => false));
    exit();
}
