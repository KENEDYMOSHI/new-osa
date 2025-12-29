<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<style>
    :root {
        --sidebar-bg: #1e1e2d;
        --sidebar-hover: #2b2b40;
        --accent-color: #6366f1; /* Indigo/Purple */
        --text-muted: #9ca3af;
        --border-color: #e5e7eb;
    }

    .notifications-page {
        display: flex;
        height: calc(100vh - 57px); /* Adjust based on navbar height */
        background-color: #f3f4f6;
        overflow: hidden;
    }

    /* Sidebar Styles */
    .n-sidebar {
        width: 250px;
        background-color: var(--sidebar-bg);
        color: #ffffff;
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .compose-btn {
        background-color: var(--accent-color);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        width: 100%;
        text-align: center;
        margin-bottom: 2rem;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .compose-btn:hover {
        background-color: #4f46e5;
        color: white;
        text-decoration: none;
    }

    .nav-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        color: #9ca3af;
        text-decoration: none;
        border-radius: 6px;
        margin-bottom: 0.25rem;
        transition: all 0.2s;
    }

    .nav-item:hover {
        background-color: var(--sidebar-hover);
        color: white;
        text-decoration: none;
    }

    .nav-item.active {
        background-color: #3f3f5a; /* Slightly lighter than bg */
        color: white; /* Active text color */
        border-right: 3px solid var(--accent-color); /* Or background styling */
         /* Actually design shows full highlight usually, let's stick to simple background */
         background-color: var(--accent-color);
    }
    
    .nav-item i {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }

    .badge-count {
        background-color: #ef4444;
        color: white;
        font-size: 0.75rem;
        padding: 0.1rem 0.4rem;
        border-radius: 9999px;
    }

    /* Main Content Styles */
    .n-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: white;
        overflow: hidden;
    }

    .n-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .n-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .n-header p {
        color: #6b7280;
        margin: 0;
        font-size: 0.9rem;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .search-box i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .toolbar {
        padding: 0.75rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: #f9fafb;
    }

    .toolbar-btn {
        background: white;
        border: 1px solid var(--border-color);
        color: #6b7280;
        padding: 0.4rem 0.6rem;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .toolbar-btn:hover {
        background-color: #f3f4f6;
        color: #374151;
    }

    /* Notification List */
    .n-list {
        flex: 1;
        overflow-y: auto;
    }

    .n-item {
        display: flex;
        align-items: center;
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.1s;
        cursor: pointer;
    }

    .n-item:hover {
        background-color: #f9fafb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); /* Subtle lift */
    }

    .n-item.unread {
        background-color: #fff; /* or keep white? usually unread is highlighted */
        background-color: #fefffe; /* slightly distinct maybe? */
    }
    
    .n-item.unread .n-title {
        font-weight: 700;
        color: #111827;
    }
    
    .n-item.unread .n-preview {
         color: #374151;
         font-weight: 500;
    }

    .n-check {
        margin-right: 1rem;
    }

    .n-star {
        margin-right: 1rem;
        color: #d1d5db; /* gray */
        cursor: pointer;
    }

    .n-star.starred {
        color: #fbbf24; /* yellow */
    }

    .n-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: #4b5563;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .n-avatar.system { background-color: #dbeafe; color: #1e40af; }
    .n-avatar.alert { background-color: #fee2e2; color: #991b1b; }
    .n-avatar.success { background-color: #d1fae5; color: #065f46; }

    .n-details {
        flex: 1;
        min-width: 0; /* text-overflow fix */
    }

    .n-header-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
    }

    .n-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #4b5563;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-right: 0.5rem;
    }

    .n-date {
        font-size: 0.8rem;
        color: #9ca3af;
        white-space: nowrap;
    }

    .n-preview {
        font-size: 0.9rem;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .n-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #9ca3af;
    }
    .n-empty i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="notifications-page">
    <!-- Sidebar -->
    <div class="n-sidebar">
        <a href="#" class="compose-btn">
            <i class="fas fa-plus"></i> Compose New
        </a>
        
        <nav>
            <a href="#" class="nav-item active">
                <div><i class="fas fa-inbox"></i> Inbox</div>
                <?php 
                    $unreadCount = count(array_filter($notifications, function($n) { return !$n->is_read; }));
                    if($unreadCount > 0): 
                ?>
                    <span class="badge-count"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="nav-item">
                <div><i class="fas fa-star"></i> Starred</div>
                <span class="badge-count" style="background:#6b7280; display:none;">0</span>
            </a>
            <a href="#" class="nav-item">
                <div><i class="far fa-envelope"></i> Unread</div>
                <?php if($unreadCount > 0): ?>
                    <span class="badge-count"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="nav-item">
                <div><i class="fas fa-paper-plane"></i> Sent</div>
            </a>
            <a href="#" class="nav-item">
                <div><i class="fas fa-archive"></i> Archived</div>
            </a>
            <a href="#" class="nav-item">
                <div><i class="fas fa-trash"></i> Trash</div>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="n-content">
        <!-- Header -->
        <div class="n-header">
            <div>
                <h2>Notifications</h2>
                <p>Stay updated with your application status</p>
            </div>
            <div class="d-flex gap-2">
                 <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search notifications...">
                </div>
                <button class="toolbar-btn" onclick="location.reload()" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
                 <button class="toolbar-btn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <input type="checkbox" class="n-check" title="Select All">
            <button class="toolbar-btn" onclick="location.reload()">
                <i class="fas fa-redo-alt"></i>
            </button>
            <button class="toolbar-btn">
                <i class="fas fa-ellipsis-h"></i>
            </button>
        </div>

        <!-- Notifications List -->
        <div class="n-list">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="n-item <?= !$notif->is_read ? 'unread' : '' ?>" onclick="viewNotification('<?= $notif->id ?? '' ?>')">
                        <input type="checkbox" class="n-check" onclick="event.stopPropagation()">
                        <i class="far fa-star n-star" onclick="event.stopPropagation(); this.classList.toggle('fas'); this.classList.toggle('far'); this.classList.toggle('starred');"></i>
                        
                        <!-- Avatar based on type -->
                        <div class="n-avatar <?php 
                            if ($notif->type === 'document_returned') echo 'alert';
                            elseif ($notif->type === 'application_approved') echo 'success';
                            else echo 'system';
                        ?>">
                            <?php 
                                if ($notif->type === 'document_returned') echo '<i class="fas fa-exclamation"></i>';
                                elseif ($notif->type === 'application_approved') echo '<i class="fas fa-check"></i>';
                                else echo '<i class="fas fa-bell"></i>';
                            ?>
                        </div>

                        <div class="n-details">
                            <div class="n-header-row">
                                <span class="n-title">
                                    <?php 
                                        // Sender Name Simulation
                                        echo 'WMA System'; 
                                    ?>
                                </span>
                                <span class="n-date"><?= date('M d, Y', strtotime($notif->created_at)) ?></span>
                            </div>
                            <div class="n-header-row" style="margin:0;">
                                <div class="d-flex align-items-center" style="width: 100%; overflow:hidden;">
                                    <strong class="mr-2" style="white-space:nowrap;"><?= esc($notif->title) ?></strong> 
                                    <span class="n-preview">- <?= esc($notif->message) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="n-empty">
                    <i class="far fa-bell-slash"></i>
                    <p>No notifications found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function viewNotification(id) {
        // Handle click action - maybe expand or go to detail
        // For now, if related_entity_id exists logic from previous
        // We'll just alert standard behavior for design demo
        // console.log("Clicked notification " + id);
    }
    
    // Search filter
    document.querySelector('.search-box input').addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.n-item').forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(term) ? 'flex' : 'none';
        });
    });
</script>

<?= $this->endSection(); ?>
