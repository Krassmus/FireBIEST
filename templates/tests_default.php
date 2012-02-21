<h2><?= _("Plugins testen") ?></h2>

<label>
    <?= _("Komponente testen:") ?>
    <select onChange="location.href = '?path=' + encodeURIComponent(this.value);">
        <option value=""><?= _("Stud.IP-Kern") ?></option>
        <? foreach ($plugins as $plugin) : ?>
        <option value="<?= $plugin['pluginpath'] ?>"<?= $plugin['pluginpath'] === $selected_path ? " selected" : "" ?>><?= htmlReady($plugin['pluginname']) ?></option>
        <? endforeach ?>
    </select>
</label>
