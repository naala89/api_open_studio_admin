<?php

/**
 * Class CtrlLogin.
 *
 * Controller for login page.
 *
 * @package    ApiOpenStudioAdmin
 * @subpackage Controllers
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudioAdmin\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlLogin.
 *
 * Controller for the login page.
 */
class CtrlLogin extends CtrlBase
{
    /**
     * Login page.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request, Response $response, array $args)
    {
        $menu = $this->getMenus();
        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Logout page.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface Response.
     */
    public function logout(Request $request, Response $response, array $args)
    {
        try {
            $this->apiCall('post', 'logout', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        unset($_SESSION['username']);
        $menu = $this->getMenus();
        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Accept an invite token.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function inviteAccept(Request $request, Response $response, array $args)
    {
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        $allVars = $args;
        $menu = $this->getMenus();

        // Token not received.
        if (empty(($token = $allVars['token']))) {
            $this->flash->addMessageNow('error', 'Invalid token.');
            return $this->view->render($response, 'login.twig', [
                'menu' => $menu,
                'messages' => $this->flash->getMessages(),
            ]);
        }

        try {
            $this->apiCall('post', "user/invite/accept/$token", [
                'headers' => ['Accept' => 'application/json']
            ]);
            $message = '<p>';
            $message .= 'Success, Please visit the home page and click on "forgot Password" to set your password.';
            $message .= '</p>';
            $message .= '<p>';
            $message .= 'If you need any privileged access to applications, please speak to your administrators.';
            $message .= '</p>';
            $this->flash->addMessageNow('info', $message);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Request a user password reset.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function passwordReset(Request $request, Response $response, array $args)
    {
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        $menu = $this->getMenus();

        if ($request->isPost()) {
            $allPostVars = $request->getParams();
            $email = $allPostVars['email'];
            if (empty($email)) {
                $this->flash->addMessageNow('error', 'Invalid email.');
                return $this->view->render($response, 'login.twig', [
                    'menu' => $menu,
                    'messages' => $this->flash->getMessages(),
                ]);
            }
            try {
                $result = $this->apiCall(
                    'post',
                    'password/reset',
                    [
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'form_params' => [
                            'email' => $email,
                        ],
                    ]
                );
                $this->flash->addMessageNow(
                    'info',
                    'Password reset link activated, which will expire in 15 minutes. Please check your emails.'
                );
            } catch (\Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
            }
            return $this->view->render($response, 'login.twig', [
                'menu' => $menu,
                'messages' => $this->flash->getMessages(),
            ]);
        }

        return $this->view->render($response, 'password-reset.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Reset a user password.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function setPassword(Request $request, Response $response, array $args)
    {
        unset($_SESSION['token']);
        unset($_SESSION['uid']);
        $menu = $this->getMenus();

        if ($request->isGet()) {
            if (empty($token = $args['token'])) {
                $this->flash->addMessageNow('error', 'Invalid password reset link.');
                return $this->view->render($response, 'login.twig', [
                    'menu' => $menu,
                    'messages' => $this->flash->getMessages(),
                ]);
            }

            return $this->view->render($response, 'password-set.twig', [
                'menu' => $menu,
                'token' => $token,
                'messages' => $this->flash->getMessages(),
            ]);
        }

        $allPostVars = $request->getParams();
        $token = $allPostVars['token'];
        $password = $allPostVars['password'];
        $confirmPassword = $allPostVars['confirm-password'];

        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $this->flash->addMessageNow('error', 'Invalid password reset request.');
            return $this->view->render($response, 'login.twig', [
                'menu' => $menu,
                'messages' => $this->flash->getMessages(),
            ]);
        }

        if ($password != $confirmPassword) {
            $this->flash->addMessageNow('error', 'Passwords do not match.');
            return $this->view->render($response, 'password-set.twig', [
                'menu' => $menu,
                'token' => $token,
                'messages' => $this->flash->getMessages(),
            ]);
        }

        try {
            $result = $this->apiCall(
                'post',
                'password/reset',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'token' => $token,
                        'password' => $password,
                    ],
                ]
            );
            $this->flash->addMessageNow('info', 'Password reset.');
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->view->render($response, 'login.twig', [
            'menu' => $menu,
            'messages' => $this->flash->getMessages(),
        ]);
    }
}
