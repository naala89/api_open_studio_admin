<?php

/**
 * Class CtrlInvite.
 *
 * Controller for invite page.
 *
 * @package    ApiOpenStudioAdmin
 * @subpackage Controllers
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudioAdmin\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlInvite.
 *
 * Controller for the invite pages.
 */
class CtrlInvite extends CtrlBase
{
    /**
     * {@inheritdoc}
     *
     * @var array Roles permitted to view these pages.
     */
    protected array $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
    ];

    /**
     * View all current invites.
     *
     * @param Request $request Slim request object.
     * @param Response $response Slim response object.
     * @param array $args Slim args array.
     *
     * @return ResponseInterface|Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Invites: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
        $menu = $this->getMenus();
        $params = $request->getParams();
        $invites = [];

        try {
            $result = $this->apiCall(
                'get',
                'invite',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => $params,
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            $invites = isset($result['result']) && isset($result['data']) ? $result['data'] : $result;
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Pagination.
        $page = isset($params['page']) ? $params['page'] : 1;
        $pages = ceil(count($result) / $this->settings['admin']['pagination_step']);
        $invites = array_slice(
            $invites,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true
        );

        return $this->view->render($response, 'invites.twig', [
            'menu' => $menu,
            'invites' => $invites,
            'params' => $params,
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Delete an invite.
     *
     * @param Request $request Slim request object.
     * @param Response $response Slim response object.
     * @param array $args Slim args array.
     *
     * @return ResponseInterface|Response
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Invites: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
        $menu = $this->getMenus();

        if (empty($iid = $args['iid'])) {
            $this->flash->addMessageNow('error', 'Delete invite: invalid invite ID');
            return $response->withStatus(302)->withHeader('Location', '/invites');
        }

        try {
            $result = $this->apiCall(
                'delete',
                "invite/$iid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if (isset($result['result']) && isset($result['data'])) {
                $result = $result['data'];
            }
            $this->flash->addMessageNow('info', $result);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        $result = [];
        try {
            $result = $this->apiCall(
                'get',
                'invite',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if (isset($result['result']) && isset($result['data'])) {
                $result = $result['data'];
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Pagination.
        $page = 1;
        $pages = ceil(count($result) / $this->settings['admin']['pagination_step']);
        $result = array_slice(
            $result,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true
        );

        return $this->view->render($response, 'invites.twig', [
            'menu' => $menu,
            'invites' => $result,
            'params' => [],
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Invite a single or multiple users to ApiOpenStudio.
     *
     * @param Request $request Slim request object.
     * @param Response $response Slim response object.
     * @param array $args Slim args array.
     *
     * @return ResponseInterface|Response
     */
    public function invite(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($email = $allPostVars['invite-email'])) {
            $this->flash->addMessage('error', 'Invite user: email not specified');
            return $response->withRedirect('/invites');
        }

        try {
            $result = $this->apiCall(
                'post',
                "user/invite",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => ['email' => $email],
                ]
            );
            $result = json_decode($result->getBody()->getContents(), true);
            if (isset($result['result']) && isset($result['data'])) {
                $result = $result['data'];
            }

            $message = '';
            if (isset($result['resent'])) {
                $message .= "<p><b>Resent invites:</b><br/>";
                foreach ($result['resent'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            if (isset($result['success'])) {
                $message .= "<p><b>Sent invites:</b><br/>";
                foreach ($result['success'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            if (isset($result['fail'])) {
                $message .= "<p><b>Failed invites:</b><br/>";
                foreach ($result['fail'] as $email) {
                    $message .= "$email<br/>";
                }
                $message .= "</p>";
            }
            $this->flash->addMessage('info', $message);
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }

        return $response->withStatus(302)->withHeader('Location', '/invites');
    }
}
