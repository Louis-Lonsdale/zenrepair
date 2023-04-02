<?php

/**
 * Registers routes onto the Slim application instance.
 * 
 * @author B Moss <P2595849@mydmu.ac.uk>
 * Date: 02/01/23
 */

declare(strict_types = 1);

use Slim\App;
use App\Controller\UserController;
use App\Controller\WorkshopController;
use App\Controller\AdminController;
use App\Controller\DeviceController;
use App\Controller\TicketController;
use Slim\Routing\RouteCollectorProxy;
use App\Controller\CustomerController;
use App\Controller\DashboardController;
use App\Controller\ErrorController;
use App\Middleware\GuardianMiddleware;

return function (App $app)
{
    $app->group('/workshop', function(RouteCollectorProxy $workshop) {
        $workshop->get('/dashboard', [WorkshopController::class, 'viewDashboard']);
        $workshop->get('/settings', [WorkshopController::class, 'viewSettings']);

        $workshop->get('/tickets', [WorkshopController::class, 'viewTickets']);
        $workshop->get('/ticket/{id}', [WorkshopController::class, 'viewTicket']);

        $workshop->get('/customers', [WorkshopController::class, 'viewCustomers']);
        $workshop->get('/customer/{id}', [WorkshopController::class, 'viewCustomer']);

        $workshop->get('/devices', [WorkshopController::class, 'viewDevices']);
        $workshop->get('/device/{id}', [WorkshopController::class, 'viewDevice']);

        $workshop->get('/create/{context}', [WorkshopController::class, 'viewCreate']);
        $workshop->get('/notice/{notice}', [WorkshopController::class, 'getNotice']);
    })->add(GuardianMiddleware::class);

    $app->group('/admin', function(RouteCollectorProxy $admin) {
        $admin->get('/users', [AdminController::class, 'viewUsers']);
        $admin->get('/user/{id}', [AdminController::class, 'viewUser']);

        $admin->get('/settings', [AdminController::class, 'viewSettings']);
    })->add(GuardianMiddleware::class);

    $app->group('/dashboard', function(RouteCollectorProxy $dashboard) {
        $dashboard->get('/get/stats', [DashboardController::class, 'getStats']);
    })->add(GuardianMiddleware::class);

    $app->group('/users', function (RouteCollectorProxy $users) {
        $users->get('/get', [UserController::class, 'getUserRecords']);
        $users->get('/get/{id}', [UserController::class, 'getUserRecord']);

        $users->put('/create', [AccountController::class]);
        $users->put('/update', [AccountController::class]);
        $users->put('/delete', [AccountController::class]);
    })->add(GuardianMiddleware::class);

    $app->group('/customers', function (RouteCollectorProxy $customers) {
        $customers->get('/get', [CustomerController::class, 'getRecords']);
        $customers->get('/get/{id}', [CustomerController::class, 'getRecord']);
        $customers->get('/create', [CustomerController::class, 'getCreateForm']);

        $customers->post('/create', [CustomerController::class, 'create']);
        $customers->post('/search', [CustomerController::class, 'searchRecords']);

        $customers->put('/update', [CustomerController::class]);
        $customers->delete('/delete/{id}', [CustomerController::class, 'delete']);
    })->add(GuardianMiddleware::class);

    $app->group('/tickets', function (RouteCollectorProxy $tickets) {
        $tickets->get('/get/creator', [TicketController::class, 'getCreator']);

        $tickets->get('/get', [TicketController::class, 'getRecords']);
        $tickets->get('/get/{id}', [TicketController::class, 'getRecord']);

        $tickets->put('/create', [TicketController::class]);
        $tickets->put('/update', [TicketController::class]);
        $tickets->put('/delete', [TicketController::class]);
    })->add(GuardianMiddleware::class);

    $app->group('/devices', function (RouteCollectorProxy $devices) {
        $devices->get('/get', [DeviceController::class, 'getRecords']);
        $devices->get('/get/{id}', [DeviceController::class, 'getRecord']);
        $devices->get('/create', [DeviceController::class, 'getCreateForm']);

        $devices->post('/create', [DeviceController::class, 'create']);
        $devices->put('/update', [DeviceController::class]);
        $devices->put('/delete', [DeviceController::class]);
    })->add(GuardianMiddleware::class);

    $app->group('/errors', function (RouteCollectorProxy $error) {
        $error->get('/not-found', [ErrorController::class, 'notFoundError']);

        $error->delete('/clear', [ErrorController::class, 'clearAll']);
    })->add(GuardianMiddleware::class);
};