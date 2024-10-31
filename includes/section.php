<?php

/*
	Copyright (C) 2020 by Piwik PRO <https://piwik.pro>
	and associates (see AUTHORS.txt file).

	This file is part of Piwik PRO Tag Manager integration plugin.

	Piwik PRO Tag Manager integration plugin is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	Piwik PRO Tag Manager integration plugin is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Piwik PRO Tag Manager integration plugin; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace PiwikPRO\Tag_Manager;

use PiwikPRO\Tag_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Section' ) ) {
	class Section extends Filterer {
		protected $url          = '';
		protected $container    = '';
		protected $method       = false;
		protected $dependencies = array();
		protected $cache        = false;
		protected $interval     = 60;
		protected $rewrite      = false;
		protected $ver          = false;
		protected $snippet      = '1.16.0';

		protected function __construct() {
			parent::__construct();

			if ( $settings = $this->get() )
				foreach( $this->get_class_vars() as $key => $value )
					if ( ! empty( $settings[$key] ) ) $this->$key = $settings[$key];

			$this->cache();
		}

		public function __get( $name ) {
			if ( isset( $this->$name ) ) return $this->$name;
			return false;
		}

		/**
		 * A callback function that sanitizes the option's value.
		 *
		 * @param array $option Array of value to sanitize.
		 *
		 * @return mixed Sanitized value.
		 */
		public function sanitize( $option = array() ) {
			// TODO add errors & check if src exists before save

			$settings = array();
			foreach( $this->get_class_vars() as $key => $value )
				$settings[$key] = ! empty( $option[$key] ) ? esc_html( $option[$key] ) : $value;

			$settings['url']          = untrailingslashit( $settings['url'] );
			$settings['method']       = in_array( $settings['method'], array( 'wp', 'ob' ) ) ? $settings['method'] : false;
			$settings['dependencies'] = array_map( 'trim', $settings['dependencies'] ? explode( ',', $settings['dependencies'] ) : array() );
			$settings['interval']     = is_numeric( $settings['interval'] ) ? (int)$settings['interval'] : 60;

			foreach( array( 'cache', 'rewrite', 'ver' ) as $key )
				$settings[$key] = (bool)$settings[$key];

			if ( ! empty( $option['clear'] ) ) {
				$this->clear();
			}

			$settings['snippet'] = ! empty( $option['snippet'] ) ? '1.16.0' : '1.15.3';

			return $settings;
		}

		public function cache() {
			if ( ! $this->cache )     return false;
			if ( ! $this->container ) return false;
			if ( ! $this->url )       return false;

			if ( Files::file_exists( $this->get_file() ) && Files::filesize( $this->get_file() ) ) return false;
			if ( ! $content = Files::get_content( $this->get_script( false ) )  )                  return false;

			Files::mkdir( WP_CONTENT_DIR . $this->get_dir() );
			Files::put_content( $this->get_file(), $content );
		}

		public function clear() {
			Files::rmdir( WP_CONTENT_DIR . $this->get_dir() );
			Files::mkdir( WP_CONTENT_DIR . $this->get_dir() );
		}

		public function action_admin_notices() {
			if ( 'settings_page_' . Tag_Manager::$slug !== get_current_screen()->id ) return;
			if ( Settings::instance()->get_tab() !== $this->get_class() )             return;

            $link = Tag_Manager::get_template( 'link', array( 'url' => 'https://wordpress.org/plugins/piwik-pro/', 'link' => 'Piwik PRO' ) );
            echo Tag_Manager::get_template( 'notice', array( 'content' => sprintf( Tag_Manager::__( 'Deprecated: We\'ll be removing this plugin by the end of May 2021. Use the new %s plugin instead.' ), $link ) ) );

            $dir = WP_CONTENT_DIR . $this->get_dir();
			$code = Tag_Manager::get_template( 'code', array( 'content' => $dir ) );
			if     ( ! Files::file_exists( $dir ) ) echo Tag_Manager::get_template( 'notice', array( 'content' => sprintf( Tag_Manager::__( 'Dir: %s does not exists!' ), $code ) ) );
			elseif ( ! Files::is_writable( $dir ) ) echo Tag_Manager::get_template( 'notice', array( 'content' => sprintf( Tag_Manager::__( 'Dir: %s is not writable!' ), $code ) ) );
			elseif ( ! Files::is_readable( $dir ) ) echo Tag_Manager::get_template( 'notice', array( 'content' => sprintf( Tag_Manager::__( 'Dir: %s is not readable!' ), $code ) ) );

			if ( ! got_url_rewrite() ) echo Tag_Manager::get_template( 'notice', array( 'content' => Tag_Manager::__( 'Server not supports URL rewriting.' ) ) );
		}

		/**
		 * Add fields to a Piwik PRO Tag Manager integration section of a settings page.
		 * Register a setting and its sanitization callback.
		 */
		public function action_admin_init() {
			register_setting(     $this->get_class(), static::class, array( $this, 'sanitize' ) );
			add_settings_section( $this->get_class(), '', array( $this, 'section' ), $this->get_class() );

			add_settings_field( static::class . '\url', Tag_Manager::__( 'Server URL' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'input',
					'class'   => 'regular-text',
					'name'    => static::class . '[url]',
					'value'   => $this->url,
					'before'  => 'http(s)://',
					'after'   => '',
					'desc'    => ''
				)
			);

			add_settings_field( static::class .'\container', Tag_Manager::__( 'Website ID' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'input',
					'class'   => 'regular-text',
					'name'    => static::class . '[container]',
					'value'   => $this->container,
					'before'  => '',
					'after'   => '',
					'desc'    => ''
				)
			);

			add_settings_field( static::class . '\method\wp', Tag_Manager::__( 'Method' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => $this->get_class(),
					'name'    => static::class . '[method]',
					'value'   => 'wp',
					'checked' => 'wp' === $this->method ? 'checked' : '',
					'before'  => '',
					'after'   => 'wp_' . $this->get_class(),
					'desc'    => sprintf(
						Tag_Manager::__( 'Paste the following code after %s tag in your theme: %s.' ) . '<br />' .
						Tag_Manager::__( 'Preferred method.' ),
						Tag_Manager::get_template( 'code', array( 'content' => htmlspecialchars( '<' . $this->get_class() . '>' ) ) ),
						Tag_Manager::get_template( 'code', array( 'content' => htmlspecialchars( '<?php wp_' . $this->get_class() . '(); ?>' ) ) )
					)
				)
			);

			add_settings_field( static::class . '\method\ob', '', array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => $this->get_class(),
					'name'    => static::class . '[method]',
					'value'   => 'ob',
					'checked' => 'ob' === $this->method ? 'checked' : '',
					'before'  => '',
					'after'   => 'output buffering',
					'desc'    => sprintf(
						Tag_Manager::__( 'Use this option if you are not sure if the %s function is added to your theme.' ) . '<br />' .
						Tag_Manager::__( 'It will add snippet in %s section using %s.' ),
						Tag_Manager::get_template( 'code', array( 'content' => 'wp_' . $this->get_class() ) ),
						Tag_Manager::get_template( 'code', array( 'content' => htmlspecialchars( '<' . $this->get_class() . '>' ) ) ),
						Tag_Manager::get_template( 'code', array( 'content' => 'output buffering' ) )
					)
				)
			);

			add_settings_field( static::class . '\dependencies', Tag_Manager::__( 'Dependencies' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'text',
					'class'   => 'regular-text',
					'name'    => static::class . '[dependencies]',
					'value'   => implode( ',', $this->dependencies ),
					'before'  => '',
					'after'   => '',
					'desc'    => sprintf(
						Tag_Manager::__( 'Comma separated %s handles. Works only with %s method.' ),
						Tag_Manager::get_template( 'code', array( 'content' => 'wp_enqueue_script' ) ),
						Tag_Manager::get_template( 'code', array( 'content' => 'wp_' . $this->get_class() ) )
					)
				)
			);

			add_settings_field( static::class . '\cache', Tag_Manager::__( 'Cache' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => 'cache',
					'name'    => static::class . '[cache]',
					'value'   => true,
					'checked' => $this->cache ? 'checked' : '',
					'before'  => '',
					'after'   => Tag_Manager::__( 'enable/disable' ),
					'desc'    => sprintf(
						Tag_Manager::__( 'Use this option if you want to serve the script from a cached file' ) . ':<br />' .
						Tag_Manager::get_template( 'code', array(
								'content' => $this->get_domain( str_replace( $this->get_domain( '', true ) , '', WP_CONTENT_URL ) . $this->get_dir(), true )
							)
						)
					)
				)
			);

			$cron = '';
			if ( ! defined( 'DISABLE_WP_CRON' ) || true !== DISABLE_WP_CRON ) {
				if ( file_exists( $file = ABSPATH . 'wp-config.php' ) ) :
				elseif ( file_exists( $file = dirname( ABSPATH ) . '/wp-config.php' ) ) :
				else : $file = 'wp-config.php';
				endif;

				$cron .= sprintf(
					Tag_Manager::__( 'Preferred method is to disable %s and add a task to cron on the server.' ),
					Tag_Manager::get_template( 'code', array( 'content' => 'wp_cron' ) )
				) . '<br /><br />';

				$cron .= Tag_Manager::get_template( 'config', array(
					'message' => Tag_Manager::__( 'Add the following constant to' ),
					'file'    => $file
				) ) . '<br /><br />';

				$cron .= Tag_Manager::get_template( 'cron', array(
					'message' => Tag_Manager::__( 'Add the following task to your cron jobs' ),
					'domain'  => $this->get_domain( null, true )
				) );
			}

			add_settings_field( static::class . '\interval', '', array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'number',
					'class'   => 'interval',
					'name'    => static::class . '[interval]',
					'value'   => $this->interval,
					'before'  => '',
					'after'   => Tag_Manager::__( 'minutes' ),
					'desc'    => sprintf(
						Tag_Manager::__( 'Set refresh the cache interval.' ) . '<br />' .
						$cron
					)
				)
			);

			$domain = is_multisite() ? $this->get_domain() : '';
			$rules = Tag_Manager::get_template( 'rules', array(
				'domain'  => $domain,
				'rewrite' => $this->get_rewrite(),
				'dir'     => str_replace( $this->get_domain( '', true ) , '', WP_CONTENT_URL ) . $this->get_dir()
			) );

			$rewrite = '';
			if ( $this->rewrite &&
			     Files::file_exists( $file = ABSPATH . '.htaccess' ) &&
			     str_replace( "\r\n", "\n", $rules ) != implode( "\n", extract_from_markers( $file, $marker = trim( 'Piwik PRO Tag Manager ' . $domain ) ) ) )
				$rewrite = Tag_Manager::get_template( 'htaccess', array(
					'message' => Tag_Manager::__( 'Add the following rules at the beginning of this file' ),
					'file'    => $file,
					'marker'  => $marker,
					'rules'   => htmlspecialchars( $rules )
				) );

			add_settings_field( static::class . '\rewrite', '', array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => 'rewrite',
					'name'    => static::class . '[rewrite]',
					'value'   => true,
					'checked' => $this->rewrite ? 'checked' : '',
					'before'  => '',
					'after'   => Tag_Manager::__( 'rewrite' ),
					'desc'    =>
						Tag_Manager::__( "Use this option if you want to rewrite the script's url to" ) . ':<br />' .
						Tag_Manager::get_template( 'code', array( 'content' => $this->get_domain( $this->get_rewrite(), true ) ) ) . '<br /><br />' .
						$rewrite
				)
			);

			add_settings_field( static::class . '\clear', '', array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => 'clear',
					'name'    => static::class . '[clear]',
					'value'   => true,
					'checked' => $this->clear ? 'checked' : '',
					'before'  => '',
					'after'   => Tag_Manager::__( 'clear' ),
					'desc'    => sprintf(
						Tag_Manager::__( 'Delete all the cached files and directories from' ) . ':<br />' .
						Tag_Manager::get_template( 'code', array( 'content' => WP_CONTENT_DIR . $this->get_dir() ) )
					)
				)
			);

			add_settings_field( static::class . '\ver', Tag_Manager::__( 'Version' ), array(
					$this,
					'input'
				), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => 'ver',
					'name'    => static::class . '[ver]',
					'value'   => true,
					'checked' => $this->ver ? 'checked' : '',
					'before'  => '',
					'after'   => Tag_Manager::__( 'enable/disable' ),
					'desc'    => sprintf(
						Tag_Manager::__( "Add the %s parameter in the query string for the scripts' url with the timestamp." ),
					    Tag_Manager::get_template( 'code', array( 'content' => 'ver' ) )
					)
				)
			);

			add_settings_field( static::class . '\snippet', Tag_Manager::__( 'Snippet' ), array(
				$this,
				'input'
			), $this->get_class(), $this->get_class(), array(
					'type'    => 'checkbox',
					'class'   => 'snippet',
					'name'    => static::class . '[snippet]',
					'value'   => $snippet = '1.16.0',
					'checked' => version_compare( $this->snippet, $snippet, '>=' ) ? 'checked' : '',
					'before'  => '',
					'after'   => 'Piwik PRO Tag Manager ' . Tag_Manager::__( 'version' ) . ' >= ' . $snippet,
					'desc'    => sprintf(
						Tag_Manager::__( "Uncheck this option to enable support for older versions" )
					)
				)
			);
		}

		public function section() {
			$section = Tag_Manager::get_template( 'code', array( 'content' => htmlspecialchars( '<' . $this->get_class() . '>' ) ) );
			echo Tag_Manager::get_template( 'section', array(
				'content' => sprintf( Tag_Manager::__( 'Include tags in %s section' ), $section ),
				'sections' => array( 'Head', 'Body' ),
			) );
		}

		/**
		 * Join array elements changing array representation key => value to key="value".
		 *
		 * @param array $atts Array of html properties
		 *
		 * @return string String containing a string representation of all the array
		 * elements in the same order, with the glue string between each element.
		 */
		protected function implode( $atts = array() ) {
			array_walk( $atts, function ( &$value, $key ) {
				$value = sprintf( '%s="%s"', $key, esc_attr( $value ) );
			} );

			return implode( ' ', $atts );
		}

		/**
		 * Echo custom field template with custom input.
		 *
		 * @param string $args Name of field.
		 */
		public function input( $args ) {
			extract( $args, EXTR_SKIP );

			echo Tag_Manager::get_template( 'input', array(
					'atts' => self::implode( array(
							'type'  => isset( $type )  ? $type  : '',
							'class' => isset( $class ) ? $class : '',
							'name'  => isset( $name )  ? $name  : '',
							'value' => isset( $value ) ? $value : ''
						)
					),
					'checked' => isset( $checked ) ? $checked : '',
					'before'  => isset( $before )  ? $before  : '',
					'after'   => isset( $after )   ? $after   : '',
					'desc'    => isset( $desc )    ? $desc    : ''
				)
			);
		}

		/**
		 * Start output buffering.
		 *
		 * @param string Template name.
		 *
		 * @return string Template name.
		 */
		public function filter_template_include_0( $template ) {
			if ( ! $this->container ) return $template;
			if ( ! $this->url )       return $template;

			if ( 'ob' === $this->method ) ob_start();

			return $template;
		}

		public function is_frontend() {
			if ( defined( 'DOING_AJAX' )     or
			     defined( 'REST_REQUEST' )   or
			     defined( 'XMLRPC_REQUEST' ) or
			     is_admin() ) return false;
			return true;
		}

		public function get_script( $cache = true ) {
			if ( ! $this->container ) return false;
			if ( ! $this->url )       return false;

			return sprintf( '//%s/' . static::SCRIPT, $this->get_url( $cache ), $this->container );
		}

		public function get_domain( $url = '', $scheme = false ) {
			// wp-admin/includes/network.php get_clean_basedomain()
			return $scheme ? get_site_url( null, $url ) : preg_replace( '|https?://|', '', get_site_url( null, $url ) );
		}

		public function get_url( $cache = true ) {
			if ( ! $this->url ) return false;

			if( Files::file_exists( $this->get_file() ) &&
			    Files::filesize(    $this->get_file() ) &&
			    $this->cache &&
			    $cache )
				$url = $this->rewrite ?
					untrailingslashit( $this->get_domain( $this->get_rewrite(), true ) ) :
					untrailingslashit( WP_CONTENT_URL . $this->get_dir() );
			else $url = $this->url;

			foreach( array( 'https://', 'http://' ) as $schema )
				if ( 0 === strpos( $url, $schema ) ) $url = substr( $url, strlen( $schema ) );

			return apply_filters( static::class . '\Url', $url );
		}

		public function get_rewrite() {
			return apply_filters( static::class . '\Rewrite', 'scripts/' );
		}

		public function get_ver() {
			if ( ! $this->ver )   return null;
			if ( ! $this->cache ) return time();
			if ( ! Files::file_exists( $file = $this->get_file() ) ) return null;
			return Files::filemtime( $file );
		}

		public function get_file() {
			return WP_CONTENT_DIR . $this->get_dir() . sprintf( static::SCRIPT, $this->container );
		}

		public function get_dir() {
			$dir = '/cache';
			if ( is_multisite() ) {
				$site = (array)get_site();
				$dir .= '/' . $site['site_id'] . '/' . $site['blog_id'];
			}
			$dir .= '/scripts';

			return trailingslashit( apply_filters( static::class . '\Dir', $dir ) );
		}

		public function get_class() {
			return strtolower( str_replace( __NAMESPACE__ . '\\', '', static::class ) );
		}

		public function get_class_vars() {
			return get_class_vars( static::class );
		}

		public function get_object_vars() {
			return get_object_vars( $this );
		}

		/**
		 * Retrieve option value based on name of Piwik PRO Tag Manager integration option and key.
		 *
		 * @return array|mixed|void Element from Piwik PRO Tag Manager integration option array.
		 */
		public function get() {
			return get_option( static::class );
		}

		/**
		 * Add option value based on name of Piwik PRO Tag Manager integration option and key.
		 *
		 * @return bool True if success or false if not.
		 */
		public function add() {
			if ( $this->get() ) return $this->update();
			return add_option( static::class, $this->get_object_vars() );
		}

		/**
		 * Update option value based on name of Piwik PRO Tag Manager integration option and key.
		 *
		 * @return bool True if success or false if not.
		 */
		public function update() {
			if ( ! $this->get() ) {
				return $this->add();
			}
			return update_option( static::class, $this->get_object_vars() );
		}

		/**
		 * Delete option value based on name of Piwik PRO Tag Manager integration option and key.
		 *
		 * @return bool True if success or false if not.
		 */
		public function delete() {
			if ( ! $this->get() ) return false;
			return delete_option( static::class );
		}
	}
}
