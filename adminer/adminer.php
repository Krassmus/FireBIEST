<?php



function adminer_object() {
    // required to run any plugin
    include_once dirname(__file__)."/plugin.php";
    
    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        // specify enabled plugins here
        new AdminerFrames,
        new AdminerDumpZip,
        new AdminerAutologin
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include dirname(__file__)."/adminer-3.3.3-mysql.php";
?>