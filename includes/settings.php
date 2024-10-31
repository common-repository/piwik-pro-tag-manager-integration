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
if ( ! class_exists( __NAMESPACE__ . '\Settings' ) ) {
	class Settings extends Filterer {
		const URL  = 'options-general.php?page=%s';

		/**
		 * Add JavaScript code to Piwik PRO Tag Manager integration settings page.
		 */
		public function action_admin_enqueue_scripts( $page ) {
			if ( 'settings_page_' . Tag_Manager::$slug == $page ) {
				wp_register_script( $page, Tag_Manager::$url . '/assets/js/script.js', array( 'jquery' ), Tag_Manager::$version, true );
				wp_enqueue_script(  $page );
			}
		}

		/**
		 * Add Piwik PRO Tag Manager integration settings page.
		 */
		public function action_admin_menu_999() {
			add_options_page(
				Tag_Manager::__( 'Piwik PRO Tag Manager integration' ),
				Tag_Manager::get_template( 'menu', array(
					'class'   => 'dashicons-before dashicons-tag',
					'content' => Tag_Manager::__( 'Piwik PRO Tag Manager integration' ) ) ),
				'manage_options',
				Tag_Manager::$slug,
				array( $this, 'page' )
			);
		}

		/**
		 * Echo custom settings page template.
		 */
		public function page() {
			$sections = array();
			foreach ( array( 'head', 'body' ) as $section )
				$sections[$section] = sprintf( Tag_Manager::__( 'Include tags in %s section' ), $section );

			echo Tag_Manager::get_template( 'page', array(
				'sections' => $sections,
				'section'  => $this->get_tab(),
				'content'  => Tag_Manager::__( 'Settings' ),
				'title'    => Tag_Manager::$name,
				'url'      => sprintf( self::URL, Tag_Manager::$slug ),
			) );
		}

		public function get_tab() {
			return isset( $_GET['tab'] ) && $_GET['tab'] === 'body' ? 'body' : 'head';
		}
	}
}
