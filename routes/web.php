<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'prefix' => 'auth'
], function () use ($router) {
    $router->post('login', 'Auth\LoginAction');

    $router->post('employee/login', 'Auth\OtpLoginAction@do');
    $router->post('employee/verify', 'Auth\OtpCodeVerificationAction@verify');
    $router->post('employee/guest-book', 'Auth\SignUpAction');
});

$router->group([
    'prefix'     => 'app',
    'middleware' => [
        'auth:api-employee'
    ],
], function ($router) {
    $router->post('register-device', 'Notification\CatchDeviceTokenAction');

    $router->group([
        'prefix' => 'dashboard'
    ], function () use ($router) {
        $router->get('/', 'Dashboard\GetClientDashboardDataAction@index');

        $router->group([
            'prefix' => 'request'
        ], function ($router) {
            $router->get('/', 'Dashboard\EmployeeRequest\ShowOwnHistoriesAction');
            $router->post('/', 'Dashboard\EmployeeRequest\RequestWithdrawalAction@do');
        });

        $router->group([
            'prefix' => 'pin'
        ], function ($router) {
            $router->post('/', 'Auth\Employee\CreatePinAction');
            $router->put('/', 'Auth\Employee\ChangePinAction');
            $router->patch('/', 'Auth\Employee\ResetPinAction');
            $router->post('/verify', 'Auth\Employee\VerifyPinAction');
        });

        $router->group([
            'prefix' => 'contact'
        ], function ($router) {
            $router->get('/', 'Dashboard\EmployeeRequest\ShowLastContactAction');
            $router->post('/', 'Dashboard\EmployeeRequest\SetLastContactAction');
        });

        $router->get('notifications', 'Dashboard\Notification\GetEmployeeNotificationAction');
    });
});

$router->group([
    'middleware' => [
        'auth',
    ]
], function ($router) {
    $router->get('notif', 'Helper\SendFireNotificationAction');
    $router->group([
        'prefix' => 'dashboard'
    ], function () use ($router) {
        $router->get('/', 'Dashboard\GetDashboardDataAction');
    });

    $router->group([
        'prefix' => 'employee'
    ], function () use ($router) {

        $router->group([
            'prefix' => 'request'
        ], function () use ($router) {
            $router->get('/', 'EmployeeRequest\ListAllEmployeeRequestByCompanyAction@index');
            $router->put('/', 'EmployeeRequest\ApproveEmployeeRequestAction');
            $router->patch('/', 'EmployeeRequest\DeclineEmployeeRequestAction');
            $router->get('cards', 'EmployeeRequest\ShowCardStatusAction@index');
        });

        $router->get('/', 'Employee\ListAllCompanyEmployeeAction@index');
        $router->post('/', 'Employee\AddEmployeeAction@store');
    });

    $router->group([
        'prefix' => 'company',
    ], function () use ($router) {
        $router->get('departments', 'Company\ListCompanyDepartmentsAction@index');
        $router->get('department/{id:[0-9]+}/positions', 'Company\ListCompanyPositionWithDepartmentAction');
        $router->get('positions', 'Company\ListCompanyPositionsAction@index');

        $router->group([
            'prefix' => 'employee'
        ], function () use ($router) {
            $router->get('/', 'Employee\ListAllCompanyEmployeeAction@index');
            $router->post('/', 'Employee\AddEmployeeAction@store');
            $router->post('/{id:[0-9]+}/edit', 'Employee\EditEmployeeDataAction@put');
            $router->patch('/{id:[0-9]+}', 'Employee\ReassignEmployeeAction@patch');
            $router->delete('/{id:[0-9]+}', 'Employee\ResigningEmployeeAction@delete');
            $router->get('/{id:[0-9]+}/detail', 'Employee\DetailedEmployeeBiographAction@index');
            $router->post('/image', 'Employee\RequestWithPictureAction@index');
        });
    });
});

$router->get('otp-hints', 'Auth\ShowOTPHintsAction@show');

$router->get('/bank', 'Helper\GetAllBankListAction');