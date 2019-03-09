<?php
/*
 * Copyright (C) 2017-present, Facebook, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * @package FacebookPixelPlugin
 */

namespace FacebookPixelPlugin\Integration;

defined('ABSPATH') or die('Direct access not allowed');

abstract class FacebookWordpressIntegrationBase {
  const PLUGIN_FILE = '';
  const TRACKING_NAME = '';

  /**
   * inject the pixel code for the plugin
   */
  public static function injectPixelCode() {
  }

  // TODO(T39560845): Add unit test for addPixelFireForHook
  public static function addPixelFireForHook($hook_name, $inject_function, $priority = 11) {
    add_action(
      $hook_name, function () use ($inject_function) {
        add_action('wp_footer', array(
          // get derived class in base class
          static::class, $inject_function), 11);
      },
      $priority);
  }
}
