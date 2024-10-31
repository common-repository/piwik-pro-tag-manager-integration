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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/*
	 * Deprecated function & action
	 */
	function wp_body_open() {
		_deprecated_function( 'wp_body_open', '2.0.0', 'wp_body' );
		do_action_deprecated( 'wp_body_open', array(), '2.0.0', 'wp_body' );
		do_action( 'wp_body' );
	}
}

if ( ! function_exists( 'wp_body' ) ) {
	/**
	 * Execute functions hooked on a specific custom action hook - 'wp_body'.
	 * According to: https://core.trac.wordpress.org/ticket/12563
	 * Add the following code directly after the <body> tag in your theme:
	 */
	function wp_body() {
		do_action( 'wp_body' );
	}
}
