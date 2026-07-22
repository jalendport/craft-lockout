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
use jalendport\lockout\Lockout as LockoutPlugin;
use jalendport\lockout\models\Settings;
use jalendport\lockout\records\LocalSettings;
use jalendport\lockout\services\Lockout as LockoutService;
use craft\base\Model;
use yii\caching\ArrayCache;

require_once dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';
require_once dirname(__DIR__) . '/vendor/craftcms/cms/src/Craft.php';

/**
 * App-free stand-in for the database-backed local settings record.
 */
class TestLocalSettings extends LocalSettings
{
    public bool $enabled = false;

    public int $saveCalls = 0;

    public function __construct(private ArrayCache $cache)
    {
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        $this->saveCalls++;
        $this->cache->set('enabled', $this->enabled);

        return true;
    }
}

/**
 * Builds the service around fixed app-free state seams.
 */
function lockoutService(?bool $configOverride, LocalSettings $localSettings): LockoutService
{
    return new class($configOverride, $localSettings) extends LockoutService {
        public function __construct(private ?bool $configOverride, private LocalSettings $localSettings)
        {
            parent::__construct();
        }

        public function getConfigOverride(): ?bool
        {
            return $this->configOverride;
        }

        public function getLocalSettings(): LocalSettings
        {
            return $this->localSettings;
        }
    };
}

/**
 * Builds the service with a fixed effective state for bypass tests.
 */
function lockoutServiceWithEnabled(bool $enabled): LockoutService
{
    return new class($enabled) extends LockoutService {
        public function __construct(private bool $enabled)
        {
            parent::__construct();
        }

        public function getIsEnabled(): bool
        {
            return $this->enabled;
        }
    };
}

/**
 * Installs an app-free plugin double for config override resolution.
 */
function installLockoutPlugin(?bool $enabled, bool $declared = true): void
{
    $settings = Factory::settings(Settings::class, ['enabled' => $enabled]);
    $overrides = $declared ? ['enabled'] : [];

    LockoutPlugin::$plugin = new class($settings, $overrides) extends LockoutPlugin {
        public function __construct(private Settings $settings, private array $overrides)
        {
        }

        public function getConfigOverrides(): array
        {
            return $this->overrides;
        }

        public function getSettings(): ?Model
        {
            return $this->settings;
        }
    };
}
