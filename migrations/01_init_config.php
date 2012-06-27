<?php


class InitConfig extends Migration {
    
	function description() {
        return 'initializes the config-entries for this plugin';
    }

    public function up() {
	    $config = Config::get();
        if (!isset($config["FIREBIEST_TEST_IMAGES"])) {
            $config->create("FIREBIEST_TEST_IMAGES", array('section' => "plugins", 'is_default' => 1, 'value' => 1, 'type' => "boolean", 'range' => "global", 'description' => "tests all image-links on the site if they have a nice title-attribute", 'comment' => ""));
        }
        if (!isset($config["FIREBIEST_TEST_BLOCKQUOTES"])) {
            $config->create("FIREBIEST_TEST_BLOCKQUOTES", array('section' => "plugins", 'is_default' => 1, 'value' => 1, 'type' => "boolean", 'range' => "global", 'description' => "tests if there are blockquotes on the site which are only for indenting-purpose (this is not good!)", 'comment' => ""));
        }
        if (!isset($config["FIREBIEST_TEST_LABELS"])) {
            $config->create("FIREBIEST_TEST_LABELS", array('section' => "plugins", 'is_default' => 0, 'value' => 0, 'type' => "boolean", 'range' => "global", 'description' => "tests all forms in Stud.IP if they have a correct label", 'comment' => ""));
        }
	}
	
	public function down() {
		Config::get()->delete("FIREBIEST_TEST_IMAGES");
        Config::get()->delete("FIREBIEST_TEST_BLOCKQUOTES");
        Config::get()->delete("FIREBIEST_TEST_LABELS");
	}
}