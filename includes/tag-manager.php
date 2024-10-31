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

namespace PiwikPRO;

use PiwikPRO\Tag_Manager\Plugin;
use PiwikPRO\Tag_Manager\Settings;
use PiwikPRO\Tag_Manager\Head;
use PiwikPRO\Tag_Manager\Body;
use PiwikPRO\Tag_Manager\Cron;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Tag_Manager' ) ) {
	/**
	 * Class Tag_Manager
	 * @package Clearcode
	 */
	class Tag_Manager extends Plugin {
		protected $sections = array( 'head', 'body' );
		protected $crons    = array();

		public function __construct( $file ) {
			parent::__construct( $file );

			Settings::instance();
			Head::instance();
			Body::instance();

			foreach( $this->sections as $section ) $this->crons[$section] = new Cron( $section );
		}

		/**
		 * If option is not exists, add option with default values on plugin activation.
		 */
		public function activation() {
			if ( ! Head::instance()->get() ) Head::instance()->add();
			if ( ! Body::instance()->get() ) Body::instance()->add();

			foreach( $this->crons as $cron ) $cron->install();
		}

		/**
		 *  Remove option on deactivation.
		 */
		public function deactivation() {
			Head::instance()->delete();
			Body::instance()->delete();

			foreach( $this->crons as $cron ) $cron->uninstall();
		}

		/**
		 * Return list of links to display on the plugins page.
		 *
		 * @param array $links List of links.
		 *
		 * @return mixed List of links.
		 */
		public function filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			if ( empty( static::$name        ) ) return $actions;
			if ( empty( $plugin_data['Name'] ) ) return $actions;
			if ( static::$name == $plugin_data['Name'] )
				array_unshift( $actions, static::get_template( 'link', array(
					'url'   => get_admin_url( null, sprintf( Settings::URL, static::$slug ) ),
					'link'  => static::__( 'Settings' ),
				) ) );

			return $actions;
		}
	}
}
