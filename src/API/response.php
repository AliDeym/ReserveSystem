<?php
abstract class Err {
    /* Invalid parameters count. */
    const InvalidArgumentsCount = 0;
    
    /* Invalid parameters type. */
    const InvalidArgumentsType = 1;

    /* Database connection problem. */
    const EstablishConnection = 2;

    /* Case of duplicates, can be duplicate install or anything that is dupe. */
    const DuplicateEntry = 3;

    /* Missing setup file, re-configure the system in order to make it work. */
    const InvalidSetup = 4;

    /* Query errors or fails. */
    const QueryError = 5;

    /* Fail when trying to make a file. */
    const FileCreationError = 6;

    /* Failed to authenticate using username and password. */
    const AuthenticationFailure = 7;

    /* Username already exists. */
    const UserExists = 8;

    /* Parameter or value exceed it's limit. */
    const ExceedLimit = 9;

    /* Get error name of error constant. */
    public static function getName($errorType) 
    {
        // Get reflection of this class and also it's constants as an array.
        $errClass = new ReflectionClass(__CLASS__);
        $consts = $errClass->getConstants();

        // Create a temporarly variable for const name for later use.
        $constname = "nil";

        // Iterate through the constants to search for the constant name we have as our first argument.
        foreach ($consts as $key => $val) {
            if ($val == $errorType) {
                $constname = $key;
                break;
            }
        }

        return $constname;
    }
}

class Response {
    /* Failure repsonse with error details. */
    public static function Fail($errorType, $error) 
    {
        // Generate name from error const integer value.
        $errName = Err::getName($errorType);


        // Return the failure data with error details.
        die (json_encode (array (
            "data" => $error,
            "errorcode" => $errorType,
            "errortype" => $errName,
            "status" => false
        )));

        return 0;
    }

    /* Success response, with data inside. */
    public static function Success($data) 
    {
        echo json_encode (array(
            "data" => $data,
            "status" => true
        ));

        return 1;
    }
}
?>