<?php $settings = $this->getSettings(); ?>
<div class="wrap">
	<h2><?php _e( 'Gigya Wildfire Settings' ); ?></h2>
	<form method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="gigya-wildfire-for-wordpress-partner-id"><?php _e( 'Gigya Partner ID' ); ?></label></th>
					<td>
						<input type="text" class="regular-text" id="gigya-wildfire-for-wordpress-partner-id" name="gigya-wildfire-for-wordpress-partner-id" value="<?php echo attribute_escape( $settings[ 'gigya-wildfire-for-wordpress-partner-id' ] ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="gigya-wildfire-for-wordpress-message-text"><?php _e( 'Message Text' ); ?></label></th>
					<td>
						<textarea class="large-text" rows="3" name="gigya-wildfire-for-wordpress-message-text"><?php echo htmlentities( $settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] ); ?></textarea><br />
						<?php _e( 'Use the following placeholders in the text above.  This text will be used when others share your article.' ); ?>
						<ul>
							<li><?php _e( '<code>%SITENAME%</code> - Replaced with the name of your WordPress blog.' ); ?></li>
							<li><?php _e( '<code>%URL%</code> - Replaced with the link to the post being shared.' ); ?></li>
							<li><?php _e( '<code>%TITLE%</code> - Replaced with the title of the post being shared.' ); ?></li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Wildfire Bookmark Button' ); ?></th>
					<td>
						<input <?php checked( 1, $settings[ 'gigya-wildfire-for-wordpress-wildfire-enable' ] ); ?>type="checkbox" value="1" id="gigya-wildfire-for-wordpress-wildfire-enable" name="gigya-wildfire-for-wordpress-wildfire-enable" />
						<?php _e( 'I want to enable the Wildfire bookmark button for' ); ?>
						<select id="gigya-wildfire-for-wordpress-wildfire-types" name="gigya-wildfire-for-wordpress-wildfire-types">
							<option <?php selected( 'both', $settings[ 'gigya-wildfire-for-wordpress-wildfire-types' ] ); ?> value="both"><?php _e( 'Posts and Pages' ); ?>
							<option <?php selected( 'post', $settings[ 'gigya-wildfire-for-wordpress-wildfire-types' ] ); ?> value="post"><?php _e( 'Posts' ); ?></option>
							<option <?php selected( 'page', $settings[ 'gigya-wildfire-for-wordpress-wildfire-types' ] ); ?> value="page"><?php _e( 'Pages' ); ?></option>
						</select>
						<?php _e( 'on' ); ?>
						<select id="gigya-wildfire-for-wordpress-wildfire-where" name="gigya-wildfire-for-wordpress-wildfire-where">
							<option <?php selected( 'both', $settings[ 'gigya-wildfire-for-wordpress-wildfire-where' ] ); ?> value="both"><?php _e( 'the main loop and single item pages' ); ?></option>
							<option <?php selected( 'main', $settings[ 'gigya-wildfire-for-wordpress-wildfire-where' ] ); ?> value="main"><?php _e( 'the main loop only' ); ?></option>
							<option <?php selected( 'single', $settings[ 'gigya-wildfire-for-wordpress-wildfire-where' ] ); ?> value="single"><?php _e( 'single item pages only' ); ?></option>
						</select>
					</td>
					<tr>
						<th scope="row"><label for="gigya-wildfire-for-wordpress-language"><?php _e( 'Language' ); ?></label></th>
						<td>
							<select name="gigya-wildfire-for-wordpress-language">
								<?php foreach( $this->languages as $languageCode => $language ) { ?>
								<option <?php selected( $settings[ 'gigya-wildfire-for-wordpress-wildfire-language' ], $languageCode ); ?> value="<?php echo $languageCode; ?>"><?php echo htmlentities( $language ); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php wp_nonce_field( 'save-gigya-wildfire-for-wordpress-settings' ); ?>
			<input type="submit" name="save-gigya-wildfire-for-wordpress-settings" id="save-gigya-wildfire-for-wordpress-settings" value="<?php _e( 'Save Settings' ); ?>" />
		</p>
	</form>
</div>