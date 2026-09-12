<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('lang/(:segment)', 'Language::switch/$1');
$routes->get('portsys-assets/(:any)', 'PortsysAssets::show/$1');
$routes->match(['post', 'put', 'patch', 'delete'], 'portsys-assets/(:any)', 'PortsysAssets::misdirected/$1');
$routes->get('favicon.ico', 'PortsysAssets::favicon');

// Public pages
$routes->get('about', 'Pages::about');
$routes->get('services', 'Pages::services');
$routes->get('ports-directory', 'Pages::portsDirectory');
$routes->get('ships-directory', 'Pages::shipsDirectory');
$routes->get('news', 'Pages::news');
$routes->get('contact', 'Contact::create');
$routes->post('contact', 'Contact::store');
$routes->get('privacy', 'Pages::privacy');
$routes->get('terms', 'Pages::terms');
$routes->get('faq', 'Pages::faq');

// Authentication
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerUser');
$routes->post('logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('search', 'Search::index');

    // Account
    $routes->get('profile', 'Profile::index');
    $routes->post('profile', 'Profile::update');
    $routes->post('profile/password', 'Profile::changePassword');

    // Notifications
    $routes->get('notifications', 'NotificationController::index');
    $routes->get('notifications/feed', 'NotificationController::feed');
    $routes->get('notifications/read/(:num)', 'NotificationController::markRead/$1');
    $routes->post('notifications/read-all', 'NotificationController::markAllRead');
    $routes->post('notifications/clear-read', 'NotificationController::clearRead');

    // Trips and user requests
    $routes->get('trips', 'Trip::index', ['filter' => 'permission:view_trips']);
    $routes->get('trips/show/(:num)', 'Trip::show/$1', ['filter' => 'permission:view_trip_details']);
    $routes->post('trip-requests', 'TripRequest::create', ['filter' => 'permission:create_trip_request']);
    $routes->get('trip-requests/my', 'TripRequest::myRequests', ['filter' => 'permission:create_trip_request']);
    $routes->post('trip-requests/(:num)/cancel', 'TripRequest::cancel/$1', ['filter' => 'permission:create_trip_request']);
    $routes->get('my-trips', 'MyTrips::index', ['filter' => 'permission:view_trips']);
    $routes->get('my-trips/download', 'MyTrips::downloadCsv', ['filter' => 'permission:download_my_trips']);

    // Read-only operational directories for authenticated users
    $routes->get('ports', 'Port::index', ['filter' => 'permission:view_ports']);
    $routes->get('ships', 'Ship::index', ['filter' => 'permission:view_ships']);

    $routes->group('', ['filter' => 'role:admin'], static function (RouteCollection $routes): void {
        // Ports CRUD
        $routes->get('ports/create', 'Port::create');
        $routes->post('ports', 'Port::store');
        $routes->post('ports/import-place', 'Port::importPlace');
        $routes->post('ports/sync-places', 'Port::syncFromPlaces');
        $routes->get('ports/edit/(:num)', 'Port::edit/$1');
        $routes->post('ports/update/(:num)', 'Port::update/$1');
        $routes->post('ports/delete/(:num)', 'Port::delete/$1');

        // Places CRUD
        $routes->get('places', 'Place::index');
        $routes->get('places/create', 'Place::create');
        $routes->post('places', 'Place::store');
        $routes->get('places/edit/(:num)', 'Place::edit/$1');
        $routes->post('places/update/(:num)', 'Place::update/$1');
        $routes->post('places/delete/(:num)', 'Place::delete/$1');
        $routes->post('places/import-defaults', 'Place::importDefaults');

        // Ships CRUD
        $routes->get('ships/create', 'Ship::create');
        $routes->post('ships', 'Ship::store');
        $routes->get('ships/edit/(:num)', 'Ship::edit/$1');
        $routes->post('ships/update/(:num)', 'Ship::update/$1');
        $routes->post('ships/delete/(:num)', 'Ship::delete/$1');

        // Trips management
        $routes->get('trips/export', 'Trip::export');
        $routes->get('trips/create', 'Trip::create');
        // Accept both /trips and /trips/create submissions. This protects the
        // create screen from bad cached actions or XAMPP index.php URL quirks.
        $routes->post('trips/create', 'Trip::store');
        $routes->post('trips', 'Trip::store');
        $routes->get('trips/edit/(:num)', 'Trip::edit/$1');
        $routes->post('trips/update/(:num)', 'Trip::update/$1');
        $routes->post('trips/delete/(:num)', 'Trip::delete/$1');

        // Trip requests moderation
        $routes->get('trip-requests', 'TripRequest::index');
        $routes->post('trip-requests/(:num)/status/(:segment)', 'TripRequest::updateStatus/$1/$2');
        $routes->get('trips/(:num)/accepted', 'TripRequest::acceptedForTrip/$1');

        // User administration
        $routes->get('users', 'Users::index');
        $routes->post('users/(:num)/role', 'Users::updateRole/$1');
        $routes->post('users/(:num)/status', 'Users::updateStatus/$1');

        // Contact inbox and audit log
        $routes->get('messages', 'Contact::index');
        $routes->get('messages/(:num)', 'Contact::show/$1');
        $routes->post('messages/(:num)/read', 'Contact::markRead/$1');
        $routes->post('messages/(:num)/delete', 'Contact::delete/$1');
        $routes->get('activity-log', 'ActivityLog::index');
    });
});
