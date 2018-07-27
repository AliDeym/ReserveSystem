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
$field = $request[2];


// Authenticate.
rs_auth($code, $auth);


$classes = null;

try {
    $classes = rs_get_all("classes", "field_id", (int)$field, "eq");

    foreach ($classes as $k => $v) {
        
        // Convert all times in classes to epoch.
        $classes[$k]["created_at"] = strtotime($classes[$k]["created_at"]);
        $classes[$k]["start_date"] = strtotime($classes[$k]["start_date"]);
        $classes[$k]["finish_date"] = strtotime($classes[$k]["finish_date"]);


    }
} catch (Exception $ex) {
    return Response::Fail(
        Err::InvalidArgumentsType, 
        "Department ID must be a number."
    );
}

return Response::Success($classes);