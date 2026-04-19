/**
 * Summernote Editor Init
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
$(function () {
    var toolbarFull = [
        ['style',    ['style']],
        ['font',     ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['fontsize', ['fontsize']],
        ['color',    ['color']],
        ['para',     ['ul', 'ol', 'paragraph']],
        ['table',    ['table']],
        ['insert',   ['link', 'picture', 'hr']],
        ['view',     ['fullscreen', 'codeview']]
    ];

    var toolbarBasic = [
        ['style',    ['style']],
        ['font',     ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['fontsize', ['fontsize']],
        ['color',    ['color']],
        ['para',     ['ul', 'ol', 'paragraph']],
        ['table',    ['table']],
        ['insert',   ['link', 'hr']],
        ['view',     ['fullscreen', 'codeview']]
    ];

    // News-Editor
    if ($('#news').length) {
        $('#news').summernote({ lang: 'de-DE', height: 400, toolbar: toolbarFull });
        $('form').on('submit', function (e) {
            var content = $('#news').summernote('code');
            if (!content || content === '<p><br></p>') {
                e.preventDefault();
                alert('Bitte einen Newstext eingeben.');
            }
        });
    }

    // Settings: Impressum & Datenschutz
    if ($('#impressum_content').length) {
        $('#impressum_content').summernote({ lang: 'de-DE', height: 300, toolbar: toolbarBasic });
    }
    if ($('#privacy_policy').length) {
        $('#privacy_policy').summernote({ lang: 'de-DE', height: 300, toolbar: toolbarBasic });
    }
});
