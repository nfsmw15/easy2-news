<?php
/**
 * News-Widget für Startseite
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 * Zeigt die letzten 3 News als Panel an
 * Einbinden mit: <?php include('./templates/news_widget.php'); ?>
 */
$widgetNews = $newssystem->listNews(3, 0);
if (!empty($widgetNews)):
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 style="margin:0;">
            <i class="fa fa-newspaper-o"></i> Aktuelle News
            <a href="./?p=newsa" class="btn btn-xs btn-default pull-right">Alle News</a>
        </h4>
    </div>
    <div class="list-group" style="margin-bottom:0;">
        <?php foreach ($widgetNews as $wn):
            $wDatum = date('d.m.Y', strtotime($wn['datum']));
            $wBadge = '';
            if (isset($wn['visible']) && $wn['visible'] == 0) {
                $wBadge = '<span class="label label-warning" style="font-size:10px;"><i class="fa fa-lock"></i> Intern</span> ';
            }
        ?>
        <a href="./?p=news&n=<?php echo (int)$wn['id']; ?>" class="list-group-item">
            <?php echo $wBadge; ?>
            <strong><?php echo html_entity_decode($wn['ueberschrift'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <br>
            <small class="text-muted">
                <i class="fa fa-user"></i> <?php echo htmlspecialchars($wn['autor']); ?>
                &middot;
                <i class="fa fa-clock-o"></i> <?php echo $wDatum; ?>
            </small>
            <br>
            <span class="text-muted"><?php echo html_entity_decode(strip_tags(substr($wn['kurznews'], 0, 80)), ENT_QUOTES, 'UTF-8'); ?>…</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
