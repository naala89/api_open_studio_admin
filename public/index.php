<?php

/**
 * ApiOpenStudio Admin page.
 *
 * @package   ApiOpenStudioAdmin
 * @license   This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

/**
 * Slim application.
 *
 * @var Slim\App $app
 */
$app = require dirname(__DIR__) . '/includes/bootstrap.php';

// Start
$app->run();
