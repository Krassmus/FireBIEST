<?php


class CreateConfigKeepMockTables extends Migration {
    
	function description() {
        return 'initializes the config-entries for this plugin';
    }

    public function up() {
	    $config = Config::get();
        if (!isset($config["FIREBIEST_KEEP_MOCK_TABLES"])) {
            Config::get()->create("FIREBIEST_KEEP_MOCK_TABLES", array('section' => "FireBIEST", 'is_default' => 0, 'value' => 0, 'type' => "boolean", 'range' => "global", 'description' => "should the mock-tables in the database be kept after a unit-test?", 'comment' => ""));
        }
	}
	
	public function down() {
		Config::get()->delete("FIREBIEST_KEEP_MOCK_TABLES");
    }
}