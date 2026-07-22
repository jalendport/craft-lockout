<?php
/**
 * Lockout — Spark Craft Lab seed hook
 *
 * Lockout needs no schema or content; it follows the config-file pattern.
 * The written config overrides `message` and `statusCode` (so the settings
 * page and utility demonstrate the “overridden by config” UX) while leaving
 * `enabled` and `headline` untouched, so the utility toggle and the headline
 * field stay live for CP inspection. The generic lab seed provides an admin
 * and a non-admin editor but grants the editor no permissions, so this hook
 * adds `accessCp` — without it Craft rejects the CP login outright and the
 * lock screen can never be reached.
 */

use craft\helpers\FileHelper;

return function ($controller = null): void {
    $editor = Craft::$app->getUsers()->getUserByUsernameOrEmail('editor@lab.test');

    if ($editor !== null) {
        Craft::$app->getUserPermissions()->saveUserPermissions($editor->id, ['accessCp']);

        if ($controller !== null) {
            $controller->stdout("Granted accessCp to editor@lab.test.\n");
        }
    }

    $config = <<<'PHP'
<?php

return [
    // Leave `enabled` out so the DB flag (and the utility toggle) stays live.
    'message' => 'Back after the deploy.',
    'statusCode' => 503,
];
PHP;

    FileHelper::writeToFile(Craft::getAlias('@config') . '/lockout.php', $config);

    if ($controller !== null) {
        $controller->stdout("Wrote config/lockout.php with message + statusCode overrides.\n");
    }
};
