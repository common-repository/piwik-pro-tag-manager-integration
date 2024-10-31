<?php

/*
	Plugin Name: Piwik PRO Tag Manager integration
	Plugin URI: https://wordpress.org/plugins/piwik-pro-tag-manager-integration
	Description: The plugin integrates WordPress site with Piwik PRO Tag Manager, allowing to add/modify website’s tags without the need to involve IT department.
	Version: 2.2.4
	Author: piwikpro
	Author URI: https://piwik.pro
	Text Domain: piwik-pro-tag-manager-integration
	Domain Path: /languages/
	License: GPLv3
	License URI: http://www.gnu.org/licenses/gpl-3.0.txt

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
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

foreach ( array( 'singleton', 'filterer', 'plugin' ) as $file ) {
	require_once( __DIR__ . "/framework/$file.php" );
}

foreach ( array( 'tag-manager', 'functions' ) as $file ) {
	require_once( __DIR__ . "/includes/$file.php" );
}

try {
	spl_autoload_register( __NAMESPACE__ . '::autoload' );

	if ( ! has_action( __NAMESPACE__ ) ) {
		do_action( __NAMESPACE__, Tag_Manager::instance( __FILE__ ) );
	}
} catch ( Exception $exception ) {
	if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
		echo $exception->getMessage();
	}
}
