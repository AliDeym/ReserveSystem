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

require(".core.php");


// Get Request parameters.
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

