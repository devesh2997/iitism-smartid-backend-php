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
$user->getById();

// check if more than 0 record found
if ($user->auth_id != null) {

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
        "branch_id" => $user->branch_id,
        "course_id" => $user->course_id,
        "smartid_no" => $user->smartid_no,
        "balance" => $user->balance
    );

    // set response code - 200 OK
    http_response_code(200);

    $res = array();
    $res['user'] = $user_item;
    $res['success'] = true;


    // show users data in json format
    echo json_encode($res);
} else {

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("error" => "User does not exist","success"=>false)
    );
}
