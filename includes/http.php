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
if ( ! class_exists( __NAMESPACE__ . '\HTTP' ) ) {
	class HTTP {
		protected $response = null;

		public function __construct( $url ) {
			$this->response = wp_remote_get( set_url_scheme( $url ), array( 'sslverify' => false ) );
		}

		public function get() {
			return $this->response;
		}

		public function code() {
			return wp_remote_retrieve_response_code( $this->response );
		}

		public function body() {
			return wp_remote_retrieve_body( $this->response );
		}

		public function is_wp_error() {
			return is_wp_error( $this->response );
		}

		public function error() {
			return $this->response->get_error_messages();
		}
	}
}
