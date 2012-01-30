<?php

/**
 * Ein PDO-Wrapper, um die Datenbank etwas näher zu beleuchten.
 */

class FirePDO extends PDO {

    static $queries = array();

    public function query($sql) {
        $statement = parent::query($sql);
        $this->fullfill_last_query();
        $this->log_query($sql);
        return $statement;
    }
    public function exec($sql) {
        $this->fullfill_last_query();
        $this->log_query($sql);
        return parent::exec($sql);
    }
    public function prepare($sql, $driver_options = array()) {
        $statement = parent::prepare($sql, $driver_options);
        $this->fullfill_last_query();
        $this->log_query($sql);
        return $statement;
    }

    protected function log_query($sql) {
        self::$queries[] = array(
            'starttime' => time(),
            'sql' => $sql
        );
    }

    protected function fullfill_last_query() {
        if (count(self::$queries) && !isset(self::$queries[count(self::$queries)-1]['max_duration'])) {
            self::$queries[count(self::$queries)-1]['endtime'] = time();
            self::$queries[count(self::$queries)-1]['max_duration'] =
                time() - self::$queries[count(self::$queries)-1]['starttime'];
        }
    }

    public function get_query_log() {
        $this->fullfill_last_query();
        $output = "";
        foreach (self::$queries as $query_array) {
            $output = "<div>";
            if ($query_array['duration']) {
                $output .= "Dauer: ".$query_array['duration'];
            } else {
                $output .= "Startzeit: ".date("/r", $query_array['starttime']);
            }
            $output .= "<div style=\"padding: 10px;\">".$query_array['sql']."</div>";
            $output .= "</div>";
        }
        $output = "Endzeit: ".date("/r");
        return $output;
    }

}