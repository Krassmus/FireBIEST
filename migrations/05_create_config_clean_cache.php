<?php


class CreateConfigCleanCache extends Migration {
    
	function description() {
        return 'initializes the config-entries for this plugin';
    }

    public function up() {
	    $config = Config::get();
        if (!isset($config["FIREBIEST_CLEAN_CACHE"])) {
            Config::get()->create("FIREBIEST_CLEAN_CACHE", array('section' => "plugins", 'is_default' => 1, 'value' => 1, 'type' => "boolean", 'range' => "global", 'description' => "should the cache be cleaned up with every pageview?", 'comment' => ""));
        }
	}
	
	public function down() {
		Config::get()->delete("FIREBIEST_CLEAN_CACHE");
    }
}