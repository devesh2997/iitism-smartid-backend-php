<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../utils/validate-jwt.php';

// instantiate database and user object
$database = new Database();
$db = $database->getConnection();

// initialize object
$user = new User($db);

// get keywords
$keywords = isset($_POST["s"]) ? $_POST["s"] : "";
$prefix = isset($_POST["prefix"]) ? $_POST["prefix"] : "";

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

// query users
$stmt = $user->search($keywords,$prefix);
$num = $stmt->rowCount();

// check if more than 0 record found
if ($num > 0) {

    // users array
    $users_arr = array();
    $users_arr["users"] = array();

    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $user_item = array(
            "id" => $id,
            "auth_id" => $auth_id,
            "created_date" => $created_date,
            "status" => $status,
            "remark" => $remark,
            "first_name" => $first_name,
            "middle_name" => $middle_name,
            "last_name" => $last_name,
            "sex" => $sex,
            "category" => $category,
            "allocated_category" => $allocated_category,
            "dob" => $dob,
            "email" => $email,
            "photopath" => $photopath,
            "marital_status" => $marital_status,
            "physically_challenged" => $physically_challenged,
            "dept_id" => $dept_id,
            "updated" => $updated,
            "branch_id" => $branch_id,
            "course_id" => $course_id,
            "smartid_no" => $smartid_no,
            "balance" => $balance
        );

        array_push($users_arr["users"], $user_item);
    }
    $users_arr['success'] = true;

    // set response code - 200 OK
    http_response_code(200);

    // show users data in json format
    echo json_encode($users_arr);
} else {

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("error" => "No users found.", "success" => false)
    );
}
