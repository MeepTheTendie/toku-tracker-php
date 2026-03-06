<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EP <?php echo $episode; ?> - <?php echo htmlspecialchars($series['name']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0c;
            color: #e0e0e5;
            min-height: 100vh;
        }
        
        header {
            background: #15151a;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2a2a35;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .back-btn {
            color: #888;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s;
        }
        
        .back-btn:hover {
            color: #fff;
        }
        
        .episode-title {
            font-size: 1rem;
            color: #fff;
        }
        
        .episode-subtitle {
            font-size: 0.85rem;
            color: #666;
        }
        
        /* Video Player Placeholder */
        .player-container {
            background: #000;
            aspect-ratio: 16/9;
            max-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .player-placeholder {
            text-align: center;
            color: #444;
        }
        
        .player-placeholder .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .player-placeholder p {
            font-size: 1.1rem;
        }
        
        /* Controls */
        .controls-container {
            background: #15151a;
            border-top: 1px solid #2a2a35;
            padding: 1.5rem;
        }
        
        .controls-inner {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .episode-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .nav-btn {
            background: #252530;
            border: 1px solid #3a3a45;
            color: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-btn:hover:not(:disabled) {
            background: #3a3a45;
            border-color: #4a4a55;
        }
        
        .nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .nav-btn.primary {
            background: #4a9eff;
            border-color: #4a9eff;
        }
        
        .nav-btn.primary:hover {
            background: #3a8eef;
        }
        
        .nav-btn.success {
            background: #00d4aa;
            border-color: #00d4aa;
        }
        
        .nav-btn.success:hover {
            background: #00c49a;
        }
        
        .episode-counter {
            font-size: 0.95rem;
            color: #888;
        }
        
        /* Episode List Mini */
        .episodes-mini {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        
        .ep-mini {
            min-width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #252530;
            border: 2px solid #3a3a45;
            border-radius: 8px;
            text-decoration: none;
            color: #888;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .ep-mini:hover {
            border-color: #4a9eff;
            color: #fff;
        }
        
        .ep-mini.current {
            background: #4a9eff;
            border-color: #4a9eff;
            color: #fff;
        }
        
        .ep-mini.watched {
            background: #00d4aa;
            border-color: #00d4aa;
            color: #fff;
        }
        
        /* Watch Status */
        .watch-status {
            text-align: center;
            padding: 1rem;
            background: #1a1a24;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .watch-status.watched {
            background: rgba(0, 212, 170, 0.1);
            border: 1px solid #00d4aa;
        }
        
        .watch-status-text {
            color: #00d4aa;
            font-weight: 600;
        }
        
        footer {
            text-align: center;
            padding: 1.5rem;
            color: #666;
            font-size: 0.8rem;
            border-top: 1px solid #2a2a35;
        }
        
        /* External Link Notice */
        .external-notice {
            background: #1a1a24;
            border: 1px solid #3a3a45;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .external-notice p {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .external-link {
            color: #4a9eff;
            text-decoration: none;
        }
        
        .external-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <a href="./series-detail?id=<?php echo $series['id']; ?>" class="back-btn">
            ← Back to Series
        </a>
        <div style="text-align: center;">
            <div class="episode-title"><?php echo htmlspecialchars($series['name']); ?></div>
            <div class="episode-subtitle">Episode <?php echo $episode; ?></div>
        </div>
        <div style="width: 100px;"></div>
    </header>
    
    <div class="player-container">
        <div class="player-placeholder">
            <div class="icon">📺</div>
            <p>This is a tracker app, not a video player</p>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Mark this episode as watched after viewing</p>
        </div>
    </div>
    
    <div class="controls-container">
        <div class="controls-inner">
            <!-- External Watch Notice -->
            <div class="external-notice">
                <p>Watch this episode on your preferred platform, then mark it as watched here.</p>
                <p style="font-size: 0.8rem; margin-top: 0.5rem; color: #666;">
                    This app tracks your progress • It does not host videos
                </p>
            </div>
            
            <!-- Navigation -->
            <div class="episode-nav">
                <a href="./watch?id=<?php echo $series['id']; ?>&episode=<?php echo max(1, $episode - 1); ?>" 
                   class="nav-btn" 
                   <?php echo $episode <= 1 ? 'disabled' : ''; ?>>
                    ← Previous
                </a>
                
                <span class="episode-counter">
                    Episode <?php echo $episode; ?> of <?php echo $series['episodes']; ?>
                </span>
                
                <?php 
                $epData = $series['episodes_list'][$episode - 1] ?? null;
                $isWatched = $epData['is_watched'] ?? false;
                ?>
                
                <?php if ($isWatched): ?>
                <button class="nav-btn success" onclick="unwatchEpisode()">
                    ✓ Watched
                </button>
                <?php else: ?>
                <button class="nav-btn primary" onclick="watchEpisode()">
                    Mark as Watched
                </button>
                <?php endif; ?>
                
                <a href="./watch?id=<?php echo $series['id']; ?>&episode=<?php echo min($series['episodes'], $episode + 1); ?>" 
                   class="nav-btn"
                   <?php echo $episode >= $series['episodes'] ? 'disabled' : ''; ?>>
                    Next →
                </a>
            </div>
            
            <!-- Episode Quick Jump -->
            <div class="episodes-mini">
                <?php foreach ($series['episodes_list'] as $ep): 
                    $classes = ['ep-mini'];
                    if ($ep['episode_number'] == $episode) $classes[] = 'current';
                    elseif ($ep['is_watched']) $classes[] = 'watched';
                ?>
                <a href="./watch?id=<?php echo $series['id']; ?>&episode=<?php echo $ep['episode_number']; ?>" 
                   class="<?php echo implode(' ', $classes); ?>">
                    <?php echo $ep['episode_number']; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <?php if ($isWatched): ?>
            <div class="watch-status watched">
                <span class="watch-status-text">✓ You watched this episode</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        Toku Tracker • Built with PHP • Experimental Build
    </footer>
    
    <script>
        const seriesId = <?php echo $series['id']; ?>;
        const episodeNum = <?php echo $episode; ?>;
        
        async function watchEpisode() {
            try {
                const response = await fetch('api/watch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        series_id: seriesId,
                        episode: episodeNum,
                        action: 'watch'
                    })
                });
                
                if (response.ok) {
                    // Reload to show updated state or advance
                    const nextEp = episodeNum + 1;
                    if (nextEp <= <?php echo $series['episodes']; ?>) {
                        window.location.href = `./watch?id=${seriesId}&episode=${nextEp}`;
                    } else {
                        window.location.reload();
                    }
                }
            } catch (err) {
                console.error('Failed to mark as watched:', err);
            }
        }
        
        async function unwatchEpisode() {
            try {
                const response = await fetch('api/watch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        series_id: seriesId,
                        episode: episodeNum,
                        action: 'unwatch'
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (err) {
                console.error('Failed to unmark:', err);
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                <?php if ($episode > 1): ?>
                window.location.href = `./watch?id=${seriesId}&episode=${episodeNum - 1}`;
                <?php endif; ?>
            } else if (e.key === 'ArrowRight') {
                <?php if ($episode < $series['episodes']): ?>
                window.location.href = `./watch?id=${seriesId}&episode=${episodeNum + 1}`;
                <?php endif; ?>
            } else if (e.key === ' ' || e.key === 'w') {
                e.preventDefault();
                <?php if ($isWatched): ?>
                unwatchEpisode();
                <?php else: ?>
                watchEpisode();
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
