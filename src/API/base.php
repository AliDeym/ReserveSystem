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
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));


// List of executes.
$RS_EXECS = array();

/* Minimum number of arguments for the current API method. */
/* NOTE: This method also checks for SQL Injections and clears them. */
function rs_minargs($num)
{
    global $request;
    global $db;

    if (count($request) < $num)
        return Response::Fail(Err::InvalidArgumentsCount, "Invalid arguments count.");

    
    // Prevent SQL Injections.
    foreach ($request as $k => $v) {
        $request[$k] = mysqli_real_escape_string($db, $v);
    }
}

/* This function forces the API method to use authentication. */
function rs_auth($user, $auth_code)
{
    $user_auth = rs_get("auths", "code", $user);

    if (!$user_auth) {
        return Response::Fail(Err::AuthenticationFailure, "Method requires authentication.");
    }

    if ($auth_code != $auth_code) {
        return Response::Fail(Err::AuthenticationFailure, "Method requires authentication.");
    }
}

/* Gets comparison method from shortcut strings. */
function _rs_get_shortcut($shortcut = "eq")
{
    switch (strtolower($shortcut)) {
        case "eq":
        case "=":
        case "==":
            return "=";


        case "noteq":
        case "not":
        case "ne":
        case "nq":
        case "no":
        case "!":
        case "!=":
        case "<>":
            return "<>";


        case "lt":
        case "<":
            return "<";


        case "gt":
        case "bt":
        case "mt":
        case ">":
            return ">";


        case "ltoreq":
        case "ltoe":
        case "lte":
        case "le":
        case "<=":
            return "<=";


        case "gtoreq":
        case "btoreq":
        case "mtoreq":
        case "gtoe":
        case "btoe":
        case "mtoe":
        case "gte":
        case "bte":
        case "mte":
        case "ge":
        case "be":
        case "me":
        case ">=":
            return ">=";


        default:
            return "=";
    }
}


/* Execute a query, and store it for free-ing the results.*/
function rs_exec($data, $errorSafe = false)
{
    // Get the database MySqli object from globals.
    global $db;

    $qry = mysqli_query($db, $data);

    // Check for SQL errors.
    if (mysqli_error($db)) {
        if ($errorSafe) {
            return false;
        }

        return Response::Fail(
            Err::QueryError,
            "Database Query Error: " . mysqli_error($db)
        );
    }

    array_push($RS_EXECS, $qry);

    return $qry;
}

/* All in one function to run queries easier. */
function rs_get_raw($table, $field, $value, $comparison = "eq", $fields = "*", $limit = 0, $offset = 0)
{
    $comparison_raw = _rs_get_shortcut($comparison);

    return rs_exec(
        "SELECT $fields FROM $table WHERE $field $comparison_raw "
        . (gettype($value) == "integer" ? $value : "'$value'")
        . ($limit <= 0 ? "" : " LIMIT $limit")
        . ($offset <= 0 ? "" : " OFFSET $offset")
        . ";"
    );
}

/* Same as get raw, but only returns one value, and returns the assoc array. easy to use ;). */
function rs_get($table, $field, $value, $comparison = "eq", $fields = "*")
{
    return rs_assoc(rs_get_raw($table, $field, $value, $comparison, $fields, 1));
}

/* Shortcut for mysqli_fetch_assoc. */
function rs_assoc($qry)
{
    return mysqli_fetch_assoc($qry);
}

/* Shortcut for mysqli_fetch_array. */
function rs_array($qry)
{
    return mysqli_fetch_array($qry);
}

/* Free all remaining results from mysqli and also closes the sql connection. */
function rs_quit()
{
    // Get MySqli object from globals.
    global $db;

    foreach ($RS_EXECS as $val) {
        mysqli_free_result($val);
    }

    mysqli_close($db);
}