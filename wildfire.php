<?php
/*
Plugin Name: Gigya Wildfire for WordPress
Plugin URI: http://gigya.com
Description: This plugin integrate the Gigya Wildfire bookmarking service into your blog posts quickly and easily.
Author: Nick Ohrn of Plugin-Developer.com
Version: 1.1.1
Author URI: http://plugin-developer.com
*/

if( !class_exists( 'GigyaWildfireForWordPress' ) ) {

	class GigyaWildfireForWordPress {

		// SETTINGS


		/**
		 * Associative array of languages and language codes.
		 *
		 * @var array
		 */
		var $languages = array();

		/**
		 * Default settings for the plugin.  These settings are used when no settings have yet been saved by the user.
		 *
		 * @var array
		 */
		var $defaults = array( 'gigya-wildfire-for-wordpress-wildfire-enable' => 1, 'gigya-wildfire-for-wordpress-wildfire-where' => 'both', 'gigya-wildfire-for-wordpress-wildfire-types' => 'both', 'gigya-wildfire-for-wordpress-partner-id' => '671981' );

		/**
		 * Array of items that need to get rendered via JavaScript.
		 *
		 * @var array
		 */
		var $needToRender = array();

		// MISC


		/**
		 * A string containing the version for this plugin.  Always update this when releaseing a new version.
		 *
		 * @var string
		 */
		var $version = '1.1.0';

		/**
		 * Adds all the appropriate actions and filters.
		 *
		 * @return GigyaWildfireForWordPress
		 */
		function GigyaWildfireForWordPress() {
			register_deactivation_hook( __FILE__, array( &$this, 'deleteSettings' ) );
			add_action( 'admin_menu', array( &$this, 'addAdministrativePage' ) );
			add_action( 'init', array( &$this, 'savePluginSettings' ) );
			add_action( 'wp_footer', array( &$this, 'displayRenderingPage' ) );

			add_filter( 'the_content', array( &$this, 'appendWildfireButton' ) );

			$this->languages = array( 'en' => __( 'English' ), 'zh-hk' => __( 'Chinese (Hong Kong)' ), 'zh-tw' => __( 'Chinese (Taiwan)' ), 'cs' => __( 'Czech' ), 'da' => __( 'Danish' ), 'nl' => __( 'Dutch' ), 'fi' => __( 'Finnish' ), 'fr' => __( 'French' ), 'de' => __( 'German' ), 'el' => __( 'Greek' ), 'hu' => __( 'Hungarian' ), 'it' => __( 'Italian' ), 'ja' => __( 'Japanese' ), 'ko' => __( 'Korean' ), 'no' => __( 'Norwegian' ), 'pl' => __( 'Polish' ), 'pt' => __( 'Portuguese' ), 'pt-br' => __( 'Portuguese (Brazil)' ), 'ru' => __( 'Russian' ), 'es' => __( 'Spanish' ), 'es-mx' => __( 'Spanish (Mexican)' ), 'sv' => __( 'Swedish' ) );
		}

		/// CALLBACKS


		/**
		 * Registers a new administrative page which displays the settings panel.
		 *
		 */
		function addAdministrativePage() {
			add_options_page( __( 'Gigya Wildfire' ), __( 'Gigya Wildfire' ), 'manage_options', 'gigya-wildfire', array( $this, 'displaySettingsPage' ) );
		}

		/**
		 * Appends a Wildfire bookmark button to the post if the settings say to do so.
		 *
		 * @param string $content The content prior to adding the Wildfire bookmark button.
		 */
		function appendWildfireButton( $content ) {
			global $post;
			if( $this->shouldAppendWildfireButton( $post ) ) {
				$settings = $this->getSettings();
				$postInfo = array();
				$postInfo['ID'] = $post->ID;
				$postInfo[ 'permalink' ] = get_permalink( $post->ID );
				$postInfo[ 'title' ] = get_the_title( $post->ID );
				$postInfo[ 'content' ] = str_replace( array( '%URL%', '%SITENAME%', '%TITLE%' ), array( $postInfo[ 'permalink' ], get_bloginfo(), $postInfo[ 'title' ] ), $settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] . '<br /><br />$userMsg$' );
				$postInfo[ 'div' ] = "gw-{$post->ID}";
				$this->needToRender[] = $postInfo;
				$content .= '<div id="' . $postInfo[ 'div' ] . '"></div>';
			}
			return $content;
		}

		/**
		 * Attempts to intercept a POST request that is saving the settings for the GS for WordPress plugin.
		 *
		 */
		function savePluginSettings() {
			$settings = $this->getSettings( );
			if( is_admin( ) && isset( $_POST[ 'save-gigya-wildfire-for-wordpress-settings' ] ) && check_admin_referer( 'save-gigya-wildfire-for-wordpress-settings' ) ) {
				$settings[ 'gigya-wildfire-for-wordpress-partner-id' ] = trim( htmlentities( strip_tags( stripslashes( $_POST[ 'gigya-wildfire-for-wordpress-partner-id' ] ) ) ) );
				$settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] = strip_tags( stripslashes( $_POST[ 'gigya-wildfire-for-wordpress-message-text' ] ), '<a><img><div><span><strong><em><b><i>' );
				$settings[ 'gigya-wildfire-for-wordpress-wildfire-enable' ] = 1 == $_POST[ 'gigya-wildfire-for-wordpress-wildfire-enable' ] ? 1 : 0;
				$settings[ 'gigya-wildfire-for-wordpress-wildfire-types' ] = in_array( $_POST[ 'gigya-wildfire-for-wordpress-wildfire-types' ], array( 'both', 'post', 'page' ) ) ? $_POST[ 'gigya-wildfire-for-wordpress-wildfire-types' ] : 'both';
				$settings[ 'gigya-wildfire-for-wordpress-wildfire-where' ] = in_array( $_POST[ 'gigya-wildfire-for-wordpress-wildfire-where' ], array( 'both', 'main', 'single' ) ) ? $_POST[ 'gigya-wildfire-for-wordpress-wildfire-where' ] : 'both';
				$settings[ 'gigya-wildfire-for-wordpress-wildfire-language' ] = in_array( $_POST[ 'gigya-wildfire-for-wordpress-language' ], array_keys( $this->languages ) ) ? $_POST[ 'gigya-wildfire-for-wordpress-language' ] : 'en';
				$this->saveSettings( $settings );
				wp_redirect( 'options-general.php?page=gigya-wildfire&updated=true' );
				exit( );
			}
		}

		/// DISPLAY

		/**
		 * Includes the necessary inline JavaScript that will render all the appropriate divs.
		 *
		 */
		function displayRenderingPage() {
			if( !empty( $this->needToRender ) ) {
				include 'views/rendering-script.php';
			}
		}

		/**
		 * Outputs the correct HTML for the settings page.
		 *
		 */
		function displaySettingsPage() {
			include ( 'views/settings.php' );
		}

		/// SETTINGS


		/**
		 * Removes the settings for the Gigya Wildfire for WordPress plugin from the database.
		 *
		 */
		function deleteSettings() {
			delete_option( 'Gigya Wildfire for WordPress Settings' );
		}

		/**
		 * Returns the settings for the Gigya Wildfire for WordPress plugin.
		 *
		 * @return array An associative array of settings for the Gigya Wildfire for WordPress plugin.
		 */
		function getSettings() {
			if( $this->settings === null ) {
				$this->settings = get_option( 'Gigya Wildfire for WordPress Settings', $this->defaults );
				if( !isset( $this->settings[ 'gigya-wildfire-for-wordpress-wildfire-language' ] ) ) {
					$this->settings[ 'gigya-wildfire-for-wordpress-wildfire-language' ] = 'en';
				}
				if( !isset( $this->settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] ) || empty( $this->settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] ) ) {
					$this->settings[ 'gigya-wildfire-for-wordpress-wildfire-message-text' ] = __( 'I just read <a href="%URL%">%TITLE%</a> on %SITENAME%.' );
				}
			}

			return $this->settings;
		}

		/**
		 * Saves the settings for the Gigya Wildfire for WordPress plugin.
		 *
		 * @param array $settings An array of settings for the Gigya Wildfire for WordPress plugin.
		 */
		function saveSettings( $settings ) {
			$this->settings = $settings;
			update_option( 'Gigya Wildfire for WordPress Settings', $this->settings );
		}

		/**
		 * Returns a boolean value indicating whether a wildfire button should be appended.
		 *
		 * @param object $post A post object for WordPress.
		 * @return bool true if a Wildfire button should be appended to content and false otherwise.
		 */
		function shouldAppendWildfireButton( $post ) {
			$settings = $this->getSettings( );
			$postType = $post->post_type;
			$wildfireTypes = $settings[ 'gigya-wildfire-for-wordpress-wildfire-types' ];
			$wildfireWhere = $settings[ 'gigya-wildfire-for-wordpress-wildfire-where' ];
			$isEnabled = 1 == $settings[ 'gigya-wildfire-for-wordpress-wildfire-enable' ];
			$isCorrectType = ( $wildfireTypes == 'both' || $wildfireTypes == $postType );
			$isCorrectWhere = ( $wildfireWhere == 'both' || ( $wildfireWhere == 'single' && ( is_single( ) || is_page( ) ) ) || ( $wildfireWhere == 'main' && !is_single( ) ) );
			return $isEnabled && $isCorrectType && $isCorrectWhere;
		}
	}
}

if( class_exists( 'GigyaWildfireForWordPress' ) ) {
	$gwfw = new GigyaWildfireForWordPress( );
}