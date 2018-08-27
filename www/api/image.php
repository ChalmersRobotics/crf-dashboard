<?php
    // Scpipt for uploading and retrieving image files (for example from webcams) 
    // Inspiration: https://cloudinary.com/blog/file_upload_with_php

    // some global parameters

    // include the configuration file
    $config = include("include/config.php");
    include_once("include/common.php");

    //_DSC5595 - 5B686526F8890F38E47D22A212544CF0272BF81FED1E84A3E436B0181BD8A624
    //_DSC5595.jpg - 6A3A1DED227689D2A1341B8ECFC14AB4ACAFA5A6E98D382C50DB78826BDD29EB

    //cat.jpg - DB73FD963840B2BDE4D1B236524C854B4135EB02B5DD4EFC5D4E29B4090E7C00
    //cat - 1417C5FF6495966A86780847C7B7341355BEF453F39164C7F165BFE69BA175B2


    

    function getFilePath($name, $salt){
        // get the current directory
        $currentDir = getcwd();

        // the name of the upload directory
        $uploadDirectoryName = "/uploaded_images/";

        // generate a hash based on the resource name and the provided salt
        $hashedFileName = hash("sha256", $name . $salt);

        // concatenate and return the filepath
        return $currentDir . $uploadDirectoryName .  $hashedFileName;
    }

    // check request type
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') { // GET request

        // check if client specified what file to get
        if(isset($_GET['name'])){

            // get the name of the resource
            $resourceName = $_GET['name'];      
                                  
            // get path to file
            $filePath = getFilePath($resourceName, $config['salt']);

            // check if the file exists
            if(file_exists($filePath)){
                // yes, read the image properties and return the correct mime-type ( see https://stackoverflow.com/questions/4286677/show-image-using-file-get-contents )
                $imginfo = getimagesize($filePath);
                header("Content-type: {$imginfo['mime']}");

                // read (and echo) the file contents
                readfile($filePath);

                // end here
                exit();
            }
        }
        // resource not found, indicate by sending a 404 Not Found HTTP code
        http_response_code(404);

    }else if($method === 'POST'){ // POST upload request

        // stores all errors that may occur
        $errors = [];

        // check if any resource name was specified
        if(!isset($_GET['name'])){
            $errors[] = "No resource name specified";

            // end script
            end_script(false, -1, $errors, 400); // Bad request

        }
        // get the resource name
        $resourceName = $_GET['name'];
        
        // Authorization checks, exit early if not authorized
        if(!isAuthorized($resourceName, $config['key_secret'])){
            $errors[] = "Unauthorized";
            end_script(false, -1, $errors, 401); // Unauthorized
        }

        // check if a file was acually provided?
        if(!isset($_FILES['image'])){
            
            $errors[] = "No image file specified in POST";

            // end script here
            end_script(false, -1, $errors, 400); // Bad request
            
        }      
        
        // make sure there were no errors during upload
        if($_FILES['image']['error']){
            $errors[] = "There was an error during file upload";

            end_script(false, -1, $errors, 400); // Bad Request
        }

        // the allowed image file extensions
        $fileExtensions = ['jpeg', 'jpg', 'png'];

        // information about the file being uploaded
        $file = $_FILES['image'];       
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];
        $fileType = $file['type'];
        // $fileExtension = strtolower(end(explode('.', $fileName)));
        

        $fileExtension = pathinfo($fileName)['extension'];

        // create the upload path
        $uploadPath = getFilePath($resourceName, $config['salt']);
   
        // check file extension
        if(!in_array($fileExtension, $fileExtensions)){
            $errors[] = "This file extension is not allowed, please upload a JPEG or PNG file";
        }

        // check file size
        if ($fileSize > 2000000) {
            $errors[] = "The file has to be less than or equal to 2MB in size";
        }

        // check to make sure the file is actually an image (the @ suppresses any errors caused by the file not being an image)
        if(@getimagesize($fileTmpName) == false){
            $errors[] = "Please upload a valid image file";
        }

        // only save file if there were no errors
        if(empty($errors)){
            // move the uploaded file
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

            // check if file was uploaded or not
            if($didUpload)
                end_script(true, -1, $errors, 200);
            else   { 
                $errors[] = "Something went wrong while completing the upload";
                end_script(false, -1, $errors, 500); // 500 - Internal Server Error
            }
        } 
        
        end_script(false, -1, $errors, 200);
        
    }
?>