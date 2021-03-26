<?php

/**
 * Slim PHP containers declaration file for ApiOpenStudio admin.
 *
 * @package   ApiOpenStudioAdmin
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

use Slim\Container;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use ApiOpenStudioAdmin\Controllers;

$container = $app->getContainer();

/**
 * Flash container.
 *
 * @return Messages
 */
$container['flash'] = function () {
    return new Messages();
};

/**
 * Register Twig View container.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return Twig
 *   Twig object.
 */
$container['view'] = function (Container $container) {
    $settings = $container->get('settings');
    $viewDir = $settings['admin']['base_path'] . $settings['twig']['template_path'];
    $publicDir = $settings['admin']['base_path'] . $settings['admin']['dir_public'];

    $twigSettings = $settings['twig']['options'];
    $twigSettings['debug'] = $settings['debug'];
    $twig = new Twig($viewDir, $twigSettings);
    $loader = $twig->getLoader();
    $loader->addPath($publicDir, 'public');

    // Instantiate and add twig extension/s.
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment($container->get('environment'));
    $twig->addExtension(new TwigExtension($router, $uri));
    $twig->addExtension(new DebugExtension());

    return $twig;
};

/**
 * Register Account controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlAccount
 *   CtrlApplication object.
 */
$container['CtrlAccount'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlAccount($settings, $view, $flash);
};

/**
 * Register Account controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlAccount
 *   CtrlApplication object.
 */
$container['CtrlApplication'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlApplication($settings, $view, $flash);
};

/**
 * Register Home controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlHome
 *   CtrlHome object.
 */
$container['CtrlHome'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlHome($settings, $view, $flash);
};

/**
 * Register User controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlUser
 *   CtrlUser object.
 */
$container['CtrlInvite'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlInvite($settings, $view, $flash);
};

/**
 * Register Login controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlLogin
 *   CtrlLogin object.
 */
$container['CtrlLogin'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlLogin($settings, $view, $flash);
};

/**
 * Register Resource controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return Controllers\CtrlResource
 *   CtrlResource object.
 */
$container['CtrlResource'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlResource($settings, $view, $flash);
};

/**
 * Register Role controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return Controllers\CtrlRole
 *   CtrlRole object.
 */
$container['CtrlRole'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlRole($settings, $view, $flash);
};

/**
 * Register User controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlUser
 *   CtrlUser object.
 */
$container['CtrlUser'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlUser($settings, $view, $flash);
};

/**
 * Register Users controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return ApiOpenStudio\Admin\Controllers\CtrlUsers
 *   CtrlUser object.
 */
$container['CtrlUsers'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlUsers($settings, $view, $flash);
};

/**
 * Register User controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return Controllers\CtrlUserRole
 *   CtrlUserRole object.
 */
$container['CtrlUserRole'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlUserRole($settings, $view, $flash);
};

/**
 * Register Vars controller.
 *
 * @param Container $container
 *   Slim container.
 *
 * @return Controllers\CtrlVars
 *   CtrlRole object.
 */
$container['CtrlVars'] = function (Container $container) {
    $settings = $container->get('settings');
    $view = $container->get('view');
    $flash = $container->get('flash');
    return new Controllers\CtrlVars($settings, $view, $flash);
};
