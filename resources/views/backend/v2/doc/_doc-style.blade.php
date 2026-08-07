<style>
    .doc-wrap {}
    .doc-page-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .doc-page-sub {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 22px;
    }
    .doc-section {
        margin-bottom: 28px;
    }
    .doc-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .doc-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'SFMono-Regular', 'Menlo', 'Consolas', monospace;
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 700;
    }
    .doc-pill.post {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .doc-pill.json {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .doc-endpoint-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .doc-endpoint-row code {
        font-size: 13px;
        color: #0f172a;
        background: transparent;
        padding: 0;
    }
    .doc-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .doc-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .doc-table tbody td {
        padding: 12px 14px;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }
    .doc-table tbody tr:last-child td {
        border-bottom: none;
    }
    .doc-table .col-field {
        font-family: 'SFMono-Regular', 'Menlo', 'Consolas', monospace;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }
    .doc-table .col-type {
        font-family: 'SFMono-Regular', 'Menlo', 'Consolas', monospace;
        font-size: 12px;
        color: #7c3aed;
        white-space: nowrap;
    }
    .doc-table .col-required {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 700;
    }
    .col-required.is-required {
        background: #fee2e2;
        color: #b91c1c;
    }
    .col-required.is-optional {
        background: #f1f5f9;
        color: #64748b;
    }
    .doc-code {
        position: relative;
        margin: 0;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        font-family: 'SFMono-Regular', 'Menlo', 'Consolas', monospace;
        font-size: 13px;
        line-height: 1.6;
        overflow-x: auto;
    }
    .doc-code-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #1e293b;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 10px 16px;
        border-radius: 12px 12px 0 0;
        text-transform: uppercase;
    }
    .doc-code-header + .doc-code {
        border-radius: 0 0 12px 12px;
        margin-top: 0;
    }
    .doc-copy-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #e2e8f0;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .doc-copy-btn:hover { background: rgba(255, 255, 255, 0.15); }
    .doc-copy-btn.is-copied { color: #10b981; }
    .doc-note {
        padding: 14px 16px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #3b82f6;
        border-radius: 10px;
        font-size: 13.5px;
        color: #1e3a8a;
    }
    .doc-note.warning {
        background: #fffbeb;
        border-color: #fde68a;
        border-left-color: #f59e0b;
        color: #92400e;
    }
    .doc-note strong { color: inherit; }
    .doc-status-chip {
        display: inline-block;
        font-family: 'SFMono-Regular', 'Menlo', 'Consolas', monospace;
        font-size: 11.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        margin-right: 4px;
    }
    .doc-status-chip.success { background: #ecfdf5; color: #047857; }
    .doc-status-chip.pending { background: #fffbeb; color: #b45309; }
    .doc-status-chip.failed  { background: #fee2e2; color: #b91c1c; }

    /* Reset Bootstrap pre/code defaults inside doc */
    .doc-wrap pre, .doc-wrap code { color: inherit; }

    /* Responsive Styles for Mobile */
    @media (max-width: 768px) {
        .doc-table {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 576px) {
        .doc-page-title {
            font-size: 18px;
            line-height: 1.4;
        }
        .doc-page-sub {
            font-size: 13px;
            margin-bottom: 16px;
        }
        .doc-section-title {
            font-size: 13.5px;
            letter-spacing: 0.5px;
        }
        .doc-code-block {
            margin-bottom: 12px;
        }
        .doc-code-header {
            font-size: 11px;
            padding: 8px 12px;
        }
        .doc-code {
            padding: 12px 14px;
            font-size: 12px;
        }
        .doc-endpoint-row {
            padding: 10px 12px;
            gap: 8px;
        }
        .doc-endpoint-row code {
            font-size: 12px;
            word-break: break-all;
        }
    }
</style>

<script>
    function docCopy(btn) {
        var pre = btn.closest('.doc-code-block').querySelector('.doc-code');
        if (!pre) return;
        var text = pre.innerText;
        var done = function () {
            var icon = btn.querySelector('i');
            var label = btn.querySelector('span');
            btn.classList.add('is-copied');
            if (icon) icon.className = 'mdi mdi-check';
            if (label) label.textContent = 'Đã copy';
            setTimeout(function () {
                btn.classList.remove('is-copied');
                if (icon) icon.className = 'mdi mdi-content-copy';
                if (label) label.textContent = 'Copy';
            }, 1400);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () {
                docFallbackCopy(text, done);
            });
        } else {
            docFallbackCopy(text, done);
        }
    }
    function docFallbackCopy(text, onDone) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); onDone && onDone(); } catch (e) {}
        document.body.removeChild(ta);
    }
</script>
