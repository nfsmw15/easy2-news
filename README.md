# easy2-news

News system extension for [easy2-php8](https://github.com/nfsmw15/Easy2-PHP8).

## About

A news management extension for the EASY 2.0 login system. Supports public and internal news, frontend display, admin management, and a homepage widget.

## Features

- Public and internal (login-required) news
- Frontend list and single view
- Admin: create, edit, delete, toggle active/inactive
- Homepage widget (latest news)

## Files

| File | Description |
|------|-------------|
| `templates/news/newsall.php` | Frontend news list |
| `templates/news/news.php` | Frontend single view |
| `templates/news/news_add.php` | Admin management |
| `templates/news/news_widget.php` | Homepage widget |
| `system/classes/newssystem.php` | Core class |

## Integration into EASY 2.0

Add to `system/classes.run.user.php`:
```php
$newssystem = new newssystem();
```

Add to `system/run.user.php`:
```php
if ($p == 'news_add' && $c == 'newsadd')   { $error = $newssystem->newsAdd(); }
if ($p == 'news_add' && $c == 'newsedit')  { $error = $newssystem->newsEdit(); }
if ($p == 'news_add' && $c == 'newsdelete'){ $error = $newssystem->newsDelete(); }
if ($p == 'news_add' && $c == 'newstoggle'){ $error = $newssystem->newsToggleActive(); }
```

Register pages `news`, `newsa`, `news_add` in the EASY 2.0 admin panel.

## License

Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
SPDX-License-Identifier: AGPL-3.0-or-later
