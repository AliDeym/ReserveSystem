<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Register.php usage:
// http=>/code/password/firstname/lastname/fathername/phonenumber/birthdate

// Run API's base.
require_once("base.php");

rs_minargs(7);


// Failure response shortcut.
function auth_fail()
{
    return Response::Fail(Err::AuthenticationFailure, "Username exists. Please log-in.");
}

function type_fail($type)
{
    $type = ucwords($type);

    return Response::Fail(Err::InvalidArgumentsType, "Invalid arguments type '$type'.");
}

function limit_fail ($type)
{
    $type = ucwords($type);

    return Response::Fail(Err::ExceedLimit, "Exceed the limit for type '$type'.");
}


// Get the parameters.
$code = $request[0];
$pass = $request[1];
$firstname = $request[2];
$lastname = $request[3];
$fathername = $request[4];
$phonenumber = $request[5];
$birthdate = $request[6];


// Get user by code.
$user = rs_get("users", "code", $code);


$date_lowlimit = date('Y-m-d', strtotime('01/01/1910'));
$date_highlimit = date('Y-m-d');

$converted_birth = date('Y-m-d', $birthdate);


// User exists:
if ($user) 
    return auth_fail();


// Cheks for validation:

if (!ctype_digit($code))
    return type_fail("user code");

if (substr($phonenumber, 0, 1) !== "0" || !ctype_digit($phonenumber))
    return type_fail("phone number");

if (!is_numeric($birthdate) || !date('Y-m-d', $birthdate))
    return type_fail("birth date");


// Passed the limit:
if ($converted_birth > $date_highlimit || $converted_birth < $date_lowlimit)
    return Response::Fail(Err::ExceedLimit, "Invalid Age.");

if (strlen($code) > 32)
    return limit_fail("user code");

if (strlen($firstname) > 128)
    return limit_fail("first name");

if (strlen($lastname) > 128)
    return limit_fail("last name");

if (strlen($phonenumber) > 128)
    return limit_fail("phone number");

if (strlen($fathername) > 128)
    return limit_fail("father name");
    


// TODO: Change MD5 to PBKDF2.
$md5 = md5($pass);
$date = date("Y-m-d H:i:s", $birthdate);


// Insert into database.
rs_exec("INSERT INTO users 
    (
        code, 
        password, 
        firstname,
        lastname,
        fathername,
        phonenumber,
        birthdate,
        registered_at,
        edited_at,
        administrator
    ) VALUES 
    (
        '$code', 
        '$md5',
        '$firstname',
        '$lastname',
        '$fathername',
        '$phonenumber',
        '$date',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        0
    );
");


return Response::Success("User created.");