<style>
div#errors > div {
  margin: 10px;
  border: 1px dotted #555555;
  padding: 5px;
  background-color: #ffffdd;
}
</style>

<h2><?= _("Plugins testen") ?></h2>

<div style="text-align: center;">
    <select id="plugin_selector">
        <option value=""><?= _("Plugin auswählen") ?></option>
        <? foreach ($plugins as $p) : ?>
        <option value="<?= $p['pluginpath'] ?>"<?= $p['pluginpath'] === $selected_path ? " selected" : "" ?>><?= htmlReady($p['pluginname']) ?></option>
        <? endforeach ?>
    </select>
    <?= Studip\LinkButton::create(_("Test starten"), "", array('onClick' => 'STUDIP.FireBIEST.start_test(); return false;')) ?>
</div>

<div id="progressbar" style="margin: 10px;"></div>
<div id="progress_time" style="display: none;"><?= $_SESSION['unit_test_progress_time'] ? (int) $_SESSION['unit_test_progress_time'] : "8000" ?></div>

<div id="result"></div>

<textarea style="display: none;" id="output" aria-label="<?= htmlReady(_("Zwischentestergebnis - wird noch ausgewertet.")) ?>"></textarea>

<div id="errors"></div>

<script>
    STUDIP.FireBIEST = {
        'test_in_progress': false,
        'test_starttime': false,
        'start_test': function () {
            var plugin = jQuery("#plugin_selector").val();
            if (STUDIP.FireBIEST.test_in_progress || !plugin) {
                return false;
            }
            jQuery("#errors").html("").hide();
            jQuery("#progressbar").css('opacity', "1").show();
            STUDIP.FireBIEST.test_in_progress = true;
            var render_progressbar = function (percent, overall_time) {
                jQuery("#progressbar").progressbar({ 'value': percent });
                if (percent <= 100) {
                    window.setTimeout(function () {
                        if (STUDIP.FireBIEST.test_in_progress) {
                            render_progressbar(percent + 1, overall_time);
                        }
                    }, overall_time / 100);
                } else {
                    jQuery("#progressbar").css('opacity', "0.5");
                }
            }
            render_progressbar(0, parseInt(jQuery("#progress_time").html(), 10));
            STUDIP.FireBIEST.test_starttime = new Date();
            jQuery.ajax({
                'url': STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/firebiest/ajax_tests",
                'data': {
                    'plugin': plugin
                },
                'dataType': "html",
                'success': STUDIP.FireBIEST.parse_errors,
                'error': function () {
                    jQuery("#progressbar").hide();
                    jQuery("#errors")
                        .append(jQuery("<div/>").text(errorThrown));
                    STUDIP.FireBIEST.test_in_progress = false;
                }
            });
        },
        'parse_errors': function (output) {
            STUDIP.FireBIEST.test_in_progress = false;
            jQuery("#progress_time").html(new Date() - STUDIP.FireBIEST.test_starttime);
            jQuery("#progressbar").hide();
            jQuery("#errors").html("").show();
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
                jQuery('<div>' + "Alles bestens!" + "</div>")
                        .appendTo("#errors");
            }
            //jQuery("#result").text(result);
        }
    };
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
                    'text' => _("Im Plugin-Verzeichnis muss ein Verzeichnis \"tests\" existieren mit Dateien, die auf \"_test.php\" oder \"_testdb.php\" enden. Siehe Beispiel des FireBIEST-Plugins.")
                )
            )
        )

    )
);