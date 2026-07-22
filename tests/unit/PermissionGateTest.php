<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

use jalendport\lockout\controllers\DisableController;
use jalendport\lockout\controllers\EnableController;
use jalendport\lockout\services\Lockout;

it('gates state-changing controllers behind POST JSON and manage permission', function(string $controllerClass) {
    $controller = $this->getMockBuilder($controllerClass)
        ->disableOriginalConstructor()
        ->onlyMethods(['requirePostRequest', 'requireAcceptsJson', 'requirePermission', 'runAndReturn'])
        ->getMock();

    $controller->expects($this->once())->method('requirePostRequest');
    $controller->expects($this->once())->method('requireAcceptsJson');
    $controller->expects($this->once())->method('requirePermission')->with(Lockout::PERMISSION_MANAGE);
    $controller->expects($this->once())->method('runAndReturn')->willReturn(null);

    expect($controller->actionIndex())->toBeNull();
})->with([
    EnableController::class,
    DisableController::class,
]);
