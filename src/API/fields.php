<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Run API's base.
require_once("base.php");

rs_minargs(3);

// Get the parameters.
$code = $request[0];
$auth = $request[1];
$department = $request[2];


// Authenticate.
rs_auth($code, $auth);


$fields = null;

try {
    $fields = rs_get_all("fields", "department_id", intval($department), "eq");
} catch (Exception $ex) {
    return Response::Fail(
        Err::InvalidArgumentsType, 
        "Department ID must be a number."
    );
}

return Response::Success($fields);