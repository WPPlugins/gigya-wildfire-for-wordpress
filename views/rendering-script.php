<script src="http://cdn.gigya.com/wildfire/js/wfapiv2.js" ></script>
<?php
$settings = $this->getSettings();
$partner = $settings[ 'gigya-wildfire-for-wordpress-partner-id' ];
$lang = $settings[ 'gigya-wildfire-for-wordpress-wildfire-language' ];
?>
<script type="text/javascript">
	if( typeof( Wildfire ) != 'undefined' ) {
		Wildfire.renderPostButton = function(divID, bookmark, content) {
			var params = {
				partner: <?php echo $partner; ?>,
				lang: '<?php echo $lang; ?>',
				w: 250,
				h: 220,
				btnurl: 'http://cdn.gigya.com/wildfire/i/share-button.gif',
				b: 'click',
				conf:{showPost:false,showEmail:true,showBookmark:true,UIConfig:'<config baseTheme="v2"><body><controls><snbuttons iconsOnly="true" /></controls></body></config>',emailBody:content,defaultBookmarkURL:bookmark},
				button_divID: divID
			};
			return Wildfire.drawWildfireButton(params);
		};
		<?php foreach( $this->needToRender as $rendering ) { ?>
		Wildfire.renderPostButton( '<?php echo $rendering[ 'div' ]; ?>', '<?php echo $rendering[ 'permalink' ]; ?>', '<?php echo $rendering[ 'content' ]; ?>' );
		<?php } ?>
	}
</script>
