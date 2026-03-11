<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($series['name']); ?> - Toku Tracker</title>
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
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            font-size: 1.5rem;
            background: linear-gradient(90deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        nav a {
            color: #888;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        nav a:hover {
            color: #fff;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Series Header */
        .series-header-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #2a2a35;
            margin-bottom: 2rem;
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .series-icon-large {
            font-size: 4rem;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #252530;
            border-radius: 20px;
        }
        
        .series-info {
            flex: 1;
        }
        
        .series-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .series-meta-row {
            display: flex;
            gap: 1rem;
            color: #888;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .series-meta-row span {
            background: #252530;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .series-progress-large {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .progress-bar-large {
            flex: 1;
            max-width: 400px;
            height: 10px;
            background: #2a2a35;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-bar-large .fill {
            height: 100%;
            background: linear-gradient(90deg, #4a9eff, #00d4aa);
            border-radius: 5px;
            transition: width 0.3s;
        }
        
        .progress-text-large {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
        }
        
        .watch-btn {
            background: #4a9eff;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s;
        }
        
        .watch-btn:hover {
            background: #3a8eef;
        }
        
        /* Episodes Grid */
        .section-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .episodes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
        }
        
        .episode-card {
            aspect-ratio: 1;
            background: #1a1a24;
            border: 2px solid #2a2a35;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            color: #888;
            text-decoration: none;
        }
        
        .episode-card:hover {
            border-color: #4a9eff;
            color: #fff;
        }
        
        .episode-card.watched {
            background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
            border-color: #00d4aa;
            color: #fff;
        }
        
        .episode-card.current {
            border-color: #4a9eff;
            color: #4a9eff;
            box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.2);
        }
        
        .episode-card.watched::after {
            content: '✓';
            position: absolute;
            font-size: 0.7rem;
            top: 4px;
            right: 6px;
        }
        
        .episode-card {
            position: relative;
        }
        
        footer {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 0.85rem;
            border-top: 1px solid #2a2a35;
            margin-top: 2rem;
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
            </nav>
        </div>
    </header>
    
    <main class="container">
        <!-- Series Header -->
        <div class="series-header-card">
            <div class="series-icon-large">
                <?php echo $series['franchise_data']['icon'] ?? '📺'; ?>
            </div>
            <div class="series-info">
                <h2 class="series-title"><?php echo htmlspecialchars($series['name']); ?></h2>
                <div class="series-meta-row">
                    <span><?php echo $series['franchise_data']['name'] ?? 'Unknown'; ?></span>
                    <span><?php echo $series['era']; ?> Era</span>
                    <span><?php echo $series['year']; ?></span>
                    <span><?php echo $series['episodes']; ?> Episodes</span>
                </div>
                <div class="series-progress-large">
                    <div class="progress-bar-large">
                        <div class="fill" style="width: <?php echo $series['progress']; ?>%"></div>
                    </div>
                    <span class="progress-text-large">
                        <?php echo $series['watched']; ?>/<?php echo $series['episodes']; ?>
                    </span>
                </div>
            </div>
            <?php if ($series['watched'] < $series['episodes']): 
                $nextEp = TokuTracker::getNextEpisodeNumber($series['id']);
            ?>
            <a href="./watch?id=<?php echo $series['id']; ?>&episode=<?php echo $nextEp; ?>" class="watch-btn">
                ▶ Continue EP <?php echo $nextEp; ?>
            </a>
            <?php else: ?>
            <span style="color: #00d4aa; font-weight: 600;">✓ Complete</span>
            <?php endif; ?>
            
            <?php if ($series['watched'] < $series['episodes']): ?>
            <button class="watch-btn" style="background: #00d4aa; margin-left: 0.5rem;" onclick="markAllWatched()">
                ✓ Mark All
            </button>
            <?php else: ?>
            <button class="watch-btn" style="background: #666; margin-left: 0.5rem;" onclick="markAllUnwatched()">
                ✕ Reset
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Episodes -->
        <h3 class="section-title">Episodes</h3>
        <div class="episodes-grid">
            <?php 
            $nextEp = TokuTracker::getNextEpisodeNumber($series['id']);
            foreach ($series['episodes_list'] as $ep): 
                $classes = ['episode-card'];
                if ($ep['is_watched']) $classes[] = 'watched';
                if ($ep['episode_number'] == $nextEp && !$ep['is_watched']) $classes[] = 'current';
            ?>
            <a href="./watch?id=<?php echo $series['id']; ?>&episode=<?php echo $ep['episode_number']; ?>" 
               class="<?php echo implode(' ', $classes); ?>">
                <?php echo $ep['episode_number']; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </main>
    
    <footer>
        Toku Tracker • Built with PHP • Experimental Build
    </footer>
    
    <script>
        const seriesId = <?php echo $series['id']; ?>;
        
        async function markAllWatched() {
            if (!confirm('Mark all episodes as watched?')) return;
            
            try {
                const response = await fetch('api/bulk', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        series_id: seriesId,
                        action: 'watch_all'
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to mark all as watched');
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error marking all as watched');
            }
        }
        
        async function markAllUnwatched() {
            if (!confirm('Reset all progress for this series?')) return;
            
            try {
                const response = await fetch('api/bulk', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        series_id: seriesId,
                        action: 'unwatch_all'
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to reset progress');
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error resetting progress');
            }
        }
    </script>
</body>
</html>
