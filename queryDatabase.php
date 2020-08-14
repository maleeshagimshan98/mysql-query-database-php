<?php
/**
 * Author - Maleesha__Gimshan
 * 2020
*/

/**
 * class for querying database with pdo objects
 */
class queryDb {

    /**
     * construct 
     * @param Object PDO Object
     */
    public function __construct ($conn) {
        $this->conn = $conn;
        $this->statement = false;
        return $this;
    }

    /**
     * prepare sql statement
     * @param String $sql
     * @return Object prepared statement - PDO
     */
    public function prepareStatement ($sql) {        
        $this->statement =  $this->conn->prepare($sql);        
        return $this->statement;
    }

    /**
     * binds parameter value one by one
     * to prepared statement
     * @param Array values with respecting order of '?' marks
     */
    public function bindValue($params) {
        for ($i = 0; $i = count($params); $i++) {
            if ($params[$i]['type'] !== gettype($params[$i]['data'])) {
                throw new Exception("Invalid_Type");
            }
            /**
             * extend further in the future
             */
            switch ($params[$i]['type']) {
                case "integer":
                    $pdoType = PDO::PARAM_INT;
                    break;
            }
            $this->statement->bindValue($i, $params[$i]['data'], $pdoType);
        }
    }

    /**
     * execute select statements
     * @param Array $params
     * @return Array,Object,Boolean
     */
    public function selectData ($params) { 
        echo json_encode($this->statement->execute((array)$params));         
        $this->statement->execute($params);
        return $this->fetchData();       
    }

    public function insertData ($params) {
        $result = $this->statement->execute();
    }

    public function updateData ($params) {        
        $this->statement->execute();
    }

    public function fetchData () {
        echo json_encode($this->statement);
        $this->results = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($this->results);        
        return $this->results;        
    }


    /**
     * execute sql statement
     * and returns data
     * @param Object,Array
     * @return Object,Array,Booelan
     * throws Exceptions
     */
    public function executeStatement($params)  {   
                    
        /**
         * prepare sql statement if not prepared yet
         */
        if (!$this->statement && isset($params->sql)) {            
            $this->prepareStatement($params->sql);
        }

        /**
         * bind values to prepared statements
         * if stated in input
         */
        if (isset($params->bindValue) && $params->bindValue) {            
            $this->bindValue($params->params);
        }
        switch ($params->action) {
            case "select":                
                return $this->selectData($params->data);                
            break;

            case "insert":
                $this->insertData($params->data);
                break;
        }
    }

}
?>