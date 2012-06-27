<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */
?>
<iframe 
    id="adminer_frame" 
    name="adminer_frame" 
    src="<?= URLHelper::getURL($url, array('username' => $GLOBALS['DB_STUDIP_USER'], 'db' => $GLOBALS['DB_STUDIP_DATABASE'])) ?>" 
    width="100%" 
    height="620" 
    frameborder="0">
    <p>Your browser does not support iframes.</p>
</iframe>

<script>
jQuery(function () {
    window.setInterval(function () {
        jQuery('#adminer_frame').attr("height", jQuery(window.document.getElementById('adminer_frame').contentWindow.document).height() + "px");
        jQuery('#adminer_frame').attr("width", jQuery(window.document.getElementById('adminer_frame').contentWindow.document).width() + "px");
    }, 500);
});
</script>