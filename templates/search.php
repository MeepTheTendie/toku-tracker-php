<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Toku Tracker</title>
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
        
        .search-section {
            background: #1a1a24;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #2a2a35;
            margin-bottom: 2rem;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .search-input {
            flex: 1;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            background: #252530;
            border: 2px solid #3a3a45;
            border-radius: 12px;
            color: #fff;
            outline: none;
            transition: border-color 0.2s;
        }
        
        .search-input:focus {
            border-color: #4a9eff;
        }
        
        .search-btn {
            padding: 1rem 2rem;
            font-size: 1rem;
            background: linear-gradient(135deg, #4a9eff, #00d4aa);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        
        .search-btn:hover {
            opacity: 0.9;
        }
        
        .search-hint {
            color: #666;
            font-size: 0.9rem;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .results-count {
            font-size: 1.2rem;
            color: #fff;
        }
        
        .results-count span {
            color: #4a9eff;
            font-weight: 600;
        }
        
        /* Results Grid */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
        }
        
        .result-card {
            background: #1a1a24;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #2a2a35;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .result-card:hover {
            transform: translateY(-4px);
            border-color: #4a4a5e;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .result-card.completed {
            border-color: #00d4aa;
        }
        
        .result-card.watching {
            border-color: #4a9eff;
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        
        .result-icon {
            font-size: 2rem;
        }
        
        .result-franchise {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            background: #252530;
            border-radius: 4px;
            color: #888;
            text-transform: uppercase;
        }
        
        .result-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .result-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1rem;
        }
        
        .result-tags {
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
        
        .result-progress {
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
            background: linear-gradient(90deg, #4a9eff, #00d4aa);
            border-radius: 3px;
        }
        
        .progress-text {
            font-size: 0.85rem;
            color: #888;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem;
            color: #666;
        }
        
        .empty-state-icon {
            font-size: 4rem;
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
        <div class="search-section">
            <form action="./search" method="get" class="search-form">
                <input type="text" name="q" class="search-input" 
                       placeholder="Search series, tags, eras..." 
                       value="<?php echo htmlspecialchars($query); ?>"
                       autofocus>
                <button type="submit" class="search-btn">Search</button>
            </form>
            <div class="search-hint">
                Try searching for: "Kamen Rider", "dinosaurs", "Heisei", "tribute", "dark", "ninja"...
            </div>
        </div>
        
        <?php if ($query): ?>
        <div class="results-header">
            <div class="results-count">
                Found <span><?php echo count($results); ?></span> results for "<?php echo htmlspecialchars($query); ?>"
            </div>
        </div>
        
        <?php if (empty($results)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>No results found</h3>
            <p>Try a different search term or check your spelling.</p>
        </div>
        <?php else: ?>
        
        <div class="results-grid">
            <?php foreach ($results as $s): ?>
            <a href="./series-detail?id=<?php echo $s['id']; ?>" class="result-card <?php echo $s['status']; ?>">
                <div class="result-header">
                    <span class="result-icon"><?php echo $s['franchise_icon']; ?></span>
                    <span class="result-franchise"><?php echo $s['franchise_name']; ?></span>
                </div>
                <div class="result-name"><?php echo htmlspecialchars($s['name']); ?></div>
                <div class="result-meta">
                    <span><?php echo $s['year']; ?></span>
                    <span><?php echo $s['episodes']; ?> episodes</span>
                    <span><?php echo $s['era']; ?></span>
                </div>
                <?php if (!empty($s['tags'])): ?>
                <div class="result-tags">
                    <?php foreach (array_slice($s['tags'], 0, 4) as $tag): ?>
                    <span class="tag"><?php echo htmlspecialchars(str_replace('_', ' ', $tag)); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="result-progress">
                    <div class="progress-bar">
                        <div class="fill" style="width: <?php echo $s['progress']; ?>"></div>
                    </div>
                    <span class="progress-text">
                        <?php echo $s['status'] === 'completed' ? '✓ Complete' : $s['watched'] . '/' . $s['episodes']; ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </main>
    
    <footer>
        Toku Tracker PHP • <?php echo TOTAL_SERIES_COUNT; ?> series • <?php echo number_format(TOTAL_EPISODE_COUNT); ?> episodes
    </footer>
</body>
</html>
