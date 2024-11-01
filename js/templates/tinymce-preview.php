<?php
/**
 * Template for the TinyMCE preview.
 *
 * @package squareoffs
 */

?>
	<#
	if(data.size == "wide"){ 
		print('<iframe id="embed_square_off_'+data.id+'" class="squareoffs-embed align-'+data.align+'" src="//squareoffs.com/square_offs/'+data.id+'" width="100%" height="850" frameborder="0" scrolling="no" style="width: 100%; margin:'+data.margin+';"> </iframe>');

	}else if(data.size == "medium"){
		print('<iframe id="embed_square_off_'+data.id+'" class="squareoffs-embed align-'+data.align+'" src="//squareoffs.com/square_offs/'+data.id+'" width="300" height="362" frameborder="0" scrolling="no" style="margin:'+data.margin+';"> </iframe>');
	}else if(data.size == "small"){
		print('<iframe id="embed_square_off_'+data.id+'" class="squareoffs-embed align-'+data.align+'" src="//squareoffs.com/square_offs/'+data.id+'?size=small" width="300" height="250" frameborder="0" scrolling="no" style="margin:'+data.margin+';"> </iframe>');
	}
	#>