<?php


class CreateConfigTestHtml extends Migration {
    
	function description() {
        return 'initializes the config-entries for this plugin';
    }

    public function up() {
	    $config = Config::get();
        if (!isset($config["FIREBIEST_TEST_HTML"])) {
            Config::get()->create("FIREBIEST_TEST_HTML", array('section' => "FireBIEST", 'is_default' => 0, 'value' => 0, 'type' => "boolean", 'range' => "global", 'description' => "tests syntax of the html"));
        }
	}
	
	public function down() {
		Config::get()->delete("FIREBIEST_TEST_HTML");
    }
}