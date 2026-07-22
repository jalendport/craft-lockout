<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\models;

use craft\base\Model;

/**
 * The plugin's settings.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool|null Whether Lockout is enabled, when pinned from
     * `config/lockout.php`. `null` (the default) hands control back to the
     * per-environment database flag the utility and CLI write to; a non-null
     * value wins over it.
     *
     * This setting is deliberately absent from the settings page — it exists so
     * a lockout can be committed to version control and made environment- or
     * env-var-aware.
     * @since 3.0.0
     */
    public ?bool $enabled = null;

    /**
     * @var string The message shown to locked-out users. Falls back to a
     * generic string when empty.
     * @since 1.0.0
     */
    public string $message = '';

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['enabled'], 'boolean', 'skipOnEmpty' => true];
        $rules[] = [['message'], 'string'];

        return $rules;
    }
}
