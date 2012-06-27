<p class="info">
    <?= _("Die folgenden Einstellungen gelten global für alle Nutzer (inkl. Root, Admin).") ?>
</p>
<form>
<table style="margin: 7px; margin-left: auto; margin-right: auto;">
    <tr>
        <td><label for="FIREBIEST_TEST_IMAGES"><?= _("Teste nach korrekten Bilder-Link-Tags") ?></label></td>
        <td><input type="checkbox" 
                   id="FIREBIEST_TEST_IMAGES" 
                   name="FIREBIEST_TEST_IMAGES" <?= $configs['FIREBIEST_TEST_IMAGES'] ? " checked" : "" ?> 
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
    <tr>
        <td><label for="FIREBIEST_TEST_BLOCKQUOTES"><?= _("Warne bei falschen Blockquotes") ?></label></td>
        <td><input type="checkbox" 
                   id="FIREBIEST_TEST_BLOCKQUOTES" 
                   name="FIREBIEST_TEST_BLOCKQUOTES" <?= $configs['FIREBIEST_TEST_BLOCKQUOTES'] ? " checked" : "" ?> 
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
    <tr>
        <td><label for="FIREBIEST_TEST_LABELS"><?= _("Teste Formular-Labels") ?></label></td>
        <td><input type="checkbox" id="FIREBIEST_TEST_LABELS"
                   name="FIREBIEST_TEST_LABELS" <?= $configs['FIREBIEST_TEST_LABELS'] ? " checked" : "" ?>
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
    <tr>
        <td><label for="FIREBIEST_TEST_HTML"><?= _("Teste HTML-Syntax") ?></label></td>
        <td><input type="checkbox" id="FIREBIEST_TEST_HTML"
                   name="FIREBIEST_TEST_HTML" <?= $configs['FIREBIEST_TEST_HTML'] ? " checked" : "" ?>
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
    <tr>
        <td><label for="FIREBIEST_TEST_WITH_DB"><?= _("Unit-Tests mit einer Mock-Datenbank? (dauert etwas länger, dafür sind Zugriff auf DBManager in den Tests begrenzt möglich) ") ?></label></td>
        <td><input type="checkbox" id="FIREBIEST_TEST_WITH_DB"
                   name="FIREBIEST_TEST_WITH_DB" <?= $configs['FIREBIEST_TEST_WITH_DB'] ? " checked" : "" ?>
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
    <tr>
        <td><label for="FIREBIEST_KEEP_MOCK_TABLES"><?= _("Mock-Tabellen stehen lassen nach Unit-Tests") ?></label></td>
        <td><input type="checkbox" id="FIREBIEST_KEEP_MOCK_TABLES"
                   name="FIREBIEST_KEEP_MOCK_TABLES" <?= $configs['FIREBIEST_KEEP_MOCK_TABLES'] ? " checked" : "" ?>
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
	<tr>
        <td><label for="FIREBIEST_CLEAN_CACHE"><?= _("Cache bei jedem Seitenaufruf leeren") ?></label></td>
        <td><input type="checkbox" id="FIREBIEST_CLEAN_CACHE"
                   name="FIREBIEST_CLEAN_CACHE" <?= $configs['FIREBIEST_CLEAN_CACHE'] ? " checked" : "" ?>
                   onChange="jQuery.ajax({ url: '<?= $save_url ?>', data: { checked: this.checked ? 1 : 0, config_name: this.id } });"></td>
    </tr>
</table>
</form>