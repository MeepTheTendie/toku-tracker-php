<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series - Toku Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f0f12;
            color: #e0e0e5;
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #2a2a3e;
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        h1 {
            font-size: 1.5rem;
            background: linear-gradient(90deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        nav {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        nav a {
            color: #888;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        nav a:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: #fff;
        }
        
        .result-count {
            color: #888;
            font-size: 0.95rem;
        }
        
        /* Filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #1a1a24;
            border-radius: 12px;
            border: 1px solid #2a2a35;
        }
        
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }
        
        .filter-label {
            color: #666;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        
        .filter-btn {
            padding: 0.5rem 1rem;
            background: #252530;
            border: 1px solid #3a3a45;
            border-radius: 20px;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .filter-btn:hover {
            border-color: #4a9eff;
            color: #fff;
        }
        
        .filter-btn.active {
            background: #4a9eff;
            border-color: #4a9eff;
            color: #fff;
        }
        
        /* Series Grid */
        .series-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
        }
        
        .series-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #2a2a35;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
        }
        
        .series-card:hover {
            transform: translateY(-4px);
            border-color: #4a4a5e;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .series-card.completed {
            border-color: #00d4aa;
            background: linear-gradient(135deg, #1a1a24 0%, rgba(0,212,170,0.05) 100%);
        }
        
        .series-card.watching {
            border-color: #4a9eff;
            background: linear-gradient(135deg, #1a1a24 0%, rgba(74,158,255,0.05) 100%);
        }
        
        .series-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        
        .series-icon {
            font-size: 2rem;
        }
        
        .series-badges {
            display: flex;
            gap: 0.5rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge.completed {
            background: #00d4aa;
            color: #000;
        }
        
        .badge.watching {
            background: #4a9eff;
            color: #fff;
        }
        
        .badge.era {
            background: #252530;
            color: #888;
        }
        
        .series-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .series-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 0.75rem;
        }
        
        .series-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-bottom: 1rem;
        }
        
        .tag {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            background: #252530;
            border-radius: 4px;
            color: #666;
        }
        
        .series-progress {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .progress-bar {
            flex: 1;
            height: 6px;
            background: #2a2a35;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar .fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s;
        }
        
        .progress-bar .fill.completed {
            background: #00d4aa;
        }
        
        .progress-bar .fill.watching {
            background: #4a9eff;
        }
        
        .progress-bar .fill.unwatched {
            background: #666;
        }
        
        .progress-text {
            font-size: 0.85rem;
            color: #888;
            min-width: 60px;
            text-align: right;
        }
        
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #2a2a35;
        }
        
        .quick-btn {
            flex: 1;
            padding: 0.5rem;
            font-size: 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .quick-btn.watch {
            background: #00d4aa;
            color: #000;
        }
        
        .quick-btn.watch:hover {
            background: #00c49a;
        }
        
        .quick-btn.reset {
            background: #3a3a45;
            color: #888;
        }
        
        .quick-btn.reset:hover {
            background: #4a4a55;
            color: #fff;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
            background: #1a1a24;
            border-radius: 12px;
            border: 1px dashed #3a3a4e;
            grid-column: 1 / -1;
        }
        
        footer {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 0.85rem;
            border-top: 1px solid #2a2a35;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .series-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>⚡ Toku Tracker</h1>
            <nav>
                <a href="./">Dashboard</a>
                <a href="./series">All Series</a>
                <a href="./stats">Statistics</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="page-header">
            <div>
                <h2 class="page-title">
                    <?php if ($franchise): ?>
                        <?php echo htmlspecialchars(FRANCHISES[$franchise]['name'] ?? 'Series'); ?>
                    <?php elseif ($filter === 'completed'): ?>
                        Completed Series
                    <?php elseif ($filter === 'watching'): ?>
                        In Progress
                    <?php elseif ($filter === 'unwatched'): ?>
                        Not Started
                    <?php elseif ($era): ?>
                        <?php echo $era; ?> Era
                    <?php else: ?>
                        All Series
                    <?php endif; ?>
                </h2>
                <div class="result-count"><?php echo count($series); ?> series found</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <span class="filter-label">Franchise:</span>
                <a href="./series" class="filter-btn <?php echo !$franchise && !$filter && !$era ? 'active' : ''; ?>">All</a>
                <?php foreach (FRANCHISES as $key => $f): ?>
                <a href="./series?franchise=<?php echo urlencode($key); ?>" 
                   class="filter-btn <?php echo $franchise === $key ? 'active' : ''; ?>">
                    <?php echo $f['icon']; ?> <?php echo $f['name']; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Status:</span>
                <a href="./series?filter=watching<?php echo $franchise ? '&franchise='.urlencode($franchise) : ''; ?>" 
                   class="filter-btn <?php echo $filter === 'watching' ? 'active' : ''; ?>">▶ Watching</a>
                <a href="./series?filter=completed<?php echo $franchise ? '&franchise='.urlencode($franchise) : ''; ?>" 
                   class="filter-btn <?php echo $filter === 'completed' ? 'active' : ''; ?>">✓ Completed</a>
                <a href="./series?filter=unwatched<?php echo $franchise ? '&franchise='.urlencode($franchise) : ''; ?>" 
                   class="filter-btn <?php echo $filter === 'unwatched' ? 'active' : ''; ?>">○ Unwatched</a>
            </div>
        </div>
        
        <?php if (empty($series)): ?>
        <div class="empty-state">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📺</div>
            <h3>No series found</h3>
            <p>Try adjusting your filters or search criteria.</p>
        </div>
        <?php else: ?>
        
        <div class="series-grid">
            <?php foreach ($series as $s): ?>
            <a href="./series-detail?id=<?php echo $s['id']; ?>" class="series-card <?php echo $s['status']; ?>">
                <div class="series-header">
                    <span class="series-icon"><?php echo $s['franchise_icon']; ?></span>
                    <div class="series-badges">
                        <?php if ($s['status'] === 'completed'): ?>
                        <span class="badge completed">✓ Done</span>
                        <?php elseif ($s['status'] === 'watching'): ?>
                        <span class="badge watching">▶ <?php echo $s['progress']; ?>%</span>
                        <?php endif; ?>
                        <span class="badge era"><?php echo $s['era']; ?></span>
                    </div>
                </div>
                <div class="series-name"><?php echo htmlspecialchars($s['name']); ?></div>
                <div class="series-meta">
                    <span><?php echo $s['year']; ?></span>
                    <span><?php echo $s['episodes']; ?> episodes</span>
                </div>
                <?php if (!empty($s['tags'])): ?>
                <div class="series-tags">
                    <?php foreach (array_slice($s['tags'], 0, 4) as $tag): ?>
                    <span class="tag"><?php echo htmlspecialchars(str_replace('_', ' ', $tag)); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="series-progress">
                    <div class="progress-bar">
                        <div class="fill <?php echo $s['status']; ?>" style="width: <?php echo $s['progress']; ?>"></div>
                    </div>
                    <span class="progress-text"><?php echo $s['watched']; ?>/<?php echo $s['episodes']; ?></span>
                </div>
                
                <div class="quick-actions" onclick="event.preventDefault();">
                    <?php if ($s['status'] !== 'completed'): ?>
                    <button class="quick-btn watch" onclick="quickMarkAll(<?php echo $s['id']; ?>, 'watch')">
                        ✓ All
                    </button>
                    <?php else: ?>
                    <button class="quick-btn reset" onclick="quickMarkAll(<?php echo $s['id']; ?>, 'unwatch')">
                        ✕ Reset
                    </button>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </main>
    
    <footer>
        Toku Tracker PHP • <?php echo TOTAL_SERIES_COUNT; ?> series • <?php echo number_format(TOTAL_EPISODE_COUNT); ?> episodes
    </footer>
    
    <script>
        async function quickMarkAll(seriesId, action) {
            const actionText = action === 'watch' ? 'mark all as watched' : 'reset progress';
            if (!confirm(`Are you sure you want to ${actionText} for this series?`)) return;
            
            try {
                const response = await fetch('api/bulk', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        series_id: seriesId,
                        action: action === 'watch' ? 'watch_all' : 'unwatch_all'
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to update series');
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error updating series');
            }
        }
    </script>
</body>
</html>
