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
if ( ! class_exists( __NAMESPACE__ . '\Head' ) ) {
	class Head extends Section {
		const SCRIPT = '%s.sync.js';

		/**
		 *  Return head script tag.
		 */
		public function filter_script_loader_tag_0( $tag, $handle, $script ) {
			if ( Tag_Manager::$slug !== $handle  ) return $tag;

			return Tag_Manager::get_template( 'head', array(
				'script'    => $script,
				'time'      => $this->get_ver() ? date( 'Y-m-d H:i:s', $this->get_ver() ) : '',
				'snippet'   => $this->snippet,
                'url'       => $this->url,
                'container' => $this->container
			) );
		}

		/**
		 *  Enqueue head scripts.
		 */
		public function action_wp_enqueue_scripts() {
			if ( 'wp' !== $this->method ) return;
			if ( ! $this->container )     return;
			if ( ! $this->url )           return;

			$handle       = Tag_Manager::$slug;
			$dependencies = $this->dependencies;
			wp_register_script( $handle, $this->get_script(), $dependencies, $this->get_ver(), false );
			wp_enqueue_script(  $handle );
		}

		/**
		 * Echo buffered output.
		 */
		public function filter_shutdown_0() {
			if ( ! $this->is_frontend() ) return;
			if ( ! $this->container )     return;
			if ( ! $this->url )           return;

			$content = '';
			if ( 'ob' === $this->method ) {
				$content = ob_get_clean();
				if ( ! $pos = stripos( $content, '</head>' ) ) goto end;
				$content = substr_replace( $content, Tag_Manager::get_template( 'head', array(
					'script'    => $this->get_ver() ? add_query_arg( 'ver', $this->get_ver(), $this->get_script() ) : $this->get_script(),
					'time'      => $this->get_ver() ? date( 'Y-m-d H:i:s', $this->get_ver() ) : '',
					'snippet'   => $this->snippet,
                    'url'       => $this->url,
                    'container' => $this->container
				) ), $pos, 0 );
			}

			end : echo $content;
		}
	}
}
