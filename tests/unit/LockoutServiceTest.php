<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

use jalendport\base\testing\Factory;
use jalendport\lockout\services\Lockout;

afterEach(function() {
    Craft::$app = null;
});

it('enables and disables the database-backed state', function() {
    $cache = Factory::cache();
    $record = new TestLocalSettings($cache);
    $service = lockoutService(null, $record);

    expect($service->enable())->toBeTrue()
        ->and($record->enabled)->toBeTrue()
        ->and($cache->get('enabled'))->toBeTrue();

    expect($service->disable())->toBeTrue()
        ->and($record->enabled)->toBeFalse()
        ->and($cache->get('enabled'))->toBeFalse()
        ->and($record->saveCalls)->toBe(2);
});

it('resolves config override precedence', function(bool $databaseEnabled, ?bool $configOverride, bool $expected, string $source) {
    $record = new TestLocalSettings(Factory::cache());
    $record->enabled = $databaseEnabled;
    $service = lockoutService($configOverride, $record);

    expect($service->getIsEnabled())->toBe($expected)
        ->and($service->getSource())->toBe($source);
})->with([
    'database false with null override' => [false, null, false, Lockout::SOURCE_DB],
    'database true with null override' => [true, null, true, Lockout::SOURCE_DB],
    'true override wins over database false' => [false, true, true, Lockout::SOURCE_CONFIG],
    'false override wins over database true' => [true, false, false, Lockout::SOURCE_CONFIG],
]);

it('reads a declared enabled config override', function(?bool $enabled, string $source) {
    installLockoutPlugin($enabled);

    $service = new Lockout();

    expect($service->getConfigOverride())->toBe($enabled)
        ->and($service->getSource())->toBe($source);
})->with([
    'true' => [true, Lockout::SOURCE_CONFIG],
    'false' => [false, Lockout::SOURCE_CONFIG],
    'null' => [null, Lockout::SOURCE_DB],
]);

it('ignores enabled when config does not declare an override', function() {
    installLockoutPlugin(true, false);

    expect((new Lockout())->getConfigOverride())->toBeNull();
});

it('does not block anyone while Lockout is disabled', function() {
    expect(lockoutServiceWithEnabled(false)->getIsBlocked())->toBeFalse();
});

it('applies the bypass permission and administrator exemption', function(bool $admin, bool $bypass, bool $blocked) {
    $user = new class($admin, $bypass) {
        public function __construct(private bool $admin, private bool $bypass)
        {
        }

        public function checkPermission(string $permission): bool
        {
            expect($permission)->toBe(Lockout::PERMISSION_BYPASS);

            return $this->bypass;
        }

        public function getIsAdmin(): bool
        {
            return $this->admin;
        }
    };

    Craft::$app = new class($user) {
        public function __construct(private object $user)
        {
        }

        public function getUser(): object
        {
            return $this->user;
        }
    };

    expect(lockoutServiceWithEnabled(true)->getIsBlocked())->toBe($blocked);
})->with([
    'administrator' => [true, false, false],
    'user with bypass permission' => [false, true, false],
    'user without bypass permission' => [false, false, true],
]);

it('exposes the namespaced permission handles', function() {
    expect(Lockout::PERMISSION_MANAGE)->toBe('lockout:manage')
        ->and(Lockout::PERMISSION_BYPASS)->toBe('lockout:bypass');
});
