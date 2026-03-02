<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<style>
    /* ── 3-Panel Email Client Layout ────────────────────────────────── */
    .email-client {
        display: flex;
        height: calc(100vh - 155px);
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        overflow: hidden;
        font-family: inherit;
    }

    /* ── Panel 1: Folder Sidebar ──────────────────────────────────── */
    .ec-sidebar {
        width: 180px;
        min-width: 180px;
        background: #f4f6f9;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        padding: 12px 0;
    }

    .ec-sidebar-header {
        padding: 4px 14px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #9ca3af;
    }

    .ec-folder {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 14px;
        font-size: 13.5px;
        color: #495057;
        cursor: pointer;
        border-radius: 0;
        text-decoration: none;
        transition: background .12s;
        gap: 8px;
    }
    .ec-folder:hover { background: #e9ecef; color: #212529; text-decoration: none; }
    .ec-folder.active { background: #2b3945; color: #fff; font-weight: 600; }
    .ec-folder.active i { color: #fff; }
    .ec-folder .folder-left { display: flex; align-items: center; gap: 8px; }
    .ec-folder i { width: 16px; text-align: center; color: #6c757d; font-size: 13px; }
    .folder-badge {
        background: #dc3545;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
        line-height: 1.4;
    }

    /* ── Panel 2: Message List ────────────────────────────────────── */
    .ec-list {
        width: 300px;
        min-width: 300px;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .ec-list-toolbar {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        gap: 6px;
    }

    .ec-list-toolbar input[type="text"] {
        flex: 1;
        padding: 5px 10px;
        font-size: 12.5px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fff;
        outline: none;
    }
    .ec-list-toolbar input[type="text"]:focus { border-color: #80bdff; }

    .ec-list-toolbar .tb-btn {
        border: 1px solid #ced4da;
        background: #fff;
        border-radius: 4px;
        padding: 5px 8px;
        cursor: pointer;
        color: #6c757d;
        font-size: 12px;
        transition: background .12s;
    }
    .ec-list-toolbar .tb-btn:hover { background: #e9ecef; }

    .ec-messages {
        flex: 1;
        overflow-y: auto;
    }

    .ec-msg {
        display: flex;
        align-items: flex-start;
        padding: 10px 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background .1s;
        gap: 10px;
        position: relative;
    }
    .ec-msg:hover { background: #f8f9fa; }
    .ec-msg.active { background: #e8f0fe; }
    .ec-msg.unread { background: #fafeff; }
    .ec-msg.unread .msg-subject { font-weight: 700; color: #111; }
    .ec-msg.unread .msg-sender  { font-weight: 700; color: #333; }

    .msg-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
        margin-top: 2px;
    }
    .msg-avatar.reupload { background: #fee2e2; color: #991b1b; }
    .msg-avatar.approved { background: #d1fae5; color: #065f46; }
    .msg-avatar.system   { background: #dbeafe; color: #1e40af; }

    .msg-body { flex: 1; min-width: 0; }
    .msg-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 2px;
    }
    .msg-sender { font-size: 13px; color: #495057; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
    .msg-date   { font-size: 11px; color: #9ca3af; white-space: nowrap; margin-left: 4px; }
    .msg-subject { font-size: 12.5px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
    .msg-preview { font-size: 11.5px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .unread-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6;
        flex-shrink: 0;
        margin-top: 14px;
    }

    /* ── Panel 3: Preview ─────────────────────────────────────────── */
    .ec-preview {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
        overflow: hidden;
    }

    .ec-preview-toolbar {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        gap: 8px;
    }

    .preview-tb-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        border: 1px solid #ced4da;
        background: #fff;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
        color: #495057;
        transition: background .12s;
        text-decoration: none;
    }
    .preview-tb-btn:hover { background: #e9ecef; color: #212529; text-decoration: none; }

    .ec-preview-content {
        flex: 1;
        overflow-y: auto;
        padding: 24px 28px;
    }

    .preview-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #9ca3af;
        gap: 12px;
    }
    .preview-empty i { font-size: 3rem; opacity: .4; }
    .preview-empty p { font-size: 14px; margin: 0; }

    .preview-subject {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        line-height: 1.3;
    }

    .preview-meta {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-top: 1px solid #f3f4f6;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 20px;
    }

    .meta-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .meta-details { flex: 1; }
    .meta-from    { font-size: 13.5px; font-weight: 600; color: #111827; }
    .meta-to      { font-size: 12px; color: #6b7280; }
    .meta-date    { font-size: 12px; color: #9ca3af; white-space: nowrap; }

    .preview-body {
        font-size: 14px;
        line-height: 1.65;
        color: #374151;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .preview-body strong { color: #111827; }

    /* ── Scrollbar thin ───────────────────────────────────────────── */
    .ec-messages::-webkit-scrollbar,
    .ec-preview-content::-webkit-scrollbar { width: 5px; }
    .ec-messages::-webkit-scrollbar-thumb,
    .ec-preview-content::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }
</style>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Notifications</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php
            $totalCount  = count($notifications);
            $unreadCount = count(array_filter($notifications, fn($n) => !$n->is_read));
        ?>
        <div class="email-client">

            <!-- ══ Panel 1: Folders ════════════════════════════════ -->
            <div class="ec-sidebar">
                <div class="ec-sidebar-header">Folders</div>

                <a href="#" class="ec-folder active" onclick="applyFilter('all',this);return false;">
                    <span class="folder-left"><i class="fas fa-inbox"></i> Inbox</span>
                    <?php if($unreadCount > 0): ?>
                        <span class="folder-badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <a href="#" class="ec-folder" onclick="applyFilter('unread',this);return false;">
                    <span class="folder-left"><i class="far fa-envelope"></i> Unread</span>
                    <?php if($unreadCount > 0): ?>
                        <span class="folder-badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <a href="#" class="ec-folder" onclick="applyFilter('read',this);return false;">
                    <span class="folder-left"><i class="fas fa-envelope-open"></i> Read</span>
                </a>

                <a href="#" class="ec-folder" onclick="applyFilter('starred',this);return false;">
                    <span class="folder-left"><i class="fas fa-star"></i> Starred</span>
                </a>

                <hr style="margin:8px 14px;border-color:#dee2e6;">

                <?php if($unreadCount > 0): ?>
                <a href="<?= base_url('wmaMarkAllNotificationsRead') ?>"
                   class="ec-folder"
                   onclick="return confirm('Mark all as read?')">
                    <span class="folder-left"><i class="fas fa-check-double"></i> Mark all read</span>
                </a>
                <?php endif; ?>

                <a href="#" class="ec-folder" onclick="location.reload();return false;">
                    <span class="folder-left"><i class="fas fa-sync-alt"></i> Refresh</span>
                </a>
            </div>

            <!-- ══ Panel 2: Message List ══════════════════════════════ -->
            <div class="ec-list">
                <!-- Toolbar -->
                <div class="ec-list-toolbar">
                    <input type="text" id="search-input" placeholder="Search...">
                    <button class="tb-btn" onclick="location.reload()" title="Refresh"><i class="fas fa-redo-alt"></i></button>
                </div>

                <!-- Messages -->
                <div class="ec-messages" id="messages-panel">
                    <?php foreach($notifications as $notif): ?>
                        <?php
                            $type = $notif->type ?? 'system';
                            if ($type === 'document_reuploaded') {
                                $avatarClass = 'reupload';
                                $icon = '<i class="fas fa-upload"></i>';
                                // Try bold format first: <strong>Name</strong>
                                $senderLabel = '';
                                if (preg_match('/<strong>([^<]+)<\/strong>/', $notif->message, $m)) {
                                    $senderLabel = strtolower($m[1]);
                                }
                                // Fallback: plain text "mwombaji NAME kutoka"
                                if (empty($senderLabel) && preg_match('/mwombaji\s+([^\n,]+?)\s+kutoka/i', strip_tags($notif->message), $m2)) {
                                    $senderLabel = strtolower(trim($m2[1]));
                                }
                                if (empty($senderLabel)) $senderLabel = 'wma system';
                            } elseif ($type === 'application_approved') {
                                $avatarClass = 'approved';
                                $icon = '<i class="fas fa-check"></i>';
                                $senderLabel = 'WMA Approval';
                            } else {
                                $avatarClass = 'system';
                                $icon = '<i class="fas fa-bell"></i>';
                                $senderLabel = 'WMA System';
                            }
                            $preview = strip_tags(mb_substr($notif->message, 0, 80));
                        ?>
                        <div class="ec-msg <?= !$notif->is_read ? 'unread' : '' ?>"
                             data-id="<?= $notif->id ?>"
                             data-title="<?= esc($notif->title) ?>"
                             data-message="<?= esc($notif->message) ?>"
                             data-date="<?= date('D, d M Y H:i', strtotime($notif->created_at)) ?>"
                             data-type="<?= esc($type) ?>"
                             data-sender="<?= esc($senderLabel) ?>"
                             data-starred="0"
                             onclick="openMessage(this)">

                            <div class="msg-avatar <?= $avatarClass ?>"><?= $icon ?></div>

                            <div class="msg-body">
                                <div class="msg-top">
                                    <span class="msg-sender" style="text-transform: lowercase; font-size: 12px; font-weight: 600; color: #374151;"><?= esc($senderLabel) ?></span>
                                    <span class="msg-date"><?= date('M d', strtotime($notif->created_at)) ?></span>
                                </div>
                                <div class="msg-subject"><?= esc($notif->title) ?></div>
                                <div class="msg-preview"><?= esc(strip_tags(mb_substr($notif->message, 0, 80))) ?>…</div>
                            </div>

                            <?php if(!$notif->is_read): ?>
                                <div class="unread-dot"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if(empty($notifications)): ?>
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;color:#9ca3af;">
                            <i class="far fa-bell-slash" style="font-size:2rem;margin-bottom:8px;opacity:.4;"></i>
                            <p style="font-size:13px;margin:0;">No notifications</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ══ Panel 3: Preview ═══════════════════════════════════ -->
            <div class="ec-preview">
                <!-- Preview Toolbar -->
                <div class="ec-preview-toolbar">
                    <button class="preview-tb-btn" id="btn-mark-read" style="display:none;" onclick="markCurrentRead()">
                        <i class="fas fa-envelope-open"></i> Mark as read
                    </button>
                    <button class="preview-tb-btn" id="btn-star" style="display:none;" onclick="toggleCurrentStar()">
                        <i class="far fa-star"></i> Star
                    </button>
                    <span style="font-size:12px;color:#9ca3af;margin-left:auto;" id="preview-count">
                        <?= $totalCount ?> notification<?= $totalCount !== 1 ? 's' : '' ?>,
                        <?= $unreadCount ?> unread
                    </span>
                </div>

                <!-- Preview Content -->
                <div class="ec-preview-content" id="preview-panel">
                    <div class="preview-empty" id="preview-empty">
                        <i class="fas fa-envelope-open-text"></i>
                        <p>Select a notification to read</p>
                    </div>

                    <div id="preview-body" style="display:none;">
                        <div class="preview-subject" id="pv-subject"></div>

                        <div class="preview-meta">
                            <div class="meta-avatar" id="pv-avatar"><i class="fas fa-bell"></i></div>
                            <div class="meta-details">
                                <div class="meta-from" id="pv-from">WMA System</div>
                                <div class="meta-to">To: <strong><?= esc(auth()->user()->username ?? 'Officer') ?></strong></div>
                            </div>
                            <div class="meta-date" id="pv-date"></div>
                        </div>

                        <div class="preview-body" id="pv-message"></div>
                    </div>
                </div>
            </div>

        </div><!-- /email-client -->
    </div>
</section>

<script>
var BASE_URL     = '<?= rtrim(base_url(), '/') ?>';
var currentId    = null;
var currentFilter = 'all';

// ── Open a message in preview panel ─────────────────────────────
function openMessage(el) {
    // Deselect previous
    document.querySelectorAll('.ec-msg').forEach(m => m.classList.remove('active'));
    el.classList.add('active');

    var id      = el.dataset.id;
    var title   = el.dataset.title;
    var message = el.dataset.message;  // may contain HTML bold tags
    var date    = el.dataset.date;
    var sender  = el.dataset.sender;
    var type    = el.dataset.type;

    currentId = id;

    // Set preview content
    document.getElementById('pv-subject').textContent = title;
    document.getElementById('pv-message').innerHTML   = message;  // render HTML bold tags
    document.getElementById('pv-date').textContent    = date;
    document.getElementById('pv-from').textContent    = sender;

    // Avatar icon by type
    var avatarEl = document.getElementById('pv-avatar');
    avatarEl.className = 'meta-avatar';
    avatarEl.innerHTML = '<i class="fas fa-bell"></i>';
    if (type === 'document_reuploaded') {
        avatarEl.classList.add('reupload');
        avatarEl.innerHTML = '<i class="fas fa-upload"></i>';
    } else if (type === 'application_approved') {
        avatarEl.classList.add('approved');
        avatarEl.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        avatarEl.classList.add('system');
    }

    // Show/hide toolbar buttons
    document.getElementById('btn-mark-read').style.display = el.classList.contains('unread') ? '' : 'none';
    document.getElementById('btn-star').style.display = '';

    // Show preview, hide empty state
    document.getElementById('preview-empty').style.display = 'none';
    document.getElementById('preview-body').style.display  = '';

    // Mark as read if unread
    if (el.classList.contains('unread')) {
        fetch(BASE_URL + '/wmaMarkNotificationRead/' + id, { method: 'GET' })
            .then(() => {
                el.classList.remove('unread');
                // Remove blue dot
                var dot = el.querySelector('.unread-dot');
                if (dot) dot.remove();
                // Update toolbar button
                document.getElementById('btn-mark-read').style.display = 'none';
                refreshSidebarCounts();
            });
    }
}

// ── Mark current open message as read manually ───────────────────
function markCurrentRead() {
    if (!currentId) return;
    var el = document.querySelector('.ec-msg[data-id="' + currentId + '"]');
    if (el) {
        fetch(BASE_URL + '/wmaMarkNotificationRead/' + currentId, { method: 'GET' })
            .then(() => {
                el.classList.remove('unread');
                var dot = el.querySelector('.unread-dot');
                if (dot) dot.remove();
                document.getElementById('btn-mark-read').style.display = 'none';
                refreshSidebarCounts();
            });
    }
}

// ── Toggle star ──────────────────────────────────────────────────
function toggleCurrentStar() {
    if (!currentId) return;
    var el    = document.querySelector('.ec-msg[data-id="' + currentId + '"]');
    var btn   = document.getElementById('btn-star');
    var icon  = btn.querySelector('i');
    if (el.dataset.starred === '1') {
        el.dataset.starred = '0';
        icon.className = 'far fa-star';
        btn.innerHTML  = btn.innerHTML.replace('Unstar', 'Star');
    } else {
        el.dataset.starred = '1';
        icon.className = 'fas fa-star';
        btn.innerHTML  = '<i class="fas fa-star"></i> Unstar';
    }
    filterMessages();
}

// ── Folder filter ────────────────────────────────────────────────
function applyFilter(type, clickedEl) {
    currentFilter = type;
    document.querySelectorAll('.ec-folder').forEach(f => f.classList.remove('active'));
    if (clickedEl) clickedEl.classList.add('active');
    filterMessages();
}

function filterMessages() {
    var search  = (document.getElementById('search-input').value || '').toLowerCase();
    var msgs    = document.querySelectorAll('.ec-msg');
    var visible = 0;

    msgs.forEach(function(m) {
        var show = false;
        if (currentFilter === 'all')     show = true;
        if (currentFilter === 'unread')  show = m.classList.contains('unread');
        if (currentFilter === 'read')    show = !m.classList.contains('unread');
        if (currentFilter === 'starred') show = m.dataset.starred === '1';

        if (show && search) {
            show = (m.dataset.title + ' ' + m.dataset.message + ' ' + m.dataset.sender)
                      .toLowerCase().includes(search);
        }
        m.style.display = show ? '' : 'none';
        if (show) visible++;
    });
}

// ── Update sidebar unread badge ──────────────────────────────────
function refreshSidebarCounts() {
    var unread = document.querySelectorAll('.ec-msg.unread').length;
    document.querySelectorAll('.folder-badge').forEach(b => {
        if (unread > 0) { b.textContent = unread; b.style.display = ''; }
        else            { b.style.display = 'none'; }
    });
    document.getElementById('preview-count').textContent =
        document.querySelectorAll('.ec-msg').length + ' notifications, ' + unread + ' unread';
}

// ── Search ───────────────────────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterMessages);
</script>

<?= $this->endSection(); ?>
