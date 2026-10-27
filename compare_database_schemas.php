<?php
//include('dbconfig.php');
    compareDatabaseSchemas($_GET);
    function compareDatabaseSchemas($get){
        $dbName1 = $get['db1']; // e.g., 'my_database_1'
        $dbName2 = $get['db2']; // e.g., 'my_database_2'
        // return $get;
        $db1 = $this->connectToDatabaseByName($dbName1);
        $db2 = $this->connectToDatabaseByName($dbName2);
        function getTablesDB($db) {
            $tables = [];
            $result = $db->query("SHOW TABLES");
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            return $tables;
        }
        // Function to get fields for a table
        function getFieldsDB($db, $table) {
            $fields = [];
            $result = $db->query("SHOW COLUMNS FROM `$table`");
            while ($row = $result->fetch_assoc()) {
                $fields[$row['Field']] = $row['Type'];
            }
            return $fields;
        }
        // Compare tables
        $tables1 = getTablesDB($db1);
        $tables2 = getTablesDB($db2);
        
        $allTables = array_unique(array_merge($tables1, $tables2));
        
        foreach ($allTables as $table) {
            echo "Checking table: $table\n";
        
            $existsInDb1 = in_array($table, $tables1);
            $existsInDb2 = in_array($table, $tables2);
        
            if (!$existsInDb1) {
                echo " - Table missing in DB1: $table\n";
            }
            if (!$existsInDb2) {
                echo " - Table missing in DB2: $table\n";
            }
        
            if ($existsInDb1 && $existsInDb2) {
                $fields1 = getFieldsDB($db1, $table);
                $fields2 = getFieldsDB($db2, $table);
        
                $allFields = array_unique(array_merge(array_keys($fields1), array_keys($fields2)));
        
                foreach ($allFields as $field) {
                    $in1 = array_key_exists($field, $fields1);
                    $in2 = array_key_exists($field, $fields2);
        
                    if (!$in1) {
                        echo "   - Field '$field' missing in DB1.$table\n";
                    }
                    if (!$in2) {
                        echo "   - Field '$field' missing in DB2.$table\n";
                    }
                    if ($in1 && $in2 && $fields1[$field] !== $fields2[$field]) {
                        echo "   - Field type mismatch in '$field' of $table: DB1={$fields1[$field]} DB2={$fields2[$field]}\n";
                    }
                }
            }
        
            echo "\n";
        }
    }

    function connectToDatabaseByName($dbName) {
    // Replace with your actual host/user/pass
        $host = HOSTNAME;
        $user = USERNAME;
        $pass = PASSWORD;

        $mysqli = new mysqli($host, $user, $pass, $dbName);
        if ($mysqli->connect_error) {
            die("Connection failed to DB [$dbName]: " . $mysqli->connect_error);
        }
        return $mysqli;
    }

?>