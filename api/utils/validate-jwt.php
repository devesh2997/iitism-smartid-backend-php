<?php
// required to decode jwt
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';


include_once '../objects/user.php';

use \Firebase\JWT\JWT;

class VerifyJWT
{
    function verifyAdmin($jwt, $db)
    {
        $user = new User($db);
        // if jwt is not empty
        if ($jwt) {

            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode($jwt, 'SmartID@IIT_ism_Dhanbad#1615536026', array('HS256'));
                // echo json_encode($decoded);

                if ($decoded->data->id != null && $decoded->data->auth_id == 'smartid'){
                    $user->id = $decoded->data->id;
                    if($user->userExists()){
                        return true;
                    }else return false;
                }
                else return false;
            } // if decode fails, it means jwt is invalid
            catch (Exception $e) {
                return false;
            }
        }
    }

    function verifyUser($jwt,$db)
    {
        // if jwt is not empty
        if ($jwt) {
            $user = new User($db);
            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode($jwt, 'SmartID@IIT_ism_Dhanbad#1615536026', array('HS256'));
                // echo json_encode($decoded);
                if ($decoded->data->id != null){
                    $user->id = $decoded->data->id;
                    if($user->userExists()){
                        return true;
                    }else return false;
                }
                else return false;
            } // if decode fails, it means jwt is invalid
            catch (Exception $e) {
                return false;
            }
        }else{
            return false;
        }
    }
}
