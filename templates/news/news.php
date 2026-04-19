<?php
/**
 * News-Einzelansicht (Frontend)
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *
 * p=news&n={id}
 */
$newsId  = isset($_GET['n']) ? (int)$_GET['n'] : 0;
$newsRow = ($newsId > 0) ? $newssystem->getNews($newsId) : false;
?>
<div class="container">

    <?php if (!$newsRow): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning" style="margin-top:40px;">
                    <i class="fa fa-exclamation-triangle"></i>
                    Dieser News-Eintrag existiert nicht oder ist f&uuml;r dich nicht sichtbar.
                </div>
                <a href="./?p=newsall" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Zur&uuml;ck zur &Uuml;bersicht
                </a>
            </div>
        </div>
    <?php else:
        $datum = date('d.m.Y', strtotime($newsRow['datum']));
        $zeit  = substr($newsRow['zeit'], 0, 5);
        $badge = '';
        if (isset($newsRow['visible'])) {
            $badge = ($newsRow['visible'] == 1)
                ? '<span class="label label-primary"><i class="fa fa-globe"></i> Extern</span> '
                : '<span class="label label-warning"><i class="fa fa-lock"></i> Intern</span> ';
        }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mt-4 mb-3"><i class="fa fa-newspaper-o"></i> 
                <?php echo html_entity_decode($newsRow['ueberschrift'], ENT_QUOTES, 'UTF-8'); ?>
                <small>von <i class="fa fa-user"></i> <?php echo htmlspecialchars($newsRow['autor']); ?></small>
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">Home</a></li>
                <li class="breadcrumb-item"><a href="./?p=newsall">News</a></li>
                <li class="breadcrumb-item active"><?php echo html_entity_decode($newsRow['ueberschrift'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
            <?php if ($badge): ?><div style="margin:15px 0 10px 0;"><?php echo $badge; ?></div><?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <hr>
            <p class="text-muted">
                <i class="fa fa-clock-o"></i>
                Geschrieben am <?php echo $datum; ?> um <?php echo $zeit; ?> Uhr
            </p>
            <hr>
            <p class="lead"><?php echo html_entity_decode(strip_tags($newsRow['kurznews']), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="news-content">
                <?php echo $newsRow['news']; ?>
            </div>
            <hr>
            <a href="./?p=newsall" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Zur&uuml;ck zur &Uuml;bersicht
            </a>
            <?php if ($loginsystem->login_session() && $loginsystem->auditRight('newsadd')): ?>
            <a href="./?p=news_add&f=edit&id=<?php echo $newsId; ?>" class="btn btn-info btn-sm">
                <i class="fa fa-pencil"></i> Bearbeiten
            </a>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="well" style="margin-top:20px;">
                <h4><i class="fa fa-newspaper-o"></i> Weitere News</h4>
                <?php
                $weitere = $newssystem->listNews(5, 0);
                foreach ($weitere as $w):
                    if ($w['id'] == $newsId) continue;
                    $wDatum = date('d.m.Y', strtotime($w['datum']));
                ?>
                    <p>
                        <a href="./?p=news&n=<?php echo (int)$w['id']; ?>">
                            <?php echo html_entity_decode($w['ueberschrift'], ENT_QUOTES, 'UTF-8'); ?>
                        </a><br>
                        <small class="text-muted"><?php echo $wDatum; ?></small>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
