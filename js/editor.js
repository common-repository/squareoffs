jQuery(document).ready(function(){
jQuery("#wp-content-editor-container iframe").contents().find("head").append(jQuery("<script type='text/javascript'>").attr("src","//squareoffs.com/assets/embed.js"));
});