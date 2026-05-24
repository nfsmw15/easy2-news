# easy2-news

News system extension for [easy2-php8](https://github.com/nfsmw15/Easy2-PHP8).

## About

A news management extension for the EASY 2.0 login system (PHP 8 fork). Supports public and internal news, frontend display, admin management, and a homepage widget.

## Requirements

- [easy2-php8](https://github.com/nfsmw15/Easy2-PHP8)

## Installation

1. Copy all files into the easy2-php8 webroot (merge into existing structure)
2. Open `install.php` in a browser
3. The installer checks all prerequisites, creates the database table, registers pages and menu entries, then deletes itself

No manual database or menu setup required.

## Files

| File | Description |
|------|-------------|
| `install.php` | One-time browser installer (deletes itself after installation) |
| `install/sql/_news.sql` | Database schema — news table, site registrations, menu entries |
| `system/plugins/news/run.php` | Plugin-loader entry point (auto-included by easy2-php8) |
| `system/classes/newssystem.php` | Core class |
| `system/tpl/news/newsall.tpl` | Frontend list template |
| `system/tpl/news/news.tpl` | Frontend single view template |
| `templates/news/newsall.php` | Frontend news list page |
| `templates/news/news.php` | Frontend single view page |
| `templates/news/news_add.php` | Admin management page |
| `templates/news/news_widget.php` | Homepage widget |
| `js/summernote-init.js` | Summernote editor initialisation |

## Features

- Public and internal (login-required) news
- Frontend list with pagination and single view
- Admin: create, edit, delete, toggle active/inactive
- Homepage widget (latest news)
- WYSIWYG editor via Summernote (provided by easy2-php8)

## License

Copyright (C) 2026 Andreas P. <https://nfsmw15.de>  
SPDX-License-Identifier: AGPL-3.0-or-later
