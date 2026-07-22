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
use jalendport\lockout\models\Settings;

it('has safe defaults', function() {
    $settings = new Settings();

    expect($settings->enabled)->toBeNull()
        ->and($settings->headline)->toBe('')
        ->and($settings->message)->toBe('')
        ->and($settings->statusCode)->toBe(Settings::STATUS_CODE_FORBIDDEN)
        ->and($settings->template)->toBeNull();
});

it('exposes the supported status code constants', function() {
    expect(Settings::STATUS_CODE_FORBIDDEN)->toBe(403)
        ->and(Settings::STATUS_CODE_UNAVAILABLE)->toBe(503);
});

it('returns fallback lock screen copy for empty settings', function() {
    $settings = new Settings();

    expect($settings->getHeadline())->toBe('Control panel locked')
        ->and($settings->getMessage())->toBe('Access to the control panel is temporarily restricted.');
});

it('returns configured lock screen copy', function() {
    $settings = Factory::settings(Settings::class, [
        'headline' => 'Maintenance in progress',
        'message' => 'Please check back soon.',
    ]);

    expect($settings->getHeadline())->toBe('Maintenance in progress')
        ->and($settings->getMessage())->toBe('Please check back soon.');
});

it('accepts each enabled config state', function(?bool $enabled) {
    $settings = Factory::settings(Settings::class, ['enabled' => $enabled]);

    expect($settings->validate())->toBeTrue();
})->with([
    'true' => true,
    'false' => false,
    'null' => null,
]);

it('accepts only the supported status codes', function() {
    $forbidden = Factory::settings(Settings::class, ['statusCode' => Settings::STATUS_CODE_FORBIDDEN]);
    $unavailable = Factory::settings(Settings::class, ['statusCode' => Settings::STATUS_CODE_UNAVAILABLE]);
    $invalid = Factory::settings(Settings::class, ['statusCode' => 500]);

    expect($forbidden->validate())->toBeTrue()
        ->and($unavailable->validate())->toBeTrue()
        ->and($invalid->validate())->toBeFalse()
        ->and($invalid->hasErrors('statusCode'))->toBeTrue();
});
