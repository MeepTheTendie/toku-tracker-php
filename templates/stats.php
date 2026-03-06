<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Toku Tracker</title>
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
        
        .page-title {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 2rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #2a2a35;
            text-align: center;
        }
        
        .stat-value {
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #888;
            font-size: 1rem;
        }
        
        .stat-card.primary .stat-value {
            background: linear-gradient(90deg, #4a9eff, #00d4aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Franchise Stats */
        .franchise-section {
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1.5rem;
        }
        
        .franchise-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #2a2a35;
            margin-bottom: 1.5rem;
        }
        
        .franchise-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .franchise-icon {
            font-size: 3rem;
        }
        
        .franchise-info h3 {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }
        
        .franchise-info p {
            color: #888;
        }
        
        .franchise-stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .franchise-stat {
            text-align: center;
            padding: 1rem;
            background: #252530;
            border-radius: 12px;
        }
        
        .franchise-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }
        
        .franchise-stat-label {
            font-size: 0.85rem;
            color: #888;
            margin-top: 0.25rem;
        }
        
        .progress-container {
            background: #252530;
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .progress-label {
            color: #888;
        }
        
        .progress-value {
            color: #fff;
            font-weight: 600;
        }
        
        .progress-bar-bg {
            height: 12px;
            background: #2a2a35;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.5s ease;
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
                <a href="./stats">Statistics</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <h2 class="page-title">📊 Statistics</h2>
        
        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-value"><?php echo $stats['overall_progress']; ?>%</div>
                <div class="stat-label">Overall Progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_series']; ?></div>
                <div class="stat-label">Total Series</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['total_episodes']); ?></div>
                <div class="stat-label">Total Episodes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['watched_episodes']); ?></div>
                <div class="stat-label">Episodes Watched</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['completed_series']; ?></div>
                <div class="stat-label">Series Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['watching_series']; ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        
        <!-- Per Franchise -->
        <div class="franchise-section">
            <h3 class="section-title">By Franchise</h3>
            
            <?php foreach ($franchiseStats as $key => $f): ?>
            <div class="franchise-card">
                <div class="franchise-header">
                    <span class="franchise-icon"><?php echo $f['icon']; ?></span>
                    <div class="franchise-info">
                        <h3><?php echo $f['name']; ?></h3>
                        <p><?php echo $f['year_range']; ?> • <?php echo $f['series_count']; ?> series</p>
                    </div>
                </div>
                
                <div class="franchise-stats-row">
                    <div class="franchise-stat">
                        <div class="franchise-stat-value"><?php echo number_format($f['total_episodes']); ?></div>
                        <div class="franchise-stat-label">Total Episodes</div>
                    </div>
                    <div class="franchise-stat">
                        <div class="franchise-stat-value"><?php echo number_format($f['watched']); ?></div>
                        <div class="franchise-stat-label">Watched</div>
                    </div>
                    <div class="franchise-stat">
                        <div class="franchise-stat-value"><?php echo $f['progress']; ?>%</div>
                        <div class="franchise-stat-label">Progress</div>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-header">
                        <span class="progress-label">Progress</span>
                        <span class="progress-value"><?php echo number_format($f['watched']); ?> / <?php echo number_format($f['total_episodes']); ?></span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?php echo $f['progress']; ?>%; background: <?php echo $f['color']; ?>"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
    
    <footer>
        Toku Tracker PHP • <?php echo TOTAL_SERIES_COUNT; ?> series • <?php echo number_format(TOTAL_EPISODE_COUNT); ?> episodes
    </footer>
</body>
</html>
