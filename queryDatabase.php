<?php
 /**
 * Copyright - 2020 - Maleesha Gimshan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
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
        $this->sql = "";
        $this->statement = false;        
        $this->prepared = false;
        return $this;
    }

    /**
     * prepare sql statement
     * @param String $sql
     * @return Object prepared statement - PDO
     */
    public function prepareStatement () {  
        if (!$this->prepared) {
            $this->statement =  $this->conn->prepare($this->sql);                        
            $this->prepared = true;            
        }        
        return $this->statement;
    }

    /**
     * check type
     * @param Any
     * @return 
     * throws Exceptions
     */
    public function checkTypes ($params) {

        $type = gettype($params);

        /**
         * extend further in the future
         */
        switch ($type) {
            case "integer":
                $pdoType = PDO::PARAM_INT;
                return $pdoType;
            break;
        }

    }

    /**
     * binds parameter value one by one
     * to prepared statement
     * @param Array values with respecting order of '?' marks
     */
    public function bindValue($params) {                
        for ($i = 0; $i < count($params); $i++) {            
            $pdoType = $this->checkTypes($params[$i]);                                    
            $this->statement->bindValue($i+1, $params[$i], $pdoType);            
        }        
    }

    /**
     * execute select statements
     * @param Object,Array $params
     * @return Array,Object,Boolean
     */
    public function selectData ($params) {

        /**
         * bind values to prepared statements
         * if explicitly stated in $params
         */
        if (isset($params->bindValue) && $params->bindValue) {                                                            
            $this->bindValue($params->data);                       
            $this->statement->execute();            
        }
        else {            
            $this->statement->execute((array)$params->data);
        }
        return $this->fetchData();       
    }


    /**
     * execute insert statements
     * and returns affected rows
     * @param Object,Array
     * @return Boolean
     */
    public function insertData ($params) {
        $result = $this->statement->execute();
    }

    
    public function fetchData () {        
        $this->results = $this->statement->fetchAll(PDO::FETCH_ASSOC);                            
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

        if($this->sql !== $params->sql) {            
            $this->sql = $params->sql;            
            $this->prepared = false;                      
        }
        $this->prepareStatement();       

        switch ($params->action) {
            case "select" :
                return $this->selectData($params);
            break;

            case "insert" :
            break;
        }     
    }

}
?>
