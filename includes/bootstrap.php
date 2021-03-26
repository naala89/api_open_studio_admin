<?php

/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package ApiOpenStudio
 */

/**
 * Bootstrap file for ApiOpenStudio admin.
 *
 * @package   ApiOpenStudioAdmin
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use ApiOpenStudioAdmin\Core\Config;

session_start();

// Get the settings.
$config = new Config();
$settings = $config->all();
// Move slim config to the root of the settings array.
foreach ($settings['admin']['slim'] as $key => $value) {
    $settings[$key] = $value;
}

// Instantiate the app
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies.
require  __DIR__ . '/container.php';

// Register routes.
require __DIR__ . '/routes.php';

return $app;
