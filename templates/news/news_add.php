<?php
/**
 * News-Verwaltung (Admin)
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
// Admin: News-Verwaltung
// Modus: Liste / Neu erstellen / Bearbeiten

$news_mode = 'list'; // Standard: Übersichtsliste
if (isset($_GET['f']) && $_GET['f'] == 'new')  $news_mode = 'new';
if (isset($_GET['f']) && $_GET['f'] == 'edit' && isset($_GET['id'])) $news_mode = 'edit';

// Für Edit-Modus: Daten der News laden
$editNews = null;
if ($news_mode == 'edit') {
    $editNews = $newssystem->getNewsForEdit((int)$_GET['id']);
    if (!$editNews) { $news_mode = 'list'; }
}
?>
<div class="container">
    <!-- Breadcrumbs -->
    <h1>News-Verwaltung</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">&Uuml;bersicht</a></li>
        <li class="breadcrumb-item active">Verwaltung</li>
        <li class="breadcrumb-item"><a href="./?p=news_add">News</a></li>
        <?php if ($news_mode == 'new'):  ?><li class="breadcrumb-item active">Neu</li><?php endif; ?>
        <?php if ($news_mode == 'edit'): ?><li class="breadcrumb-item active">Bearbeiten</li><?php endif; ?>
    </ol>

    <?php echo $error ? $error : ''; ?>

    <?php if ($news_mode == 'list'): ?>
    <!-- ===================== ÜBERSICHTSLISTE ===================== -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <i class="fa fa-newspaper-o"></i> Alle News
                    <a href="./?p=news_add&f=new" class="btn btn-xs btn-success pull-right">
                        <i class="fa fa-plus"></i> Neue News erstellen
                    </a>
                </div>
                <div class="panel-body" style="padding:0;">
                    <table class="table table-striped table-hover" style="margin-bottom:0;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Datum</th>
                                <th>Überschrift</th>
                                <th>Autor</th>
                                <th style="text-align:center;">Sichtbarkeit</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $allNews = $newssystem->listAllNewsAdmin();
                        if (empty($allNews)):
                        ?>
                            <tr><td colspan="7" class="text-center text-muted">Noch keine News vorhanden.</td></tr>
                        <?php else: foreach ($allNews as $row): ?>
                            <tr>
                                <td><?php echo (int)$row['id']; ?></td>
                                <td><?php echo date('d.m.Y', strtotime($row['datum'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars(html_entity_decode($row['ueberschrift'], ENT_QUOTES, 'UTF-8')); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(html_entity_decode(strip_tags($row['kurznews']), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>…</small>
                                </td>
                                <td><?php echo htmlspecialchars($row['autor']); ?></td>
                                <td style="text-align:center;">
                                    <?php if (isset($row['visible']) && $row['visible'] == 1): ?>
                                        <span class="label label-primary" title="Öffentlich – für alle sichtbar">
                                            <i class="fa fa-globe"></i> Extern
                                        </span>
                                    <?php else: ?>
                                        <span class="label label-warning" title="Intern – nur für eingeloggte Mitglieder">
                                            <i class="fa fa-lock"></i> Intern
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php if ($row['active'] == 1): ?>
                                        <span class="label label-success"><i class="fa fa-check"></i> Aktiv</span>
                                    <?php else: ?>
                                        <span class="label label-default"><i class="fa fa-ban"></i> Inaktiv</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <a href="./?p=news_add&f=edit&id=<?php echo (int)$row['id']; ?>"
                                       class="btn btn-xs btn-info" title="Bearbeiten">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="./?p=news_add&c=newstoggle&id=<?php echo (int)$row['id']; ?>"
                                       class="btn btn-xs btn-warning" title="<?php echo $row['active']==1 ? 'Deaktivieren' : 'Aktivieren'; ?>">
                                        <i class="fa fa-<?php echo $row['active']==1 ? 'ban' : 'check'; ?>"></i>
                                    </a>
                                    <a href="./?p=news_add&c=newsdelete&id=<?php echo (int)$row['id']; ?>"
                                       class="btn btn-xs btn-danger" title="Löschen"
                                       onclick="return confirm('News wirklich löschen?');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($news_mode == 'new' || $news_mode == 'edit'): ?>
    <!-- ===================== FORMULAR (NEU / EDIT) ===================== -->
    <?php
    $formAction = ($news_mode == 'edit')
        ? './?p=news_add&c=newsedit'
        : './?p=news_add&c=newsadd';
    $formTitle = ($news_mode == 'edit') ? 'News bearbeiten' : 'Neue News erstellen';

    date_default_timezone_set('Europe/Berlin');
    $datum   = date('d.m.Y');
    $uhrzeit = date('H:i');
    ?>
    <a href="./?p=news_add" class="btn btn-default btn-sm" style="margin-bottom:15px;">
        <i class="fa fa-arrow-left"></i> Zur Übersicht
    </a>

    <form action="<?php echo $formAction; ?>" method="post" enctype="multipart/form-data">
        <?php if ($news_mode == 'edit'): ?>
            <input type="hidden" name="news_id" value="<?php echo (int)$editNews['id']; ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-primary mt15px">
                    <div class="panel-heading">
                        <i class="fa fa-newspaper-o"></i> <?php echo $formTitle; ?>
                    </div>
                    <div class="panel-body">

                        <!-- Datum + Autor -->
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Datum:</label>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                    <input class="form-control" value="<?php echo $datum; ?> um <?php echo $uhrzeit; ?> Uhr" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label>Ersteller:</label>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="hidden" name="autor" value="<?php echo htmlspecialchars($loginsystem->getUser('first_name') . ' ' . $loginsystem->getUser('last_name')); ?>">
                                    <input class="form-control" value="<?php echo htmlspecialchars($loginsystem->getUser('first_name') . ' ' . $loginsystem->getUser('last_name')); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Sichtbarkeit -->
                        <div class="form-group">
                            <label><i class="fa fa-eye"></i> Sichtbarkeit:</label>
                            <div class="row" style="margin-top:8px;">
                                <div class="col-sm-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="visible" value="0"
                                            <?php echo (!isset($editNews['visible']) || $editNews['visible'] == 0) ? 'checked' : ''; ?>>
                                        <i class="fa fa-lock text-warning"></i>
                                        <strong>Intern</strong>
                                        <small class="text-muted">&ndash; nur für eingeloggte Mitglieder, E-Mail an Verein wird gesendet</small>
                                    </label>
                                </div>
                                <div class="col-sm-12" style="margin-top:6px;">
                                    <label class="radio-inline">
                                        <input type="radio" name="visible" value="1"
                                            <?php echo (isset($editNews['visible']) && $editNews['visible'] == 1) ? 'checked' : ''; ?>>
                                        <i class="fa fa-globe text-primary"></i>
                                        <strong>Extern</strong>
                                        <small class="text-muted">&ndash; öffentlich für alle Besucher sichtbar</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Überschrift -->
                        <div class="form-group">
                            <label for="ueberschrift">Überschrift:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-font"></i></span>
                                <input name="ueberschrift" id="ueberschrift" type="text" class="form-control"
                                       maxlength="64" placeholder="Überschrift" required
                                       value="<?php echo isset($editNews['ueberschrift']) ? htmlspecialchars(html_entity_decode($editNews['ueberschrift'], ENT_QUOTES, 'UTF-8')) : ''; ?>">
                            </div>
                        </div>

                        <!-- Kurzbeschreibung -->
                        <div class="form-group">
                            <label for="kurznews">Kurzbeschreibung <small class="text-muted">(wird in der Übersicht angezeigt)</small>:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-align-left"></i></span>
                                <input name="kurznews" id="kurznews" type="text" class="form-control"
                                       maxlength="200" placeholder="Kurze Zusammenfassung..." required
                                       value="<?php echo isset($editNews['kurznews']) ? htmlspecialchars(html_entity_decode($editNews['kurznews'], ENT_QUOTES, 'UTF-8')) : ''; ?>">
                            </div>
                        </div>

                        <hr>

                        <!-- Volltext mit Summernote -->
                        <div class="form-group">
                            <label for="news">Vollständiger Text:</label>
                            <textarea name="news" id="news"><?php
                                echo isset($editNews['news']) ? html_entity_decode($editNews['news'], ENT_QUOTES, 'UTF-8') : '';
                            ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="form-group" style="margin-top:20px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="./?p=news_add" class="btn btn-md btn-warning btn-block">
                                        <i class="fa fa-times"></i> Abbrechen
                                    </a>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-md btn-success btn-block">
                                        <i class="fa fa-save"></i> Speichern
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </form>

    <!-- Summernote (Bootstrap 3) – Dateien nach /js/summernote/ kopieren -->
    <!-- Download: https://github.com/summernote/summernote/releases/tag/v0.8.20 -->
    <link href="/css/summernote.min.css" rel="stylesheet">
    <script src="/js/summernote/summernote.min.js" defer></script>
    <script src="/js/summernote/lang/summernote-de-DE.min.js" defer></script>
    <script src="/js/summernote-init.js" defer></script>

    <?php endif; ?>

</div><!-- /.container -->
