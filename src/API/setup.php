<?php
/* ------------------------------------------------------------- */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Allow the API to be accessed from anywhere, no more CORS header error.
header("Access-Control-Allow-Origin: *");

// Turn off all error reporting so rest API will return pure json, no warns.
//error_reporting(0);

require_once("response.php");


// Check if the project is already set up and has a working config or not.
if (file_exists(".core.php")) {
    return Response::Fail(
        Err::DuplicateEntry,
        "ReserveSystem has already been setted up."
    );
}

// Get Request parameters.
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

// Check wether the arguments are less than required or not.
if (count($request) < 4) {
    return Response::Fail(
        Err::InvalidArgumentsCount,
        "Required Arguments: Host, Username, Password, Databasename"
    );
}



// Get the data out of parameters.
$host = $request[0];
$user = $request[1];
$pass = $request[2];
$dbse = $request[3];




// Establish a database connection for testing.
$db = mysqli_connect($host, $user, $pass, $dbse);


// Check wether our database established or not.
if (mysqli_connect_error()) {
    return Response::Fail(
        Err::EstablishConnection,
        "MySQL Connection Error: " . mysqli_connect_error()
    );
}

mysqli_set_charset($db, 'utf8');


// Creation query.
$creation_query = "CREATE TABLE IF NOT EXISTS `classes` (
    id INT NOT NULL AUTO_INCREMENT,
    field_id INT NOT NULL,
    name VARCHAR (255) NOT NULL,
    price INT NOT NULL,
    start_date DATETIME NOT NULL,
    finish_date DATETIME NOT NULL,
    created_at DATETIME NOT NULL,

    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS `users` (
    code VARCHAR(32) NOT NULL,
    firstname VARCHAR(128) NOT NULL,
    lastname VARCHAR(128) NOT NULL,
    password VARCHAR(32) NOT NULL,
    birthdate DATE NOT NULL,
    phonenumber VARCHAR(32) NOT NULL,
    fathername VARCHAR(128),
    registered_at DATETIME NOT NULL,
    edited_at DATETIME NOT NULL,
    administrator BIT DEFAULT 0,

    PRIMARY KEY (code)
);
CREATE TABLE IF NOT EXISTS `reserves` (
    id INT NOT NULL AUTO_INCREMENT,
    user_code VARCHAR(32) NOT NULL,
    class_id INT NOT NULL,
    accepted BIT DEFAULT 0,
    paid INT DEFAULT 0,
    reserved_at DATETIME NOT NULL,
    accepted_at DATETIME NOT NULL,

    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS `fields` (
    id INT NOT NULL AUTO_INCREMENT,
    department_id INT NOT NULL,
    name VARCHAR (255) NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS `departments` (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR (255) NOT NULL,
    PRIMARY KEY (id)
);";

// Create the required tables in database.
mysqli_multi_query(
    $db, 
    $creation_query
);

// Check for SQL errors.
if (mysqli_error($db)) {
    return Response::Fail(
        Err::QueryError,
        "Creating MySQL Tables failed. check your PHP configuration. make sure you have MySqli libraries installed.\nMySQL error: " . mysqli_error($db)
    );
}

$f = fopen("./.core.php", "w");

// In case that our core file couldn't be created.
if (!$f) {
    return Response::Fail(
        Err::FileCreationError,
        "Error while trying to create files for setup, make sure PHP user is sudo."
    );
}

fwrite($f, "
<?php
/* ------------------------------------------------------------- */
/* Auto generated setup file, coded by Ali Deym © 2018           */
/* DO NOT MESS WITH THIS CONFIG FILE.                            */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Configurations.
\$RS_HOSTNAME = '$host';
\$RS_USERNAME = '$user';
\$RS_PASSWORD = '$pass';
\$RS_DATABASE = '$dbse';

\$db = mysqli_connect(\$RS_HOSTNAME, \$RS_USERNAME, \$RS_PASSWORD, \$RS_DATABASE);

// List of executes.
\$RS_EXECS = array();



// Execute a query, and store it for free-ing the results.
function rs_exec(\$data)
{
    \$qry = mysqli_query(\$db, \$data);

    array_push(\$RS_EXECS, \$qry);

    return \$qry;
}

// Shortcut for mysqli_fetch_assoc.
function rs_assoc(\$qry)
{
    return mysqli_fetch_assoc(\$qry);
}

// Free all remaining results from mysqli and also closes the sql connection.
function rs_quit()
{
    foreach (\$RS_EXECS as \$val) {
        mysqli_free_result(\$val);
    }

    mysqli_close(\$db);
}
");

fclose($f);

return Response::Success("Install success.");