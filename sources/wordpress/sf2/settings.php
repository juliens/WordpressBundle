<div class="wrap">
<h2>Symfony2 configuration</h2>
<form method="post" action="options.php">
    <?php @settings_fields('wp_symfony_settings'); ?>
    <?php @do_settings_fields('wp_symfony_settings'); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Path vers symfony2</th>
<?php 
$path = get_option('symfony2_path');
?>
            <td><input type="text" name="symfony2_path" id="symfony2_path" style="width:300px" value="<?php echo $path; ?>" />
<?php
if (!file_exists($path.'app/bootstrap.php.cache')) {
    echo "<span style=\"color:red;\">Chemin invalide</span>";
} else {
    echo "<span style=\"color:green;\">Symfony trouvé</span>";
}
?>
</td>

            </tr>
        <tr valign="top">
            <th scope="row">Environnement (dev, prod)</th>
<?php 
$env = get_option('symfony2_env');
?>
            <td><input type="text" name="symfony2_env" id="symfony2_env" style="width:300px" value="<?php echo $env; ?>" />
</td>

            </tr>
<tr>
<td colspan="2">
<?php @submit_button() ?>
</td>
</tr>
    </table>
</form>
</div>
