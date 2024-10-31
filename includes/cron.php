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
if ( ! class_exists( __NAMESPACE__ . '\Cron' ) ) {
	class Cron {
		const EVENT    = 'schedule_piwik_pro_tag_manager_%s';
		const INTERVAL = 'interval_piwik_pro_tag_manager_%s';

		protected $section = '';

		public function __construct( $section ) {
			$this->section = $section;

			add_action( sprintf( self::EVENT, $this->section ), array( $this, 'schedule' ) );
			add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		}

		public function install() {
			if ( ! wp_next_scheduled( sprintf( self::EVENT, $this->section ) ) )
				wp_schedule_event( current_time( 'timestamp' ), sprintf( self::INTERVAL, $this->section ), sprintf( self::EVENT, $this->section ) );
		}

		public function uninstall() {
			wp_clear_scheduled_hook( sprintf( self::EVENT, $this->section ) );
			remove_action( sprintf( self::EVENT, $this->section ), array( $this, 'schedule' ) );
		}

		public function schedule() {
			$section = $this->get_section();
			$cache   = $section->cache;

			if ( empty( $cache ) ) return;
			$section->clear();
		}

		public function cron_schedules( $schedules ) {
			$section  = $this->get_section();
			$interval = $section->interval;

			$schedules[sprintf( self::INTERVAL, $this->section )] = array(
				'interval' => ( ! empty( $interval ) && is_numeric( $interval ) ? (int)$interval : 60 ) * 60,
				'display'  => Tag_Manager::$name . ' ' . Tag_Manager::__( 'interval' ) . ' ' . Tag_Manager::__( $this->section )
			);
			return $schedules;
		}

		public function get_section() {
			$section = __NAMESPACE__ . '\\' . ucfirst( $this->section );
			return $section::instance();
		}
	}
}
