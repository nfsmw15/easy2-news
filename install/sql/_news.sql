SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_news` (
  `id` bigint(16) NOT NULL,
  `datum` date DEFAULT NULL COMMENT 'Datum der News',
  `zeit` time DEFAULT NULL COMMENT 'Uhrzeit der News',
  `ueberschrift` varchar(128) DEFAULT NULL COMMENT 'Überschrift',
  `kurznews` text DEFAULT NULL COMMENT 'Kurztext (max. 9999 Zeichen)',
  `news` longtext DEFAULT NULL COMMENT 'Volltext (HTML, Summernote)',
  `autor` varchar(64) DEFAULT NULL COMMENT 'Autor',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = inaktiv, 1 = aktiv',
  `visible` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = intern, 1 = extern (öffentlich)',
  `news_email_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = nicht gesendet, 1 = E-Mail gesendet'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `[prefix]_news`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_news`
  MODIFY `id` bigint(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
