<?php
/**
 * News-Übersicht (Frontend)
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 *
 * p=newsa
 * Gäste sehen nur externe News, Mitglieder auch interne
 */
?>
<div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3"><i class="fa fa-newspaper-o"></i> News</h1>

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active">News</li>
    </ol>

    <div class="row">
        <!-- News-Hauptspalte -->
        <div class="col-lg-12">
            <?php
            $newsListe = $newssystem->listNews(20, 0);
            if (empty($newsListe)):
            ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Aktuell sind keine News vorhanden.
                </div>
            <?php else: foreach ($newsListe as $row):
                $datum = date('d.m.Y', strtotime($row['datum']));
                $badge = '';
                if (isset($row['visible'])) {
                    $badge = ($row['visible'] == 1)
                        ? '<span class="label label-primary"><i class="fa fa-globe"></i> Extern</span> '
                        : '<span class="label label-warning"><i class="fa fa-lock"></i> Intern</span> ';
                }
            ?>
                <article style="margin-bottom:30px;">
                    <?php if ($badge): ?><div style="margin-bottom:4px;"><?php echo $badge; ?></div><?php endif; ?>
                    <h2 style="margin-bottom:5px;">
                        <a href="./?p=news&n=<?php echo (int)$row['id']; ?>">
                            <?php echo html_entity_decode($row['ueberschrift'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h2>
                    <p class="text-muted" style="margin-bottom:8px;">
                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($row['autor']); ?>
                        &nbsp;&middot;&nbsp;
                        <i class="fa fa-clock-o"></i> <?php echo $datum; ?> um <?php echo substr($row['zeit'], 0, 5); ?> Uhr
                    </p>
                    <p><?php echo html_entity_decode(strip_tags($row['kurznews']), ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="btn btn-primary btn-sm" href="./?p=news&n=<?php echo (int)$row['id']; ?>">
                        Mehr lesen <i class="fa fa-angle-right"></i>
                    </a>
                    <hr>
                </article>
            <?php endforeach; endif; ?>
        </div>


    </div>

</div>
