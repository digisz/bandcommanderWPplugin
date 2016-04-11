<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Bandcommander WP Plugin
Plugin URI:  http://bandcommander.ch
Description: Display upcoming Shows of band
Version:     0.1
Author:      Pascal Reichmuth
Author URI:  http://bdd-communication.ch
License:     GLP2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: bandcommander-wp
*/

function bandcommander_shortcode() {
	$token = get_option('bandcommander_token');
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'api.bandcommander.ch/events/?token='.$token);
$result = curl_exec($ch);
curl_close($ch);
$obj = json_decode($result);
	
	
	ob_start();
    ?>
<?php for ($i = 0; $i < count($obj); $i++) {
?>
<!-- Ort -->

<div itemscope="" itemtype="http://schema.org/MusicEvent">
  <h3 itemprop="name"><?php echo $obj[$i]->band ?> @ <?php echo $obj[$i]->location ?> <?php echo $obj[$i]->place ?></h3>
  <div itemprop="location" itemscope="" itemtype="http://schema.org/MusicVenue">
    <meta itemprop="name" content="<?php echo $obj[$i]->location ?> <?php echo $obj[$i]->place ?>"/>
  </div>
  <!-- Tickets -->
  <?php if($obj[$i]->links->tickets){ ?>
  <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
    <link itemprop="url" href="<?php echo $obj[$i]->links->tickets ?>" />
    <a href="<?php echo $obj[$i]->links->tickets ?> target="_blank">Tickets kaufen</a> </div>
  <?php } ?>
  <!-- Facebook -->
  <?php if($obj[$i]->links->facebook){ ?>
  <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
    <link itemprop="url" href="<?php echo $obj[$i]->links->facebook ?>" />
    <a href="<?php echo $obj[$i]->links->tickets ?>" target="_blank">Facebook Event</a> </div>
  <?php } ?>
  <div>
    <div itemprop="startDate" content="<?php echo $obj[$i]->date ?>"><?php echo $obj[$i]->date ?></div>
  </div>
  <div itemprop="performer" itemscope="" itemtype="http://schema.org/MusicGroup">
    <meta itemprop="name" content="<?php echo $obj[$i]->band ?>">
  </div>
</div>
<?php } ?>
<?php
    return ob_get_clean();
	
}
function bandcommander_register_shortcode() {
    add_shortcode( 'bandcommander', 'bandcommander_shortcode' );
}
add_action( 'init', 'bandcommander_register_shortcode' );
?>
<?php
add_action('admin_menu', 'my_admin_menu');

function my_admin_menu () {
  add_management_page('Bandcommander', 'Bandcommander', 'manage_options', __FILE__, 'bandcommander_admin_page');
}

function bandcommander_admin_page () {

  $textvar = get_option('bandcommander_token', 'demoToken');
  if (isset($_POST['change-clicked'])) {
    update_option( 'bandcommander_token', $_POST['token'] );
    $textvar = get_option('bandcommander_token', 'demoToken');
  }
?>
<div class="wrap">
  <h1>Bandcommander Token</h1>
  <p>Bitte hier Bandcommander Token eingeben:</p>
  <form action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    Token:
    <input type="text" value="<?php echo $textvar; ?>" name="token" placeholder="token">
    <br />
    <input name="change-clicked" type="hidden" value="1" />
    <input type="submit" value="Change Token" />
  </form>
</div>
<?php }?>
