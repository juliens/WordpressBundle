<div class="wrap">
<h2>Symfony2 configuration</h2>
<form method="post" action="options.php">
    <?php @settings_fields('wp_symfony_settings'); ?>
    <?php @do_settings_fields('wp_symfony_settings'); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">URL</th>
<?php 
$path = get_option('symfony2_path');
?>
            <td><input type="text" name="symfony2_path" id="symfony2_path" style="width:300px" value="<?php echo $path; ?>" />
<?php
                if (!file_exists($path.'app/bootstrap.php.cache')) {
                    echo "Chemin invalide";
                } else {
                    echo "OK";
                }
?>
</td>

            </tr>
<?php foreach ($shortcodes as $shortcode) {
?>
    <tr><th>Shortcode <?php echo $shortcode->getName() ?></th><td><input type="checkbox" id="shortcode_<?php echo $shortcode->getName(); ?>" name="shortcode_<?php echo $shortcode->getName(); ?>" <?php  echo (get_option('shortcode_'.$shortcode->getName())==1) ? 'checked="checked"' : ''; ?> value="1" /> 
<?php
} ?>
<tr>
<td colspan="2">
<?php @submit_button() ?>
</td>
</tr>
    </table>
</form>
