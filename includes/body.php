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
if ( ! class_exists( __NAMESPACE__ . '\Body' ) ) {
	class Body extends Section {
		const SCRIPT   = '%s.js';
		const NOSCRIPT = '%s/noscript.html';

		/**
		 * Check whether a scripts has been added to the queue.
		 *
		 * @param string $handle Name of the script.
		 * @param string $list   Optional. Status of the script to check. Default 'enqueued'.
		 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
		 * @return bool Whether the script is queued.
		 */
		public function wp_script_is() {
			if ( empty( $this->dependencies ) ) return false;
			foreach( $this->dependencies as $dependence )if ( ! wp_script_is( $dependence, 'done' ) ) return false;
			return true;
		}

		/**
		 *  Echo asynchronous snippet.
		 */
		public function action_wp_body() {
			if ( 'wp' !== $this->method ) return;
			if ( ! $this->container )     return;
			if ( ! $this->url )           return;

			if ( ! empty( $this->dependencies ) &&
			     ! $this->wp_script_is() ) return;

			echo $this->get_snippet();
		}

		/**
		 * Echo buffered output.
		 *
		 */
		public function filter_shutdown_0() {
			if ( ! $this->is_frontend() ) return;
			if ( ! $this->container ) return;
			if ( ! $this->url )       return;

			$content = '';
			if ( 'ob' === $this->method ) {
				$content = ob_get_clean();
				if ( ! preg_match('/<body[^>]*>/i', $content ) ) goto end;
				$pos     = stripos( $content, '<body' );
				$pos     = stripos( $content, '>', $pos );
				$content = substr_replace( $content, $this->get_snippet(), $pos + 1, 0 );
			}

			end : echo $content;
		}

		public function get_snippet() {
			if ( ! $this->container ) return false;
			if ( ! $this->url )       return false;

			return Tag_Manager::get_template( 'body', array(
				'script'    => $this->get_ver() ? add_query_arg( 'ver', $this->get_ver(), $this->get_script() ) : $this->get_script(),
				'noscript'  => $this->get_noscript(),
				'url'       => $this->url,
				'container' => $this->container,
				'time'      => $this->get_ver() ? date( 'Y-m-d H:i:s', $this->get_ver() ) : '',
				'snippet'   => $this->snippet
			) );
		}

		public function get_file( $pattern = self::SCRIPT ) {
			return WP_CONTENT_DIR . $this->get_dir() . sprintf( $pattern, $this->container );
		}

		public function get_url( $cache = true, $pattern = self::SCRIPT ) {
			if ( ! $this->url ) return false;

			if( Files::file_exists( $this->get_file( $pattern ) ) &&
			    Files::filesize(    $this->get_file( $pattern ) ) &&
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

		public function get_noscript( $cache = true ) {
			if ( ! $this->container ) return false;
			if ( ! $this->url )       return false;

			return sprintf( '//%s/' . static::NOSCRIPT, $this->get_url( $cache ), $this->container );
		}

		public function cache() {
			parent::cache();

			if ( ! $this->cache )     return false;
			if ( ! $this->container ) return false;
			if ( ! $this->url )       return false;

			if ( Files::file_exists( $this->get_file( self::NOSCRIPT ) ) &&
			     Files::filesize(    $this->get_file( self::NOSCRIPT ) ) )          return false;
			if ( ! $content = Files::get_content( $this->get_noscript( false ) )  ) return false;

			Files::mkdir( WP_CONTENT_DIR . $this->get_dir() . '/' . $this->container );
			Files::put_content( $this->get_file( self::NOSCRIPT ), $content );
		}
	}
}
