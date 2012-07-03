<h2><?= _("Testergebnisse von ").$selected_plugin ?></h2>

<label>
    <?= _("Komponente testen:") ?>
    <select onChange="location.href = '?path=' + encodeURIComponent(this.value);">
        <option value=""><?= _("Stud.IP-Kern") ?></option>
        <? foreach ($plugins as $plugin) : ?>
        <option value="<?= $plugin['pluginpath'] ?>"<?= $plugin['pluginpath'] === $selected_path ? " selected" : "" ?>><?= htmlReady($plugin['pluginname']) ?></option>
        <? endforeach ?>
    </select>
</label>

<style>
div#errors > div {
  margin: 10px;
  border: 1px dotted #555555;
  padding: 5px;
  background-color: #ffffdd;
}

</style>

<pre id="result">
    <?= $testergebnis ?>
</pre>

<textarea style="display: none;" id="output" aria-label="<?= htmlReady(_("Zwischentestergebnis - wird noch ausgewertet.")) ?>"><?= $testergebnis ?></textarea>

<div id="errors"></div>

<script>
    jQuery(function () {
        var output = jQuery('#output').text();
        //var errors = output.match(/^[.\d\w]*[!\)]\s([.\n\w\s\[\]\(\)=><!:\\\/-]*)_test\.php/);
        output = output.split(/\n/);

        var result = "";

        result += output.pop();
        result += output.pop();
        output.pop();
        output.shift();

        var error = "";
        var start = true;
        jQuery.each(output, function (index, line) {
            if (line[0] !== "\t" && (line.search(/\d+\)/) === 0 || line.search(/Exception /) === 0)) {
                if (error !== "") {
                    jQuery('<div></div>').html(error).appendTo("#errors");
                    error = "";
                    start = true;
                }
            }
            if (start) {
                error += "<b>" + jQuery("<div/>").text(line).html() + "</b>";
            } else {
                error += '<p style="margin: 4px;">' + jQuery("<div/>").text(line).html() + "</p>";
            }
            
            start = false;
        });
        if (error !== "") {
            jQuery('<div></div>').html(error).appendTo("#errors");
        } else {
            if (jQuery('#output').text().search("OK") !== -1) {
                jQuery('<div>' + "Alles bestens!" + "</div>")
                        .appendTo("#errors");
            }
        }
        jQuery("#result").text(result);
    });
</script>

<?
$infobox = array(
    'picture' => $GLOBALS['ABSOLUTE_URI_STUDIP'].$plugin->getPluginPath()."/images/balrog_infobox.png",
    'content' => array(
        array(
            'kategorie' => _("Information"),
            'eintrag' => array(
                array(
                    'icon' => "icons/16/black/info",
                    'text' => _("Es können nur Plugins getestet werden. Die Kern-Tests arbeiten mit PHP-Unit.")
                ),
                array(
                    'icon' => "icons/16/black/info",
                    'text' => _("Im Plugin-Verzeichnis muss ein Verzeichnis \"tests\" liegen mit \"_test.php\" oder \"_testdb.php\" Dateien darin. Siehe Beispiel des FireBIEST-Plugins.")
                )
            )
        )
        
    )
);