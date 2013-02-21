<?php

/**
 * FireBIEST.class.php - a development-plugin to check your code
 *
 * - You can check mainly your javascript-code for syntax and even coding style.
 * It will be checked via JSLint and you can check any javascript-file which is 
 * included in studip or its plugins. Just select the file in the select-box.
 * - Also you can check some parts of your HTML-code for example you can check, 
 * if there are any blockquote tags, which are useless. Most of these things will
 * be checked for accessability-purpose. If there is any problem indicated you will 
 * be noticed by a dialog-box or by offensive highlighting.
 * 
 * Don't use this plugin in a working studip - only for development distributions!
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Rasmus Fuhse <fuhse@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

require_once dirname(__file__)."/classes/FirePDO.class.php";

class FireBIEST extends StudIPPlugin implements SystemPlugin {
    
    ////////////////////////////////////////////////////////////////////////////
    //                            public methods                              //
    ////////////////////////////////////////////////////////////////////////////
    
    public function __construct() {
        global $perm;
        parent::__construct();
		
        $navigation = new AutoNavigation($this->getDisplayname(), PluginEngine::getURL($this, array(), 'jslint'));
        
        PageLayout::addHeadElement("script",
            array("src" => $GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/js/htmltest.js'), "");
        PageLayout::addHeadElement("script",
            array("src" => $GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/js/jslint.js'), "");
        

        //test the html: 
        $configs = array();
        foreach (Config::get()->getFields("global", "plugins", "FIREBIEST_") as $config_name) {
            $configs[$config_name] = Config::get()->getValue($config_name);
        }
        $options = "";
        if ($configs["FIREBIEST_TEST_BLOCKQUOTES"]) {
            $options .= "STUDIP.htmltest.testBlockquotes(); ";
        }
        if ($configs["FIREBIEST_TEST_IMAGES"]) {
            $options .= "STUDIP.htmltest.testImages(); ";
        }
        if ($configs["FIREBIEST_TEST_LABELS"]) {
            $options .= "STUDIP.htmltest.testLabels(); ";
        }
        if ($configs["FIREBIEST_TEST_HTML"]) {
            $options .= "STUDIP.htmltest.HTMLLint(); ";
        }
		if ($configs["FIREBIEST_CLEAN_CACHE"]) {
			SimpleORMap::expireTableScheme();
			$GLOBALS['CACHING_ENABLE'] = false;
		}
        if ($options) {
            PageLayout::addHeadElement("script", array(), 'jQuery(function () { '.$options.' });');
        }
        
        Navigation::addItem('/start/jslintchecker', $navigation);
        Navigation::addItem('/FireBIEST', $navigation);
        
        //JSLint:
        $navigation = new AutoNavigation(_("Check your JS-Style"), PluginEngine::getURL($this, array(), 'jslint'));
        $navigation->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/code_white.png');
        Navigation::addItem('/FireBIEST/jslint', $navigation);
        //Unit-Tests:
        $navigation = new AutoNavigation(_("Unit-Tests"), PluginEngine::getURL($this, array(), 'tests'));
        $navigation->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/unit-test_white.png');
        Navigation::addItem('/FireBIEST/tests', $navigation);
        
        if ($GLOBALS['perm']->have_perm("root")) {
            //Datenbank
            $navigation = new AutoNavigation(_("Datenbank"), PluginEngine::getURL($this, array(), 'database'));
            $navigation->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/model_white.png');
            Navigation::addItem('/FireBIEST/database', $navigation);
            //Settings:
            $navigation = new AutoNavigation(_("Einstellungen"), PluginEngine::getURL($this, array(), 'settings'));
            $navigation->setImage(Assets::image_path("icons/16/white/admin.png"));
            Navigation::addItem('/FireBIEST/settings', $navigation);
        }
        
        if (Navigation::getItem("/FireBIEST")->isActive()) {
            PageLayout::setTitle($this->getDisplayname());
        }
        //PageLayout::addBodyElements(DBManager::get()->get_query_log());
    }
    
    public function jslint_action() {
    	//PageLayout::addHeadElement("script",
        //    array("src" => $GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/js/jslint.js'), "");
        
        Navigation::getItem('/FireBIEST/jslint')->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/code_black.png');
        
        $js_files = $this->getJSFiles("assets/javascripts/");
        $js_files = array_merge($js_files, $this->getJSFiles("plugins_packages/"));
        
        $template = $this->getTemplate('check.php');
        $template->set_attribute('js_files', $js_files);
        echo $template->render();
        return;
    }

    public function tests_action() {
        Navigation::getItem('/FireBIEST/tests')->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/unit-test_black.png');
        
        $db = DBManager::get();
        $path = Request::get("path");
        $plugins = $db->query(
            "SELECT * FROM plugins " .
        "")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($plugins as $key => $plugin) {
            if (!file_exists($GLOBALS['PLUGINS_PATH']."/".$plugin['pluginpath']."/tests")) {
                unset($plugins[$key]);
            }
        }
            
        $template = $this->getTemplate('tests_default.php', "with_infobox");
        
        $template->set_attribute('plugins', $plugins);
        $template->set_attribute('plugin', $this);
        print $template->render();
    }

    public function ajax_tests_action() {
        $path = Request::get("plugin");
        if (!Request::get("plugin")) {
            throw new Exception("Kein Plugin angegeben.");
        }

        $params = array();
        if (Config::get()->getValue("FIREBIEST_TEST_WITH_DB") == "1") {
            $params[] = "db=1";
            if (Config::get()->getValue("FIREBIEST_KEEP_MOCK_TABLES") != "1") {
                $params[] = "clean=1";
            }
        }
        $params[] = "path=".rawurlencode(Request::get("plugin"));
        
        $url = $this->getPluginURL()."/unit_test.php".($params ? "?".implode("&", $params) : "");

        $testergebnis = file_get_contents($url);

        print $testergebnis;
    }
    
    public function database_action() {
        if (!$GLOBALS['perm']->have_perm("root")) {
    	    throw new AccessDeniedException(_("Sie haben keinen Zugriff auf diese Methode."));
    	}
        Navigation::getItem('/FireBIEST/database')->setImage(Assets::image_path($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/model_black.png'));
    	
        $config_file = fopen(dirname(__file__)."/adminer/config.php", "w");
        fwrite($config_file, "<?php ");
        fwrite($config_file, '$STUDIP_DB_SERVER = '."'".$GLOBALS['DB_STUDIP_HOST']."'; ");
        fwrite($config_file, '$STUDIP_DB_USER = '."'".$GLOBALS['DB_STUDIP_USER']."'; ");
        fwrite($config_file, '$STUDIP_DB_NAME = '."'".$GLOBALS['DB_STUDIP_DATABASE']."'; ");
        fwrite($config_file, '$STUDIP_DB_PASSWORD = '."'".$GLOBALS['DB_STUDIP_PASSWORD']."'; ");
        fclose($config_file);
        
        $template = $this->getTemplate('adminer.php');
        $template->set_attribute('url', $this->getPluginURL()."/adminer/adminer.php");
        echo $template->render();
    }
    
    public function settings_action() {
    	if (!$GLOBALS['perm']->have_perm("root")) {
    	    throw new AccessDeniedException(_("Sie haben keinen Zugriff auf diese Methode."));
    	}
        
        Navigation::getItem('/FireBIEST/settings')->setImage(Assets::image_path("icons/16/black/admin.png"));
    	
    	$configs = array();
    	foreach (Config::get()->getFields("global", "plugins", "FIREBIEST_") as $config_name) {
    	    $configs[$config_name] = Config::get()->getValue($config_name);
    	}
		
    	$template = $this->getTemplate('settings.php', 'with_infobox');
        $template->set_attribute('configs', $configs);
        $template->set_attribute('plugin', $this);
        $template->set_attribute('save_url', PluginEngine::getURL($this, array(), "savesettings"));
        print $template->render();
        return;
    }
    
    public function savesettings_action() {
    	if ($GLOBALS['perm']->have_perm("root") && strpos(Request::get("config_name"), "FIREBIEST_") !== false) {
            Config::get()->store(Request::get("config_name"), array('value' => Request::get("checked")));
    	}
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //                           private methods                              //
    ////////////////////////////////////////////////////////////////////////////
    
    private function getDisplayname() {
        return _("FireBIEST");
    }
    
    private function getJSFiles($root_dir) {
        $return_array = array();
        if (!$handle = opendir($root_dir)) {
            return $return_array;
        }
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, ".js") !== false 
                  && strpos($root_dir.$file, ".svn") === false) {
                $return_array[$root_dir.$file] = $file;
            }
            if (is_dir($root_dir.$file) && $file != "." && $file != "..") {
                $return_array = array_merge($return_array, $this->getJSFiles($root_dir.$file."/"));
            }
        }
        return $return_array;
    }
    
    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
            $template->set_layout($GLOBALS['template_factory']->open($layout === "without_infobox" ? 'layouts/base_without_infobox' : 'layouts/base'));
        }
        return $template;
    }


}

