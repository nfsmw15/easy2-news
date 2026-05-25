/**
 * easy2-news – Summernote Editor Init
 * Copyright (C) 2026 Andreas P. <https://nfsmw15.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
$(function () {
    if ($('#news').length) {
        $('#news').summernote({
            lang: 'de-DE',
            height: 400,
            toolbar: [
                ['style',    ['style']],
                ['font',     ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontsize', ['fontsize']],
                ['color',    ['color']],
                ['para',     ['ul', 'ol', 'paragraph']],
                ['table',    ['table']],
                ['insert',   ['link', 'picture', 'hr']],
                ['view',     ['fullscreen', 'codeview']]
            ]
        });
        $('form').on('submit', function (e) {
            var content = $('#news').summernote('code');
            if (!content || content === '<p><br></p>') {
                e.preventDefault();
                alert('Bitte einen Newstext eingeben.');
            }
        });
    }
});
