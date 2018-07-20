
<?php
/* ------------------------------------------------------------- */
/* Auto generated setup file, coded by Ali Deym © 2018           */
/* DO NOT MESS WITH THIS CONFIG FILE.                            */
/* ReserveSystem Licensed to Ali Deym © 2018 under MIT. License. */
/* Visit LICENSE file for more information.                      */
/* ------------------------------------------------------------- */

// Configurations.
$RS_HOSTNAME = 'localhost';
$RS_USERNAME = 'reservesys';
$RS_PASSWORD = '124';
$RS_DATABASE = 'reservesys';

$db = mysqli_connect($RS_HOSTNAME, $RS_USERNAME, $RS_PASSWORD, $RS_DATABASE);

// List of executes.
$RS_EXECS = array();



// Execute a query, and store it for free-ing the results.
function rs_exec($data)
{
    $qry = mysqli_query($db, $data);

    array_push($RS_EXECS, $qry);

    return $qry;
}

// Shortcut for mysqli_fetch_assoc.
function rs_assoc($qry)
{
    return mysqli_fetch_assoc($qry);
}

// Free all remaining results from mysqli and also closes the sql connection.
function rs_quit()
{
    foreach ($RS_EXECS as $val) {
        mysqli_free_result($val);
    }

    mysqli_close($db);
}
