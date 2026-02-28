<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<style>
    :root {
        --sidebar-bg: #1e1e2d;
        --sidebar-hover: #2b2b40;
        --accent-color: #007bff; /* AdminLTE Primary Blue */
        --text-muted: #9ca3af;
        --border-color: #e5e7eb;
    }

    .notifications-card {
        display: flex;
        min-height: calc(100vh - 180px); /* Adjust for header/footer and padding */
        background-color: #fff;
        border-radius: 4px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        overflow: hidden;
    }

    /* Sidebar Styles */
    .n-sidebar {
        width: 250px;
        background-color: #f4f6f9; /* AdminLTE standard light gray */
        border-right: 1px solid #e5e7eb;
        color: #374151;
        padding: 1.5rem 0.5rem; /* Remove side padding so tabs can stretch */
        display: flex;
        flex-direction: column;
    }

    /* Filter Pills */
    .pill-filters {
        display: flex;
        background-color: #f1f5f9; /* Light gray container */
        border-radius: 8px;
        padding: 0.25rem;
        margin-bottom: 1.5rem;
        align-items: center;
        width: 100%;
        gap: 0.1rem; /* Tiny bit of space between them */
    }
    
    .filter-pill {
        flex: 1;
        text-align: center;
        color: #475569;
        padding: 0.4rem 0.1rem;
        border-radius: 6px;
        font-size: 0.7rem; /* Smaller font to fit */
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .filter-pill:hover {
        color: #0f172a;
        text-decoration: none;
        background-color: rgba(255,255,255,0.4);
    }
    
    .filter-pill.active {
        background-color: #ffffff; /* White active background */
        color: #0f172a;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* Subtle lift */
    }

    .new-badge {
        display: inline-block;
        background-color: #ffedd5; /* Light orange bg */
        color: #c2410c; /* Dark orange text */
        padding: 0.2rem 0.6rem;
        border-radius: 9999px; /* Fully rounded */
        font-weight: 800;
        font-size: 0.7rem; /* Smaller font */
        margin-bottom: 0.75rem;
        align-self: flex-end; /* Push to the right */
    }

    .nav-item-list {
        display: flex;
        align-items: center;
        padding: 0.6rem 2.5rem; /* Increased side padding for a wider tab */
        margin: 0.15rem 1rem; /* Added margin back so it doesn't touch the very edges of the container but is wide */
        color: #4b5563;
        text-decoration: none;
        border-radius: 50px; /* Fully rounded / pill shape */
        font-size: 0.85rem; /* Punguza font further */
        font-weight: 500;
        transition: all 0.2s;
    }

    .nav-item-list:hover {
        background-color: #e9ecef; /* Hover gray */
        color: #111827;
        text-decoration: none;
    }

    .nav-item-list.active {
        background-color: #2b3945; /* Dark slate background from image */
        color: #ffffff;
    }
    
    .nav-item-list i {
        width: 24px;
        font-size: 1.1rem;
        margin-right: 12px; /* Space between icon and text */
        text-align: center;
        color: #64748b; /* Slightly muted icon color like in screenshot */
    }
    
    .nav-item-list.active i {
        color: #ffffff; /* White icon when active */
    }

    .nav-item-list div {
        display: flex;
        align-items: center;
        flex: 1; /* Push badge to the right if exists */
    }

    .badge-count {
        background-color: #ef4444;
        color: white;
        font-size: 0.75rem;
        padding: 0.1rem 0.4rem;
        border-radius: 9999px;
        margin-left: auto; /* Push to right Edge of the shrunk container */
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
        background-color: #fefffe; 
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

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Notifications</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="notifications-card">
            <!-- Sidebar -->
            <div class="n-sidebar" style="align-items: center;">
                <?php 
                    $totalCount = count($notifications);
                    $unreadCount = count(array_filter($notifications, function($n) { return !$n->is_read; }));
                    $readCount = $totalCount - $unreadCount;
                    // Add starred count logic similarly if needed, for now hardcoding to 0
                    $starredCount = 0; 
                ?>
                <div class="new-badge">
                    <?= $unreadCount ?> new
                </div>
                <div class="pill-filters" style="width: 100%;">
                    <a href="#" class="filter-pill active" onclick="filterByPill('all', this); return false;">All (<?= $totalCount ?>)</a>
                    <a href="#" class="filter-pill" onclick="filterByPill('unread', this); return false;">Unread (<?= $unreadCount ?>)</a>
                    <a href="#" class="filter-pill" onclick="filterByPill('read', this); return false;">Read (<?= $readCount ?>)</a>
                    <a href="#" class="filter-pill" onclick="filterByPill('starred', this); return false;">Starred (<?= $starredCount ?>)</a>
                </div>
                
                <nav>
                    <a href="#" class="nav-item-list active">
                        <div><i class="fas fa-inbox"></i> Inbox</div>
                        <?php 
                            $unreadCount = count(array_filter($notifications, function($n) { return !$n->is_read; }));
                            if($unreadCount > 0): 
                        ?>
                            <span class="badge-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="#" class="nav-item-list">
                        <div><i class="fas fa-star"></i> Starred</div>
                        <span class="badge-count" style="background:#6b7280; display:none;">0</span>
                    </a>
                    <a href="#" class="nav-item-list">
                        <div><i class="far fa-envelope"></i> Unread</div>
                        <?php if($unreadCount > 0): ?>
                            <span class="badge-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="#" class="nav-item-list">
                        <div><i class="fas fa-paper-plane"></i> Sent</div>
                    </a>
                    <a href="#" class="nav-item-list">
                        <div><i class="fas fa-archive"></i> Archived</div>
                    </a>
                    <a href="#" class="nav-item-list">
                        <div><i class="fas fa-trash"></i> Trash</div>
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="n-content">
                <!-- Header -->
                <div class="n-header">
                    <div>
                        <h2>Inbox</h2>
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
                <div class="n-list" id="notifications-list-container">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="n-item <?= !$notif->is_read ? 'unread' : '' ?>" onclick="viewNotification('<?= $notif->id ?? '' ?>')">
                            <input type="checkbox" class="n-check" onclick="event.stopPropagation()">
                            <i class="far fa-star n-star" onclick="event.stopPropagation(); this.classList.toggle('fas'); this.classList.toggle('far'); this.classList.toggle('starred'); filterDynamic();"></i>
                            
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
                    
                    <div class="n-empty" id="n-empty-state" style="display: <?= empty($notifications) ? 'flex' : 'none' ?>;">
                        <i class="far fa-bell-slash"></i>
                        <p>No notifications found</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function viewNotification(id) {
        // Handle click action
    }
    
    const tabs = document.querySelectorAll('.nav-item-list');
    const pills = document.querySelectorAll('.filter-pill');
    const headerTitle = document.querySelector('.n-header h2');
    const emptyState = document.getElementById('n-empty-state');
    const nItems = document.querySelectorAll('.n-item');
    let currentFilter = 'inbox';

    // Pill Switching Logic
    function filterByPill(type, element) {
        // Update active pill styling
        pills.forEach(p => p.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }
        
        // Remove active class from nav tabs to avoid confusion
        tabs.forEach(t => t.classList.remove('active'));
        
        if (type === 'all') {
            currentFilter = 'inbox';
            headerTitle.innerText = 'All Notifications';
        } else if (type === 'read') {
            currentFilter = 'read';
            headerTitle.innerText = 'Read Notifications';
        } else {
            currentFilter = type;
            headerTitle.innerText = type.charAt(0).toUpperCase() + type.slice(1) + ' Notifications';
        }
        filterDynamic();
    }

    // Sidebar Tab Switching Logic
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active style from pills
            pills.forEach(p => p.classList.remove('active'));
            
            // Update active styling
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update title
            const tabName = tab.querySelector('div').innerText.trim();
            headerTitle.innerText = tabName;
            
            // Filter list
            currentFilter = tabName.toLowerCase();
            filterDynamic();
        });
    });

    function filterDynamic() {
        let visibleCount = 0;
        
        nItems.forEach(item => {
            let show = false;
            
            if (currentFilter === 'inbox') { show = true; } // Shows all
            else if (currentFilter === 'starred') { show = item.querySelector('.n-star.starred') !== null; }
            else if (currentFilter === 'unread') { show = item.classList.contains('unread'); }
            else if (currentFilter === 'read') { show = !item.classList.contains('unread'); }
            else if (['sent', 'archived', 'trash'].includes(currentFilter)) { show = false; }
            
            item.style.display = show ? 'flex' : 'none';
            if(show) visibleCount++;
        });
        
        // Search filter overrides
        const searchTerm = document.querySelector('.search-box input').value.toLowerCase();
        if (searchTerm) {
            visibleCount = 0;
            nItems.forEach(item => {
                if(item.style.display === 'flex') {
                    if(!item.innerText.toLowerCase().includes(searchTerm)) {
                        item.style.display = 'none';
                    } else {
                        visibleCount++;
                    }
                }
            });
        }
        
        if(emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
            if(visibleCount === 0) {
                 emptyState.querySelector('p').innerText = 'No ' + currentFilter + ' notifications found';
            }
        }
    }
    
    // Search input event
    document.querySelector('.search-box input').addEventListener('keyup', filterDynamic);
</script>

<?= $this->endSection(); ?>
