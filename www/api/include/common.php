<?php
    // include the configuration settings
    //include_once("config.php");

    // function to check if user is allowed to edit the provided resource name by comparing the provided HTTP token with the expected one
    function isAuthorized($resourceName, $key_secret){        
        if(isset($_SERVER['HTTP_TOKEN'])){
            // get the provided token
            $userToken = $_SERVER['HTTP_TOKEN'];

            // check that the token is correct
            return checkToken($resourceName, $userToken, $key_secret);
        }
        // no match, or no token provided
        return false;    
    }

    // function to check if user is allowed to edit the provided resource name by comparing the provided HTTP token with the expected one
    function checkToken($resourceName, $userToken, $key_secret){        
       
        // generate the wanted token (the "false" here means that we want a hexadecimal string back)
        $wantedToken = hash("sha256", $key_secret . $resourceName , false);

        // check hashes and return value
        return strtolower($userToken) === strtolower($wantedToken);
            
    }

    // function for ending the script during processing and return status, data and error messages to the client
    function end_script($success, $data,  $errors, $http_code){
        // we are returning JSON
        header('Content-type: application/json');

        // object holding the result of the action
        $result = [];
        $result['success'] = $success;

        if($data !== -1){
            $result['data'] = $data;
        }

        // if there were errors, return them too
        if(!empty($errors)){
            $result['error'] = $errors;
        }
        // print result
        echo(json_encode($result));
        
        // set response code
        http_response_code($http_code); 

        // end script
        exit();
    }


?>