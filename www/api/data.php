<?php

    $config = include("include/config.php");
    include_once("include/common.php");
    include_once("include/db.php");
    $errors = [];
    
    // this is a "get data" request
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod === 'GET') {
        
        // any data-points specified?
        if(!isset($_GET['key'])){            
            end_script(False, Null, $errors, 400);
        }

        // get input
        $keyInput = $_GET['key'];

        // is the input non-empty?
        if($keyInput){
            // get the data keys requested
            $keys = explode($config['data_name_separator'], $keyInput);
        }else{
            // fetch all
            $keys = null;
        }
        

        // connect to database
        $keystore = new KeystoreDB($config);
        $keystore->connect();

        // retrieve values
        $data = $keystore->get_values($keys);

        // return data and end script
        end_script(true, $data, null, 200);

    } else if ($requestMethod ==='OPTIONS') {
        // this is a CORS preflight request
        //header('Access-Control-Allow-Origin: https://chalmersrobotics.se');
        //header('Access-Control-Allow-Methods: GET, OPTIONS');
        
        header("Content-Length: 0");
        header("Content-Type: text/plain");
        exit();
    } else if ($requestMethod === 'POST') {

        // check if we have correct json-body first

        // read raw body data
        $postdata = file_get_contents("php://input");

        // convert from JSON
        $req = json_decode($postdata);

        // parser error?
        if(!$req || !is_array($req->data)){
            end_script(false, $null, 'Malformed JSON request', 400); // Bad request        
        }

        // iterate through each value to store
        $success = true;
        $values = [];
        foreach($req->data as $key){
            // any missing fields?
            if(!isset($key->name) || !isset($key->value) || !isset($key->ts_update) || !isset($key->token)){
                $errors[] = "Missing required fields";
                $success = false;
                continue;
            }

            // invalid auth token?
            if(!checkToken($key->name, $key->token, $config['key_secret'])){
                $errors[] = array(
                    'name' => $key->name,
                    'cause' => 'Unauthorized'
                );
                continue;
            }

            // put key in values to save
            $values[] = $key;
        }

        // only open database if we have anything to store
        if($values){

            // create database connection
            $keystore = new KeystoreDB($config);
            $keystore->connect();

            // store values
            $keystore->store_values($values);
            
        }

        end_script($success, null, $errors, 200); // Ok     

    }

?>