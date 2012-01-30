<?php

/**
 * Ein PDO-Wrapper, um die Datenbank etwas näher zu beleuchten.
 */

class MockPDO extends StudipPDO {

    protected $table_prefix = "mock_db_";
    protected $lastQuery = "";

    public function query($sql) {
        $sql = $this->alterSQL($sql);
        return parent::query($sql);
    }

    public function exec($sql) {
        $sql = $this->alterSQL($sql);
        return parent::exec($sql);
    }
    
    public function prepare($sql, $driver_options = array()) {
        $sql = $this->alterSQL($sql);
        $statement = parent::prepare($sql, $driver_options);
        return $statement;
    }

    protected function alterSQL($sql) {
        $sql = preg_replace("/(TABLE\sIF\sNOT\sEXISTS|TABLE\sIF\sEXISTS|TABLE)[\s`]+([^\s|`]+)[\s`]+/", "$1 `".$this->table_prefix.'$2'."`", $sql);
        $sql = preg_replace("/FROM[\s`]+([^\s|`]+)[\s`]+/", "FROM `".$this->table_prefix.'$1'."`", $sql);
        $sql = preg_replace("/JOIN[\s`]+([^\s`]+)[\s`]+/", "JOIN `".$this->table_prefix.'$1'."`", $sql);
        $sql = preg_replace("/INTO[\s`]+([^\s|`]+)[\s`]+/", "INTO `".$this->table_prefix.'$1'."`", $sql);
        $sql = preg_replace("/UPDATE[\s`]+([^\s|`]+)[\s`]+/", "UPDATE `".$this->table_prefix.'$1'."`", $sql);
        //MockDB braucht Projektionen in den Selects, da table_name.field nicht verändert werden kann.
        //Also entweder immer Projektionen oder niemals. Daher lieber immer.
        //$sql = preg_replace("/([\s`]+)([[^\s]|`]+[^`]*)\.([^\s]+)\s/", "$1".$this->table_prefix.'$1`.`$2` ', $sql);
        $this->lastQuery = $sql;
        return $sql;
    }

    public function getLastQuery() {
        return $this->lastQuery;
    }

    public function dropMockTables() {
        $mock_tables = $this->query("SHOW TABLES LIKE '".$this->table_prefix."%'")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($mock_tables as $table) {
            parent::exec("DROP TABLE `".addslashes($table)."` ");
        }
    }

}