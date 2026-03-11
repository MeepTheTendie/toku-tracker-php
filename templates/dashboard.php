<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toku Tracker - Dashboard</title>
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
        
        .search-box {
            display: flex;
            align-items: center;
            background: rgba(0,0,0,0.3);
            border: 1px solid #3a3a4e;
            border-radius: 6px;
            padding: 0.5rem 1rem;
        }
        
        .search-box input {
            background: transparent;
            border: none;
            color: #fff;
            outline: none;
            width: 200px;
        }
        
        .search-box input::placeholder {
            color: #666;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #1a1a24;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #2a2a35;
            text-align: center;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            border-color: #3a3a4e;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
        }
        
        .stat-label {
            color: #888;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        
        .stat-card.highlight .stat-value {
            background: linear-gradient(90deg, #4a9eff, #00d4aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Progress Section */
        .progress-section {
            background: #1a1a24;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #2a2a35;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 3rem;
            align-items: center;
        }
        
        .progress-ring-container {
            position: relative;
        }
        
        .progress-ring {
            width: 180px;
            height: 180px;
            transform: rotate(-90deg);
        }
        
        .progress-ring circle {
            fill: none;
            stroke-width: 10;
        }
        
        .progress-ring .bg {
            stroke: #2a2a35;
        }
        
        .progress-ring .fill {
            stroke: url(#gradient);
            stroke-linecap: round;
            transition: stroke-dashoffset 1s ease;
        }
        
        .progress-ring-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .progress-ring-text .percent {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
        }
        
        .progress-ring-text .label {
            font-size: 0.85rem;
            color: #888;
        }
        
        .progress-info h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .progress-detail {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .progress-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .progress-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .progress-dot.completed {
            background: #00d4aa;
        }
        
        .progress-dot.watching {
            background: #4a9eff;
        }
        
        .progress-dot.remaining {
            background: #666;
        }
        
        .progress-item span {
            color: #888;
        }
        
        .progress-item strong {
            color: #fff;
            margin-left: 0.5rem;
        }
        
        /* Franchise Grid */
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .franchise-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .franchise-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #2a2a35;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .franchise-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            opacity: 0.8;
        }
        
        .franchise-card:hover {
            transform: translateY(-4px);
            border-color: #4a4a5e;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .franchise-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .franchise-icon {
            font-size: 2rem;
        }
        
        .franchise-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
        }
        
        .franchise-meta {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1rem;
        }
        
        .franchise-progress {
            margin-bottom: 0.75rem;
        }
        
        .progress-bar-bg {
            height: 8px;
            background: #2a2a35;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .franchise-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #888;
        }
        
        /* Continue Watching */
        .next-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .next-card {
            background: #1a1a24;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #2a2a35;
            display: flex;
            gap: 1rem;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        
        .next-card:hover {
            border-color: #4a9eff;
            background: #1f1f2a;
        }
        
        .next-icon {
            font-size: 2.5rem;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #252530;
            border-radius: 12px;
        }
        
        .next-info {
            flex: 1;
            min-width: 0;
        }
        
        .next-title {
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .next-meta {
            font-size: 0.85rem;
            color: #888;
        }
        
        .next-ep {
            background: linear-gradient(135deg, #4a9eff, #00d4aa);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
            background: #1a1a24;
            border-radius: 12px;
            border: 1px dashed #3a3a4e;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
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
            .progress-section {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .progress-detail {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <svg width="0" height="0" style="position: absolute;">
        <defs>
            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#4a9eff"/>
                <stop offset="100%" style="stop-color:#00d4aa"/>
            </linearGradient>
        </defs>
    </svg>
    
    <header>
        <div class="header-content">
            <h1>⚡ Toku Tracker</h1>
            <nav>
                <a href="./">Dashboard</a>
                <a href="./series">All Series</a>
                <a href="./stats">Statistics</a>
                <form action="./search" method="get" class="search-box">
                    <input type="text" name="q" placeholder="Search..." value="">
                    <button type="submit" style="background: none; border: none; color: #666; cursor: pointer;">🔍</button>
                </form>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_series']; ?></div>
                <div class="stat-label">Total Series</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['total_episodes']); ?></div>
                <div class="stat-label">Total Episodes</div>
            </div>
            <div class="stat-card highlight">
                <div class="stat-value"><?php echo $stats['overall_progress']; ?>%</div>
                <div class="stat-label">Complete</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['watched_episodes']); ?></div>
                <div class="stat-label">Watched</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['completed_series']; ?></div>
                <div class="stat-label">Finished</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['watching_series']; ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        
        <!-- Overall Progress -->
        <div class="progress-section">
            <div class="progress-ring-container">
                <svg class="progress-ring" viewBox="0 0 180 180">
                    <circle class="bg" cx="90" cy="90" r="80"/>
                    <circle class="fill" cx="90" cy="90" r="80" 
                            stroke-dasharray="502.65" 
                            stroke-dashoffset="<?php echo 502.65 * (1 - $stats['overall_progress'] / 100); ?>"/>
                </svg>
                <div class="progress-ring-text">
                    <div class="percent"><?php echo $stats['overall_progress']; ?>%</div>
                    <div class="label">Complete</div>
                </div>
            </div>
            <div class="progress-info">
                <h2>Your Tokusatsu Journey</h2>
                <div class="progress-detail">
                    <div class="progress-item">
                        <div class="progress-dot completed"></div>
                        <span>Watched <strong><?php echo number_format($stats['watched_episodes']); ?></strong> episodes</span>
                    </div>
                    <div class="progress-item">
                        <div class="progress-dot remaining"></div>
                        <span>Remaining <strong><?php echo number_format($stats['total_episodes'] - $stats['watched_episodes']); ?></strong> episodes</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Continue Watching -->
        <?php if (!empty($nextToWatch)): ?>
        <h2 class="section-title">▶ Continue Watching</h2>
        <div class="next-grid">
            <?php foreach ($nextToWatch as $item): ?>
            <a href="./watch?id=<?php echo $item['id']; ?>&episode=<?php echo $item['next_episode']; ?>" class="next-card">
                <div class="next-icon"><?php echo $item['franchise_icon']; ?></div>
                <div class="next-info">
                    <div class="next-title"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="next-meta">
                        EP <?php echo $item['watched_count']; ?> of <?php echo $item['episodes']; ?> watched
                    </div>
                </div>
                <div class="next-ep">EP <?php echo htmlspecialchars((string)$item['next_episode']); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Franchise Breakdown -->
        <h2 class="section-title">📊 By Franchise</h2>
        <div class="franchise-grid">
            <?php foreach ($stats['franchise_stats'] as $key => $franchise): ?>
            <a href="./series?franchise=<?php echo urlencode($key); ?>" class="franchise-card">
                <div class="franchise-header">
                    <span class="franchise-icon"><?php echo $franchise['icon']; ?></span>
                    <span class="franchise-name"><?php echo htmlspecialchars($franchise['name']); ?></span>
                </div>
                <div class="franchise-meta">
                    <?php echo $franchise['series_count']; ?> series • <?php echo $franchise['year_range']; ?> • <?php echo number_format($franchise['total_episodes']); ?> eps
                </div>
                <div class="franchise-progress">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $franchise['progress']; ?>%; background: <?php echo $franchise['color']; ?>"></div>
                    </div>
                </div>
                <div class="franchise-stats">
                    <span><?php echo $franchise['watched']; ?> watched</span>
                    <span><?php echo $franchise['progress']; ?>%</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </main>
    
    <footer>
        Toku Tracker PHP • <?php echo TOTAL_SERIES_COUNT; ?> series • <?php echo number_format(TOTAL_EPISODE_COUNT); ?> episodes
    </footer>
</body>
</html>
