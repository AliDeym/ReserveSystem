<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Run API's base.
require_once("base.php");

rs_minargs(2);

// Get the parameters.
$code = $request[0];
$auth = $request[1];


// Authenticate.
rs_auth($code, $auth);


$departments = rs_get_all("departments");


return Response::Success($departments);