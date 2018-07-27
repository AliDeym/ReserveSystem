<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Allow the API to be accessed from anywhere, no more CORS header error.
header("Access-Control-Allow-Origin: *");

// Turn off all error reporting so rest API will return pure json, no warns.
error_reporting(0);

require_once("response.php");

// Check if the project is already set up and has a working config or not.
if (file_exists(".core.php")) {
    return Response::Success(
        "App is running."
    );
}

return Response::Fail(
    Err::InvalidSetup, 
    "ReserveSystem is not setted up yet."
);