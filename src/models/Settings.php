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

use Craft;
use craft\base\Model;

/**
 * The plugin's settings.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    // Const Properties
    // =========================================================================

    /**
     * Answer locked-out requests with `403 Forbidden`: the lockout is about who
     * you are, not whether the site is up.
     *
     * @since 3.0.0
     */
    public const STATUS_CODE_FORBIDDEN = 403;

    /**
     * Answer locked-out requests with `503 Service Unavailable`, plus a
     * `Retry-After` header built from Craft's own `retryDuration` setting.
     * Signals "come back later" to monitors and crawlers.
     *
     * @since 3.0.0
     */
    public const STATUS_CODE_UNAVAILABLE = 503;

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
     * @var string The lock screen's headline. Falls back to a generic string
     * when empty.
     * @since 3.0.0
     */
    public string $headline = '';

    /**
     * @var string The lock screen's message. Falls back to a generic string
     * when empty.
     * @since 1.0.0
     */
    public string $message = '';

    /**
     * @var int The HTTP status code locked-out requests are answered with.
     * One of the `STATUS_CODE_` constants.
     * @since 3.0.0
     */
    public int $statusCode = self::STATUS_CODE_FORBIDDEN;

    /**
     * @var string|null A site template rendered in place of the stock lock
     * screen. It's handed `headline` and `message`.
     * @since 3.0.0
     */
    public ?string $template = null;

    // Public Methods
    // =========================================================================

    /**
     * Returns the headline to show on the lock screen.
     *
     * @return string the headline
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getHeadline(): string
    {
        return $this->headline ?: Craft::t('lockout', 'Control panel locked');
    }

    /**
     * Returns the message to show on the lock screen.
     *
     * @return string the message
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getMessage(): string
    {
        return $this->message ?: Craft::t('lockout', 'Access to the control panel is temporarily restricted.');
    }

    /**
     * Builds the option list for the "Status Code" dropdown.
     *
     * @return array<int, array{label: string, value: int}> the options
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getStatusCodeOptions(): array
    {
        return [
            [
                'label' => Craft::t('lockout', '403 (Forbidden)'),
                'value' => self::STATUS_CODE_FORBIDDEN,
            ],
            [
                'label' => Craft::t('lockout', '503 (Service Unavailable)'),
                'value' => self::STATUS_CODE_UNAVAILABLE,
            ],
        ];
    }

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
        $rules[] = [['headline', 'message', 'template'], 'string'];
        $rules[] = [['statusCode'], 'in', 'range' => [self::STATUS_CODE_FORBIDDEN, self::STATUS_CODE_UNAVAILABLE]];

        return $rules;
    }
}
