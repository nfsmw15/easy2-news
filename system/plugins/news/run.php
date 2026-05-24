<?php
declare(strict_types=1);
/***
 * easy2-news Plugin
 * File: system/plugins/news/run.php
 ***/

$newssystem = new newssystem();

if ($p == 'news_add' && $c == 'newsadd') {
    $error = $newssystem->newsAdd();
}
if ($p == 'news_add' && $c == 'newsedit') {
    $error = $newssystem->newsEdit();
}
if ($p == 'news_add' && $c == 'newsdelete') {
    $error = $newssystem->newsDelete();
}
if ($p == 'news_add' && $c == 'newstoggle') {
    $error = $newssystem->newsToggleActive();
}
if ($p == 'news_add' && $h == 'news_saved') {
    $success = 'Der Newseintrag wurde erfolgreich erstellt!';
}
if ($p == 'news_add' && $h == 'news_edited') {
    $success = 'Der Newseintrag wurde erfolgreich gespeichert!';
}
if ($p == 'news_add' && $h == 'news_deleted') {
    $success = 'Der Newseintrag wurde erfolgreich gelöscht!';
}
if ($p == 'news_add' && $h == 'news_status_changed') {
    $success = 'Der Status des Newseintrags wurde geändert!';
}
