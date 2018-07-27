<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Run API's base.
require_once("base.php");

rs_minargs(2);


// Failure response shortcut.
function auth_fail()
{
    return Response::Fail(Err::AuthenticationFailure, "Username or password wrong.");
}


// Get the parameters.
$code = $request[0];
$pass = $request[1];


// Get user by code.
$user = rs_get("users", "code", $code);

// User not found:
if (!$user) 
    return auth_fail();

// Passwords are not same:
if (md5($pass) != $user["password"])
    return auth_fail();


// Generate an authentication code so user is logged in now.
$seed = (string)time();
$gen_code = md5("($seed)[$pass]{$code}($seed)");


// Insert into database.
rs_exec("INSERT INTO auths (code, auth) VALUES ('$code', '$gen_code') ON DUPLICATE KEY UPDATE auth = VALUES(auth);");

// Self explanatory.
$user_isadmin = $user["administrator"] > 0;


return Response::Success(
    array(
        "auth" => $gen_code, 
        "administrator" => $user_isadmin
    )
);