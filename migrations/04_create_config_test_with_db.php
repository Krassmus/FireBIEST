<?php


class CreateConfigTestWithDb extends Migration {
    
	function description() {
        return 'initializes the config-entries for this plugin';
    }

    public function up() {
	    $config = Config::get();
        if (!isset($config["FIREBIEST_TEST_WITH_DB"])) {
            Config::get()->create("FIREBIEST_TEST_WITH_DB", array('section' => "FireBIEST", 'is_default' => 0, 'value' => 0, 'type' => "boolean", 'range' => "global", 'description' => "should we also use unit-tests that can handle a mock-DB?", 'comment' => ""));
        }
	}
	
	public function down() {
		Config::get()->delete("FIREBIEST_TEST_WITH_DB");
    }
}