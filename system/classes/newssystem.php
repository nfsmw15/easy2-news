<?php
/**
 * News System – EASY 2.0 Extension
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
class newssystem extends loginsystem {
    protected \MysqliPDOWrapper $mysql;

    public function __construct() {
        parent::__construct();
    }

    /**
     * News hinzufügen
     * visible: 0 = intern, 1 = extern (öffentlich)
     */
    public function newsAdd() {
        $error = '';

        $ueberschrift = length(isset($_POST['ueberschrift']) ? $_POST['ueberschrift'] : NULL, 128);
        $kurznews     = length(isset($_POST['kurznews'])     ? $_POST['kurznews']     : NULL, 9999);
        $news         = isset($_POST['news']) ? $_POST['news'] : '';
        $autor        = length(isset($_POST['autor'])        ? $_POST['autor']        : NULL, 64);
        $visible      = (isset($_POST['visible']) && $_POST['visible'] == '1') ? 1 : 0;

        if (self::auditRight('newsadd')) {
            if (!empty($ueberschrift) && !empty($kurznews) && !empty($news) && !empty($autor)) {
                $ueberschrift_esc = $this->mysql->real_escape_string($ueberschrift);
                $kurznews_esc     = $this->mysql->real_escape_string($kurznews);
                $news_esc         = $this->mysql->real_escape_string($news);
                $autor_esc        = $this->mysql->real_escape_string($autor);

                // Prüfen ob neue Spalten existieren (Migration ausgeführt?)
                $colCheck = $this->mysql->query("SHOW COLUMNS FROM " . Prefix . "_news LIKE 'visible'");
                $hasNewCols = ($colCheck && $colCheck->num_rows > 0);

                if ($hasNewCols) {
                    $sql = "INSERT INTO " . Prefix . "_news 
                            (datum, ueberschrift, kurznews, news, autor, active, zeit, visible, news_email_sent) 
                            VALUES (CURRENT_DATE(), '$ueberschrift_esc', '$kurznews_esc', '$news_esc', '$autor_esc', '1', CURRENT_TIME(), '$visible', '0')";
                } else {
                    $sql = "INSERT INTO " . Prefix . "_news 
                            (datum, ueberschrift, kurznews, news, autor, active, zeit) 
                            VALUES (CURRENT_DATE(), '$ueberschrift_esc', '$kurznews_esc', '$news_esc', '$autor_esc', '1', CURRENT_TIME())";
                }

                $result = $this->mysql->query($sql);

                if ($result === true) {
                    $newId = $this->mysql->insert_id;
                    if ($visible == 0) {
                        $this->sendNewsEmail($newId, $ueberschrift, $kurznews, $autor);
                    }
                    header('Location: ?p=news_add&h=news_saved');
                    exit();
                } else {
                    $error = 'Fehler beim Speichern der News!';
                    errormail('Fehler in newssystem::newsAdd(). MySQL: ' . $this->mysql->errno . ': ' . $this->mysql->error);
                }
            } else {
                $error = 'Es m&uuml;ssen alle Felder ausgef&uuml;llt werden!';
            }
        } else {
            $error = 'Sie haben keine Berechtigung!';
        }
        return $error;
    }

    public function newsEdit() {
        $error = '';

        $id           = intval(isset($_POST['news_id']) ? $_POST['news_id'] : 0);
        $ueberschrift = length(isset($_POST['ueberschrift']) ? $_POST['ueberschrift'] : NULL, 128);
        $kurznews     = length(isset($_POST['kurznews'])     ? $_POST['kurznews']     : NULL, 9999);
        $news         = isset($_POST['news']) ? $_POST['news'] : '';
        $autor        = length(isset($_POST['autor'])        ? $_POST['autor']        : NULL, 64);
        $visible      = (isset($_POST['visible']) && $_POST['visible'] == '1') ? 1 : 0;

        if (self::auditRight('newsadd')) {
            if ($id > 0 && !empty($ueberschrift) && !empty($kurznews) && !empty($news) && !empty($autor)) {
                $ueberschrift_esc = $this->mysql->real_escape_string($ueberschrift);
                $kurznews_esc     = $this->mysql->real_escape_string($kurznews);
                $news_esc         = $this->mysql->real_escape_string($news);
                $autor_esc        = $this->mysql->real_escape_string($autor);

                $oldRow = $this->mysql->query("SELECT visible, news_email_sent FROM " . Prefix . "_news WHERE id = '$id'");
                $old    = $oldRow ? $oldRow->fetch_assoc() : null;

                $colCheck2 = $this->mysql->query("SHOW COLUMNS FROM " . Prefix . "_news LIKE 'visible'");
                $hasNewCols2 = ($colCheck2 && $colCheck2->num_rows > 0);

                if ($hasNewCols2) {
                    $sql = "UPDATE " . Prefix . "_news SET
                            ueberschrift = '$ueberschrift_esc',
                            kurznews     = '$kurznews_esc',
                            news         = '$news_esc',
                            autor        = '$autor_esc',
                            visible      = '$visible'
                            WHERE id = '$id'";
                } else {
                    $sql = "UPDATE " . Prefix . "_news SET
                            ueberschrift = '$ueberschrift_esc',
                            kurznews     = '$kurznews_esc',
                            news         = '$news_esc',
                            autor        = '$autor_esc'
                            WHERE id = '$id'";
                }

                $result = $this->mysql->query($sql);

                if ($result === true) {
                    if ($visible == 0 && $old && $old['news_email_sent'] == 0) {
                        $this->sendNewsEmail($id, $ueberschrift, $kurznews, $autor);
                    }
                    header('Location: ?p=news_add&h=news_edited');
                    exit();
                } else {
                    $error = 'Fehler beim Speichern!';
                }
            } else {
                $error = 'Alle Felder ausf&uuml;llen!';
            }
        } else {
            $error = 'Keine Berechtigung!';
        }
        return $error;
    }

    public function newsDelete() {
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
        if (self::auditRight('newsadd') && $id > 0) {
            $this->mysql->query("DELETE FROM " . Prefix . "_news WHERE id = '$id'");
            header('Location: ?p=news_add&h=news_deleted');
            exit();
        }
        return 'Fehler beim L&ouml;schen!';
    }

    public function newsToggleActive() {
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
        if (self::auditRight('newsadd') && $id > 0) {
            $row = $this->mysql->query("SELECT active FROM " . Prefix . "_news WHERE id = '$id'");
            if ($row) {
                $data      = $row->fetch_assoc();
                $newActive = ($data['active'] == 1) ? 0 : 1;
                $this->mysql->query("UPDATE " . Prefix . "_news SET active = '$newActive' WHERE id = '$id'");
            }
            header('Location: ?p=news_add&h=news_status_changed');
            exit();
        }
        return 'Keine Berechtigung!';
    }

    public function getNews($id) {
        $id  = intval($id);
        $sql = "SELECT * FROM " . Prefix . "_news WHERE id = '$id' AND active = '1'";
        if (!self::login_session()) {
            $sql .= " AND visible = '1'";
        }
        $result = $this->mysql->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    public function listNews($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM " . Prefix . "_news WHERE active = '1'";
        if (!self::login_session()) {
            $sql .= " AND visible = '1'";
        }
        $sql .= " ORDER BY datum DESC, zeit DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        $result = $this->mysql->query($sql);
        $rows   = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function countNews() {
        $sql = "SELECT COUNT(*) as cnt FROM " . Prefix . "_news WHERE active = '1'";
        if (!self::login_session()) {
            $sql .= " AND visible = '1'";
        }
        $result = $this->mysql->query($sql);
        $row    = $result ? $result->fetch_assoc() : ['cnt' => 0];
        return intval($row['cnt']);
    }

    public function listAllNewsAdmin() {
        $result = $this->mysql->query("SELECT * FROM " . Prefix . "_news ORDER BY datum DESC, zeit DESC");
        $rows   = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function getNewsForEdit($id) {
        $id     = intval($id);
        $result = $this->mysql->query("SELECT * FROM " . Prefix . "_news WHERE id = '$id'");
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    private function sendNewsEmail($newsId, $ueberschrift, $kurznews, $autor) {
        // Vereins-Sammeladresse aus den Einstellungen holen
        // Fallback auf administrator_mail falls news_group_mail nicht gesetzt
        try {
            $vereinsmail = $this->getMainData('news_group_mail') ?? '';
        } catch (\Exception $e) {
            $vereinsmail = '';
        }
        if (empty($vereinsmail)) {
            $vereinsmail = $this->getMainData('administrator_mail') ?? '';
        }
        if (!empty($vereinsmail) && filter_var($vereinsmail, FILTER_VALIDATE_EMAIL)) {
            $data = [
                'autor'        => htmlspecialchars($autor),
                'ueberschrift' => htmlspecialchars($ueberschrift),
                'kurznews'     => htmlspecialchars($kurznews),
                'newslink'     => getCurrentUrl() . '?p=news&n=' . intval($newsId),
            ];
            $this->sendMail('news.html', NULL, $data, $vereinsmail);
            $this->mysql->query("UPDATE " . Prefix . "_news SET news_email_sent = '1' WHERE id = '" . intval($newsId) . "'");
        }
    }
}
?>
