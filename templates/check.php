<h1><?= _("JSLint angewendet auf: ").htmlentities($_REQUEST['script'] ? $_REQUEST['script'] : "application.js") ?></h1>
<div style="text-align:center">
<textarea id="code_window" name="application_code" cols="82" 
  style="display: none; height: 400px; white-space: nowrap; font-family: MONOSPACE;" 
  readonly 
  wrap="off"
  aria-label="<?= _("Quellcode der JavaScript-Datei") ?>">
    <?php 
        if ($_REQUEST['script'] && (strpos($_REQUEST['script'], ".js") !== false)) {
            $datei = file_get_contents($_REQUEST['script']);
        } else {
            $datei = file_get_contents("assets/javascripts/application.js");
        }
        print $datei;
        $lines = count(explode("\n", $datei));
    ?>
</textarea>
</div>

<label for="select_js_file"><?= _("Auf andere Datei anwenden:") ?></label>
<select onChange="location.href='?script=' + encodeURIComponent(this.value);" id="select_js_file">
    <? foreach ($js_files as $path => $file_name) : ?>
    <option value="<?= $path ?>"<?= $_REQUEST['script'] === $path ? " selected" : "" ?>>
        <?= $file_name ?>
    </option>
    <? endforeach; ?>
</select>

<h2><?= _("\"Fehler\"-Meldungen:") ?></h2>
<style>
div#errors > div {
  margin: 10px;
  border: 1px dotted #555555;
  padding: 5px;
  background-color: #ffffdd;
}
div#errors .error_excerpt {
  padding: 5px;
  padding-left: 20px;
  font-style: italic;
  font-family: Courier New, MONOSPACE;
}
code, ol.code_view > li {
  font-family: Courier New, MONOSPACE;
  font-size: 14px;
  white-space: nowrap;
}
li div.errorline {
    color: #ffffaa;
    background: url(<?= Assets::image_path("vendor/jquery-ui/ui-bg_diagonals-thick_18_b81900_40x40.png") ?>) repeat;
}
</style>
<div id="errors">
    <div><?= _("keine") ?></div>
</div>

<hr style="margin: 20px">

<ol style="list-style-type: decimal;" class="code_view">
    <?php
    $datei = explode("\n", htmlentities($datei));
    foreach ($datei as $key => $zeile) : ?>
    <li id="line<?=($key+1)?>">
        <a name="zeile<?= $key+1 ?>" href="#zeile<?= $key+1 ?>"></a>
        <?= str_replace("\t", "&nbsp;&nbsp;", str_replace(" ", "&nbsp;", $zeile)) ?>
    </li>
    <? endforeach; ?>
</ol>


<script>

var success = JSLINT(jQuery("#code_window").attr("value"), {
    browser: true,
    white: true,
    evil: true,
    undef: true,
    nomen: true,
    eqeqeq: true,
    plusplus: true,
    bitwise: true,
    newcap: true,
    immed: true,
    indent: 2,
    onevar: false,
    predef: ["window", "jQuery", "$", "STUDIP"]
});
if (success === false) {
  jQuery("#errors").html("");
  jQuery.each(JSLINT.errors, function (index, error) {
    if (error && error.reason.indexOf("Unexpected dangling '_'") === -1) {
      error.excerpt_html = jQuery("<div></div>").text(error.evidence).html();
      if (!error.excerpt_html) {
        console.log(jQuery("<div></div>").text(error.evidence));
      }
      error.reason_html = jQuery("<div></div>").text(error.reason).html();
      jQuery("#errors").append("<div>" 
            + "<div class='error_position'><a href=\"#zeile" + error.line + "\"><?= _("Zeile ") ?>" 
              + error.line + " <?= _("Buchstabe") ?> " + error.character + "</a></div>"
            + "<div class='error_excerpt'>" + error.excerpt_html + "</div>"
            + "<div class='error_reason'><?= _("Grund: ") ?>" + error.reason_html + "</div>"
          + "</div>");
      jQuery("#line" + error.line)
            .wrapInner('<div class="errorline"/>')
            .attr("title", "<?= _("Grund: ") ?>" + error.reason_html)
            .attr("onClick", "alert(\"<?= _("Grund: ") ?>" + error.reason + "\")");
    }
  });
  if (jQuery("#errors").html() == "") {
    jQuery("#errors").html("<div><?= _("keine") ?></div>");
  }
}

</script>

