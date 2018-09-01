<?php
    // Script for uploading and retrieving image files (for example from webcams) 
    // Inspiration: https://cloudinary.com/blog/file_upload_with_php

    // include the configuration file
    $config = include("include/config.php");

    // and the common functions
    include_once("include/common.php");

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

        // initialize some variables
        $fileTmpName = null;

        // check if a file was provided as a file upload
        if(isset($_FILES['image'])){
        
            // make sure there were no errors during upload
            if($_FILES['image']['error']){
                $errors[] = "There was an error during file upload";

                end_script(false, -1, $errors, 400); // Bad Request
            }

            // set file temporary name to the provided one in upload
            $fileTmpName = $_FILES['image']['tmp_name'];
            
        } else {
            // since file upload was not used, assume the image is sent as raw HTTP data
            $postdata = file_get_contents("php://input");

            //echo json_encode($_POST);

            // generate temporary place to store image
            $fileTmpName = tempnam(sys_get_temp_dir(), 'img');
            
            // store to temporary file
            file_put_contents($fileTmpName, $postdata);
           
        } 

        // make sure we actually got something
        if ($fileTmpName == null){
            $errors[] = "Please attach an image file";
            end_script(false, -1, $errors, 400); // Bad request
        }

        // check file size
        if (filesize($fileTmpName) > 2000000) {
            $errors[] = "The file has to be less than or equal to 2MB in size";
        }

        // check to make sure the file is actually an image (the @ suppresses any errors caused by the file not being an image)
        if(@getimagesize($fileTmpName) == false){
            $errors[] = "Please upload a valid image file";
        }

        // only save file if there were no errors
        if(empty($errors)){
            // create the upload path
            $uploadPath = getFilePath($resourceName, $config['salt']);

            // move the uploaded file, this is different depending on whether the image was uploaded using a POST file upload or raw HTTP data
            if(isset($_FILES['image'])){
                $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
            }else{
                $didUpload = rename($fileTmpName, $uploadPath);
            }

            // check if file was uploaded or not
            if($didUpload)
                end_script(true, -1, $errors, 200);
            else   { 
                $errors[] = "Something went wrong while completing the upload";
                end_script(false, -1, $errors, 500); // 500 - Internal Server Error
            }
        } 
        
        // end with errors
        end_script(false, -1, $errors, 200);
        
    }
?>