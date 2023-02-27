<?php

/**
 * Registers routes onto the Slim application instance.
 * 
 * @author B Moss <P2595849@mydmu.ac.uk>
 * Date: 02/01/23
 */

declare(strict_types = 1);

use Slim\App;
use App\Action\AuthController;
use App\Action\DeviceController;
use App\Action\TicketController;
use Slim\Routing\RouteCollectorProxy;
use App\Action\CustomerController;
use App\Action\DashboardController;
use App\Middleware\LocalAuthMiddleware;

return function (App $app)
{
    $app->group('/', function (RouteCollectorProxy $auth) {
        $auth->get('', [AuthController::class, 'index']);
        $auth->post('', [AuthController::class, 'authUser']);
        $auth->get('logout', [AuthController::class, 'logout']);

        $auth->get('debug', [AuthController::class, 'debug0']);
    });

    $app->group('/dashboard', function (RouteCollectorProxy $dashboard) {
        $dashboard->get('', [DashboardController::class, 'index']);
    })->add(LocalAuthMiddleware::class);

    $app->group('/tickets', function (RouteCollectorProxy $tickets) {
        $tickets->get('', [TicketController::class, 'index']);

        $tickets->get('/get/creator', [TicketController::class, 'getCreator']);
        $tickets->get('/get/creator/next', [TicketController::class, 'getCreatorNext']);
        $tickets->post('/create/{id}', [TicketController::class, 'createTicket']);

        $tickets->get('/view/{id}', [TicketController::class, 'ticketView']);
        $tickets->get('/get/list', [TicketController::class, 'getList']);

        $tickets->post('/delete/{id}', [TicketController::class, 'deleteTicket']);
    })->add(LocalAuthMiddleware::class);

    $app->group('/customers', function (RouteCollectorProxy $customers) {
        $customers->get('', [CustomerController::class, 'index']);
        $customers->get('/get/list', [CustomerController::class, 'getList']);

        $customers->get('/get/creator', [CustomerController::class, 'getCreator']);
        $customers->post('/create', [CustomerController::class, 'create']);

        $customers->get('/view/{id}', [CustomerController::class, 'viewCustomer']);
        $customers->get('/get/record/{id}', [CustomerController::class, 'getRecord']);

        $customers->post('/delete/{id}', [CustomerController::class, 'deleteTicket']);
    })->add(LocalAuthMiddleware::class);

    $app->group('/devices', function (RouteCollectorProxy $devices) {
        $devices->get('', [DeviceController::class, 'index']);
        $devices->get('/get/list', [DeviceController::class, 'getList']);

        $devices->get('/create', [DeviceController::class, 'createView']);
        $devices->post('/create', [DeviceController::class, 'createDevice']);
        
        $devices->get('/view/{id}', [DeviceController::class, 'viewRecord']);
        $devices->get('/get/record/{id}', [DeviceController::class, 'getRecord']);
        
        $devices->post('/delete/{id}', [DeviceController::class, 'deleteDevice']);
    })->add(LocalAuthMiddleware::class);
};