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

        $this->template_factory = new Flexi_TemplateFactory($GLOBALS['STUDIP_BASE_PATH']);
        $this->template_path = "public/".$this->getPluginPath()."/templates";
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
        $navigation = new AutoNavigation(_("Model-Tests"), PluginEngine::getURL($this, array(), 'tests'));
        $navigation->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/unit-test_white.png');
        Navigation::addItem('/FireBIEST/tests', $navigation);
        //Settings:
        if ($GLOBALS['perm']->have_perm("root")) {
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
        
        $template = $this->template_factory->open($this->template_path.'/check.php');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
        $template->set_attribute('js_files', $js_files);
        print $template->render();
        return;
    }

    public function tests_action() {
        global $STUDIP_BASE_PATH, $PHP_PATH, $PLUGINS_PATH;
        
        Navigation::getItem('/FireBIEST/tests')->setImage($GLOBALS['ABSOLUTE_URI_STUDIP'].$this->getPluginPath().'/images/unit-test_black.png');
        
        $db = DBManager::get();
        $path = Request::get("path");
        $params = array();
        if (Config::get()->getValue("FIREBIEST_TEST_WITH_DB") == "1") {
            $params[] = "db=1";
            if (Config::get()->getValue("FIREBIEST_KEEP_MOCK_TABLES") != "1") {
                $params[] = "clean=1";
            }
        }
        if ($path) {
            $params[] = "path=".rawurlencode($path);
        }
        $testergebnis = file_get_contents($this->getPluginURL()."/unit_test.php".($params ? "?".implode("&", $params) : ""));

        $plugins = $db->query(
            "SELECT * FROM plugins " .
        "")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($plugins as $key => $plugin) {
            if (!file_exists($PLUGINS_PATH."/".$plugin['pluginpath'])) {
                unset($plugins[$key]);
            }
            if ($plugin['pluginpath'] === $path) {
                $selected_plugin = $plugin['pluginname'];
            }
        }
        $selected_plugin || $selected_plugin = _("Stud.IP Kern");

        $template = $this->template_factory->open($this->template_path.'/tests.php');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
        $template->set_attribute('testergebnis', $testergebnis);
        $template->set_attribute('plugins', $plugins);
        $template->set_attribute('selected_path', $path);
        $template->set_attribute('selected_plugin', $selected_plugin);
        print $template->render();
    }
    
    public function settings_action() {
    	global $perm;
    	if (!$perm->have_perm("root")) {
    	    throw new AccessDeniedException(_("Sie haben keinen Zugriff auf diese Methode."));
    	}
        
        Navigation::getItem('/FireBIEST/settings')->setImage(Assets::image_path("icons/16/black/admin.png"));
    	
    	$configs = array();
    	foreach (Config::get()->getFields("global", "plugins", "FIREBIEST_") as $config_name) {
    	    $configs[$config_name] = Config::get()->getValue($config_name);
    	}
    	
    	$template = $this->template_factory->open($this->template_path.'/settings.php');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('configs', $configs);
        $template->set_attribute('infobox', array(
            'picture' => "infobox/administration.jpg",
            'content' => array(
                array(
                    'kategorie' => _("Information"),
                    'eintrag' => array(
                        array('icon' => "icons/16/grey/info", 'text' => _("Alle Klicks werden sofort gespeichert."))
                    )
                )
            )
        ));
        $template->set_attribute('save_url', PluginEngine::getURL($this, array(), "savesettings"));
        print $template->render();
        return;
    }
    
    public function savesettings_action() {
    	global $perm;
    	if ($perm->have_perm("root") && strpos(Request::get("config_name"), "FIREBIEST_") !== false) {
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


}

