<?php
    

    class KeystoreDB {
        private $config = null;
        function __construct($config){
            $this->config = $config;
            $this->db = null;            
        }

        function connect(){
            // connect to the database
            try{
                $this->db = new PDO(sprintf("mysql:host=%s;dbname=%s", $this->config['db_host'], $this->config['db_database'] ), $this->config['db_user'], $this->config['db_password']);
            } catch (PDOException $e){
                echo("Error! " . $e->getMessage());
                exit();
            }
            return $this->db;
        }

        // returns the values associated with the provided keys 
        function get_values($keys){

            // select columns and convert datetime to unix time
            $query = 'SELECT `name`, `value`, UNIX_TIMESTAMP(`timestamp_update`), UNIX_TIMESTAMP(`timestamp_upload`) FROM `data_keystore`';

            // if keys specified
            if($keys){
                // prepare statement with '?' as placeholders
                $qString =  str_repeat('?,', count($keys) - 1) . '?';
                $stmt = $this->db->prepare($query . " WHERE `name` IN ($qString)");

                // execute and bind ?:s to the specified keys
                $stmt->execute($keys);

            }else{
                // just execute the query (fetch all)
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }

            if(!$stmt){
                print("Error!");
                print_r($this->db->errorInfo());
            }

            

            // iterate over all returned rows and create the result
            $result = []; 
            foreach($stmt->fetchAll() as $row){
                //print(json_encode($row)) . '<br>';

                /*
                $result[] = array(
                    'name' => $row['name'],
                    'value' => $row['value'],
                    'timestamp_update' => $row['timestamp_update'],
                );  
                */
                $result[$row['name']] = [                    
                    'value' => $row['value'],
                    'timestamp_update' => $row['UNIX_TIMESTAMP(`timestamp_update`)'],   
                    'timestamp_upload' => $row['UNIX_TIMESTAMP(`timestamp_upload`)'],   
                ];           
            }       

            return $result;
        }

        function store_values($keys){
            //$query = "UPDATE data_keystore SET `value`=:value, `timestamp_update`=FROM_UNIXTIME(:timestamp), `timestamp_upload`=CURRENT_TIMESTAMP WHERE `name`=:name";

            // the query tries to insert a new row in the table, but the name is required to be unique and therefore the values are updated insted!
            // this allows for new variables to be created "on demand"
            $query = "INSERT INTO data_keystore (`name`, `value`,`timestamp_update`, `timestamp_upload`) VALUES (:name, :value, FROM_UNIXTIME(:timestamp), CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`),`timestamp_update`=VALUES(`timestamp_update`),`timestamp_upload`=VALUES(`timestamp_upload`)";
            
            
            // do one query for each insert
            foreach($keys as $key){
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':name', $key->name);
                $stmt->bindValue(':value', $key->value);
                $stmt->bindValue(':timestamp', $key->timestamp);

                $stmt->execute();
                /*
                if(!$stmt->execute()){
                    // error
                    print("Error!");
                    print_r($stmt->errorInfo());
                }
                */
            }
        }


        function __destruct(){
            // close connection
            $this->db = null;
        }
    }
    
    
    


    
    

?>