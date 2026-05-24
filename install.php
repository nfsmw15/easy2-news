<?php
declare(strict_types=1);
/**
 * easy2-news – Installer
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *
 * Einmalig in den Easy2-PHP8-Webroot legen, im Browser aufrufen.
 * Die Datei löscht sich nach erfolgreicher Installation selbst.
 */

$root       = __DIR__;
$configFile = $root . '/system/config.inc.php';
$sqlFile    = $root . '/install/sql/_news.sql';
$pluginFile = $root . '/system/plugins/news/run.php';

// --- Pre-checks ---
$checks = [
    'system/config.inc.php'       => file_exists($configFile),
    'install/sql/_news.sql'       => file_exists($sqlFile),
    'system/plugins/news/run.php' => file_exists($pluginFile),
    'PDO-Verbindung'              => false,
    'Konstante Prefix'            => false,
];

$pdo         = null;
$prefix      = null;
$configError = null;

if ($checks['system/config.inc.php']) {
    try {
        require_once $configFile;
        $checks['PDO-Verbindung']  = isset($pdo) && $pdo instanceof PDO;
        $checks['Konstante Prefix'] = defined('Prefix');
        if ($checks['Konstante Prefix']) {
            $prefix = constant('Prefix');
        }
    } catch (Throwable $e) {
        $configError = $e->getMessage();
    }
}

$allChecksPass = !in_array(false, $checks, true);

// --- Already installed? ---
$alreadyInstalled = false;
if ($allChecksPass && $pdo instanceof PDO && $prefix !== null) {
    assert($pdo instanceof PDO);
    try {
        $pdo->query("SELECT 1 FROM `{$prefix}_news` LIMIT 1");
        $alreadyInstalled = true;
    } catch (PDOException) {
        // Tabelle existiert noch nicht
    }
}

// --- SQL-Installation bei POST ---
$message     = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'install') {
    if (!$allChecksPass) {
        $message     = 'Nicht alle Vorab-Checks bestanden. Installation abgebrochen.';
        $messageType = 'error';
    } elseif ($alreadyInstalled) {
        $message     = 'Die Tabelle <strong>' . htmlspecialchars($prefix . '_news') . '</strong> existiert bereits — Installation übersprungen.';
        $messageType = 'warning';
    } else {
        try {
            $sql = (string) file_get_contents($sqlFile);
            $sql = str_replace('[prefix]', $prefix, $sql);

            foreach (explode(';', $sql) as $chunk) {
                $lines = array_filter(
                    explode("\n", $chunk),
                    static fn(string $l): bool => !str_starts_with(ltrim($l), '--')
                );
                $stmt = trim(implode("\n", $lines));
                if ($stmt !== '') {
                    $pdo->exec($stmt);
                }
            }

            $deleted = @unlink(__FILE__);
            $message = 'Installation erfolgreich! Tabelle <strong>'
                . htmlspecialchars($prefix . '_news')
                . '</strong> wurde angelegt. '
                . ($deleted
                    ? 'Diese Datei wurde automatisch gelöscht.'
                    : '<strong>Hinweis:</strong> Die Datei konnte nicht automatisch gelöscht werden — bitte manuell löschen.')
                . ' <a href="index.php">→ Zur Startseite</a>';
            $messageType = 'success';
        } catch (Throwable $e) {
            $message     = 'Fehler bei der Installation: ' . htmlspecialchars($e->getMessage());
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>easy2-news – Installer</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body      { font-family: sans-serif; max-width: 660px; margin: 48px auto; padding: 0 1rem; color: #222; }
        h1        { font-size: 1.4rem; margin-bottom: .2rem; }
        .sub      { color: #666; margin-top: 0; margin-bottom: 1.8rem; }
        h2        { font-size: 1.05rem; margin-bottom: .5rem; }
        table     { width: 100%; border-collapse: collapse; margin-bottom: 1.4rem; }
        th, td    { text-align: left; padding: .45rem .65rem; border: 1px solid #ddd; }
        th        { background: #f5f5f5; }
        .ok       { color: #2a7a2a; font-weight: bold; }
        .fail     { color: #b00020; font-weight: bold; }
        .prefix   { font-family: monospace; background: #f0f0f0; padding: .25rem .55rem; border-radius: 3px; display: inline-block; }
        .msg      { padding: .75rem 1rem; border-radius: 4px; margin-bottom: 1.2rem; line-height: 1.5; }
        .success  { background: #d4edda; border: 1px solid #b8dfc4; color: #155724; }
        .error    { background: #f8d7da; border: 1px solid #f1b8be; color: #721c24; }
        .warning  { background: #fff3cd; border: 1px solid #ffe69c; color: #664d03; }
        .hint     { background: #e8f0fe; border: 1px solid #c5d5fb; color: #1a3a70; padding: .65rem 1rem; border-radius: 4px; margin-bottom: 1.4rem; }
        button    { background: #2a7a2a; color: #fff; border: none; padding: .6rem 1.5rem;
                    font-size: 1rem; border-radius: 4px; cursor: pointer; }
        button:hover     { background: #236023; }
        button:disabled  { background: #999; cursor: not-allowed; }
        .config-error    { color: #b00020; font-size: .88em; }
    </style>
</head>
<body>

<h1>easy2-news – Installer</h1>
<p class="sub">Legt die Datenbanktabelle für das easy2-news Plugin an.</p>

<?php if ($message !== ''): ?>
<div class="msg <?= $messageType ?>">
    <?= $message ?>
</div>
<?php endif; ?>

<?php if ($messageType !== 'success'): ?>

<h2>Vorab-Checks</h2>
<table>
    <tr><th>Prüfung</th><th>Status</th></tr>
    <?php foreach ($checks as $label => $ok): ?>
    <tr>
        <td><?= htmlspecialchars($label) ?></td>
        <td class="<?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? '✓ OK' : '✗ Fehler' ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if ($configError !== null): ?>
    <tr>
        <td colspan="2" class="config-error">
            Fehler beim Laden der Konfiguration: <?= htmlspecialchars($configError) ?>
        </td>
    </tr>
    <?php endif; ?>
</table>

<?php if ($prefix !== null): ?>
<p>Erkannter Prefix: <span class="prefix"><?= htmlspecialchars($prefix) ?></span></p>
<p>Ziel-Tabelle: <span class="prefix"><?= htmlspecialchars($prefix . '_news') ?></span></p>
<?php endif; ?>

<?php if ($alreadyInstalled): ?>
<div class="msg warning">
    Die Tabelle <strong><?= htmlspecialchars($prefix . '_news') ?></strong> existiert bereits.
    Eine erneute Installation ist nicht erforderlich.
</div>
<?php elseif ($allChecksPass): ?>
<div class="hint">
    Alle Checks bestanden. Klick auf <em>Installation starten</em> legt die Tabelle an
    und löscht diese Datei automatisch.
</div>
<form method="post">
    <input type="hidden" name="action" value="install">
    <button type="submit">Installation starten</button>
</form>
<?php else: ?>
<p class="fail">Bitte alle fehlgeschlagenen Checks beheben und die Seite neu laden.</p>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
