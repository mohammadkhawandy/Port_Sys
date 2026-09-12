<?php

namespace App\Controllers;

use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\TripRequest;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['form', 'url', 'permission', 'portsys'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $shared = [
            'notificationUnreadCount' => 0,
            'notificationItems' => [],
            'unreadMessagesCount' => 0,
            'pendingTripRequestsCount' => 0,
        ];

        if (session()->get('isLoggedIn')) {
            $userId = (int) (session()->get('userId') ?? 0);
            if ($userId > 0) {
                try {
                    $shared['notificationUnreadCount'] = (new Notification())
                        ->where('user_id', $userId)
                        ->where('is_read', 0)
                        ->countAllResults();

                    $shared['notificationItems'] = (new Notification())
                        ->where('user_id', $userId)
                        ->orderBy('created_at', 'DESC')
                        ->findAll(7);
                } catch (\Throwable $e) {
                    log_message('debug', 'Notifications unavailable: {message}', ['message' => $e->getMessage()]);
                }

                if (is_admin_user()) {
                    try {
                        $shared['unreadMessagesCount'] = (new ContactMessage())
                            ->where('is_read', 0)
                            ->countAllResults();
                    } catch (\Throwable $e) {
                        log_message('debug', 'Contact inbox unavailable: {message}', ['message' => $e->getMessage()]);
                    }

                    try {
                        $shared['pendingTripRequestsCount'] = (new TripRequest())
                            ->where('status', 'pending')
                            ->countAllResults();
                    } catch (\Throwable $e) {
                        log_message('debug', 'Trip requests unavailable: {message}', ['message' => $e->getMessage()]);
                    }
                }
            }
        }

        service('renderer')->setData($shared);
    }
}
