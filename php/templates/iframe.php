<?php
/**
 * Iframe SquareOffs
 *
 * Called from php/shortcode.php
 *
 * @package squareoffs
 */
?>
<iframe id="embed_square_off_<?php echo (int) $atts['id']; ?>" 
	class="squareoffs-embed align-<?php echo $atts["align"];?>" 
	src="https://squareoffs.com/square_offs/<?php echo (int) $atts['id']; ?>?size=<?php echo $atts["size"];?>" 
	<?php if($atts["size"] == "wide"){ ?>
		height="761" 
		width="100%" 
	<?php }elseif($atts["size"] == "medium"){ ?>
		height="426.75" 
		width="300" 
	<?php }elseif($atts["size"] == "small"){ ?>
		height="250" 
		width="300" 
	<?php } ?>
	name="embed_square_off_<?php echo (int) $atts['id']; ?>" 
	frameborder="0" 
	scrolling="no" 
	referrerpolicy="no-referrer-when-downgrade"
	></iframe>