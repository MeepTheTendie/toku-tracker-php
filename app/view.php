<?php
declare(strict_types=1);
function actionForm(int $id, string $action, string $label, ?int $episode = null, bool $confirm = false): void { ?>
    <form method="post" action="<?= h(url($episode === null ? 'api/bulk' : 'api/watch')) ?>" data-action <?= $confirm ? 'data-confirm="Change progress for every episode in this series?"' : '' ?>>
        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
        <input type="hidden" name="series_id" value="<?= $id ?>">
        <input type="hidden" name="action" value="<?= h($action) ?>">
        <?php if ($episode !== null): ?><input type="hidden" name="episode" value="<?= $episode ?>"><?php endif ?>
        <button class="<?= str_starts_with($action, 'unwatch') ? 'secondary' : 'primary' ?>" type="submit"><?= h($label) ?></button>
    </form>
<?php }
function cards(array $series): void { ?>
    <div class="cards">
    <?php foreach ($series as $s): $franchise = franchises()[$s['franchise']] ?? ['name' => $s['franchise'], 'icon' => '📺']; ?>
        <a class="card <?= h($s['franchise']) ?>" href="<?= h(url('series-detail', ['id' => $s['id']])) ?>">
            <div class="card-top"><span class="symbol" aria-hidden="true"><?= h($franchise['icon']) ?></span><span class="badge"><?= h(ucfirst($s['status'])) ?></span></div>
            <p class="eyebrow"><?= h($franchise['name']) ?></p>
            <h3><?= h($s['name']) ?></h3>
            <p class="muted"><?= h($s['year']) ?> · <?= h($s['era']) ?> · <?= h($s['episodes']) ?> episodes</p>
            <div class="tags"><?php foreach (array_slice($s['tags'],0,3) as $tag): ?><span><?= h(str_replace('_',' ',$tag)) ?></span><?php endforeach ?></div>
            <div class="card-progress"><span><?= $s['watched'] ?> / <?= $s['episodes'] ?> watched</span><strong><?= $s['progress'] ?>%</strong></div>
            <progress value="<?= $s['watched'] ?>" max="<?= $s['episodes'] ?>" aria-label="<?= h($s['name']) ?> progress"></progress>
        </a>
    <?php endforeach ?>
    </div>
<?php } ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title><?= h($title) ?> · Toku Tracker</title>
    <link rel="icon" href="<?= h(url('assets/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= h(url('assets/app.css')) ?>">
    <script src="<?= h(url('assets/app.js')) ?>" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="header"><div class="header-inner">
    <a class="brand" href="<?= h(url()) ?>"><span class="brand-mark" aria-hidden="true">T</span>TOKU<span class="brand-light">TRACKER</span></a>
    <?php if ($signedIn): ?>
    <nav aria-label="Main navigation">
        <?php foreach (['' => 'Dashboard','series' => 'Library','stats' => 'Statistics'] as $route => $label): ?>
        <a href="<?= h(url($route)) ?>" <?= ($route === '' && $page === 'dashboard') || ($route === 'series' && in_array($page,['series','detail','watch'],true)) || $route === $page ? 'aria-current="page"' : '' ?>><?= h($label) ?></a>
        <?php endforeach ?>
    </nav>
    <form method="post" action="<?= h(url('logout')) ?>"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><button class="quiet" type="submit">Sign out</button></form>
    <?php endif ?>
</div></header>
<main id="main" class="container">
<div id="notice" class="notice" role="alert" hidden></div>
<?php if ($page === 'login'): ?>
    <section class="login panel"><p class="eyebrow">YOUR PERSONAL TOKUSATSU JOURNAL</p><h1>Welcome back.</h1><p class="muted">Your heroes. Your watchlist. Your pace.</p>
        <?php if ($error): ?><p class="notice" role="alert"><?= h($error) ?></p><?php endif ?>
        <form method="post" action="<?= h(url('login')) ?>">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required autofocus maxlength="1024">
            <button class="primary" type="submit">Open my tracker →</button>
        </form>
    </section>
<?php elseif ($page === 'dashboard'): ?>
    <section class="hero"><div><p class="eyebrow">ONE EPISODE CLOSER</p><h1>Your next<br>transformation.</h1><p class="muted">From the first henshin to the final battle.<br>Keep your tokusatsu journey in one place.</p><a class="button primary" href="<?= h(url('series')) ?>">Explore the library ↗</a></div><div class="hero-stat"><span class="eyebrow">YOUR JOURNEY</span><strong><?= h($stats['progress']) ?><small>%</small></strong><progress value="<?= $stats['watched'] ?>" max="<?= max(1,$stats['episodes']) ?>" aria-label="Overall watch progress"></progress><p><?= number_format($stats['watched']) ?> episodes watched</p></div></section>
    <div class="metrics"><div><strong><?= $stats['series'] ?></strong><span>Series to explore</span></div><div><strong><?= number_format($stats['watched']) ?></strong><span>Episodes watched</span></div><div><strong><?= count($continuing) ?></strong><span>Currently watching</span></div><div><strong><?= $stats['completed'] ?></strong><span>Series completed</span></div></div>
    <section><div class="section-heading"><h2>Continue watching</h2><a href="<?= h(url('series',['filter'=>'watching'])) ?>">View all →</a></div>
    <?php if ($series): cards($series); else: ?><div class="empty panel"><h3>Your journey starts here.</h3><p class="muted">Choose a series and mark your first episode watched.</p><a class="button primary" href="<?= h(url('series')) ?>">Find a series</a></div><?php endif ?></section>
    <section><div class="section-heading"><h2>Pick your universe</h2></div><div class="franchises"><?php foreach (franchises() as $key=>$f): ?><a class="panel <?= h($key) ?>" href="<?= h(url('series',['franchise'=>$key])) ?>"><span class="symbol" aria-hidden="true"><?= h($f['icon']) ?></span><h3><?= h($f['name']) ?></h3><span class="muted">Explore →</span></a><?php endforeach ?></div></section>
<?php elseif ($page === 'series'): ?>
    <p class="eyebrow">FIND YOUR NEXT ADVENTURE</p><h1><?= h($title) ?></h1>
    <form class="filters panel" method="get" action="<?= h(url('series')) ?>">
        <div class="search-field"><label for="q">Search</label><input id="q" name="q" type="search" placeholder="Series, themes, eras…" value="<?= h($filters['q']) ?>" maxlength="200"></div>
        <div><label for="franchise">Franchise</label><select id="franchise" name="franchise"><option value="">All franchises</option><?php foreach (franchises() as $key=>$f): ?><option value="<?= h($key) ?>" <?= $filters['franchise']===$key?'selected':'' ?>><?= h($f['name']) ?></option><?php endforeach ?></select></div>
        <div><label for="era">Era</label><select id="era" name="era"><option value="">All eras</option><?php foreach ($eras as $era): ?><option <?= $filters['era']===$era?'selected':'' ?>><?= h($era) ?></option><?php endforeach ?></select></div>
        <div><label for="filter">Progress</label><select id="filter" name="filter"><?php foreach ([''=>'Any progress','unwatched'=>'Not started','watching'=>'Watching','completed'=>'Completed'] as $value=>$label): ?><option value="<?= h($value) ?>" <?= $filters['filter']===$value?'selected':'' ?>><?= h($label) ?></option><?php endforeach ?></select></div>
        <button class="primary" type="submit">Find series</button>
    </form>
    <div class="section-heading"><p class="muted"><?= count($series) ?> series found</p><a href="<?= h(url('series')) ?>">Clear filters</a></div>
    <?php if ($series): cards($series); else: ?><div class="empty panel"><h2>No matches yet.</h2><p class="muted">Try a different name or clear a filter.</p></div><?php endif ?>
<?php elseif ($page === 'detail' || $page === 'watch'): $f=franchises()[$detail['franchise']] ?? ['name'=>$detail['franchise'],'icon'=>'📺']; ?>
    <a class="back" href="<?= h(url($page==='watch'?'series-detail':'series',$page==='watch'?['id'=>$detail['id']]:[])) ?>">← <?= $page==='watch'?'Series overview':'Library' ?></a>
    <section class="detail-heading <?= h($detail['franchise']) ?>"><span class="symbol" aria-hidden="true"><?= h($f['icon']) ?></span><p class="eyebrow"><?= h($f['name']) ?> / <?= h($detail['era']) ?></p><h1><?= h($detail['name']) ?></h1><p class="muted"><?= $detail['year'] ?> · <?= $detail['episodes'] ?> episodes · <?= $detail['watched'] ?> watched</p><progress value="<?= $detail['watched'] ?>" max="<?= $detail['episodes'] ?>" aria-label="Series progress"></progress></section>
    <?php if ($page === 'watch'): $current=$detail['episodes_list'][$episode-1]; ?>
        <section class="watch-panel panel"><p class="eyebrow"><?= $current['is_watched']?'WATCHED':'UP NEXT' ?></p><h2>Episode <?= $episode ?></h2><p class="muted">Watch on your preferred platform, then save your progress here.</p>
        <div class="actions"><?php actionForm((int)$detail['id'],$current['is_watched']?'unwatch':'watch',$current['is_watched']?'Mark unwatched':'✓ Mark watched & continue',(int)$episode); ?></div>
        <div class="episode-nav"><?php if ($episode>1): ?><a data-prev href="<?= h(url('watch',['id'=>$detail['id'],'episode'=>$episode-1])) ?>">← Previous</a><?php else: ?><span></span><?php endif ?><span class="muted"><?= $episode ?> / <?= $detail['episodes'] ?></span><?php if ($episode<$detail['episodes']): ?><a data-next href="<?= h(url('watch',['id'=>$detail['id'],'episode'=>$episode+1])) ?>">Next →</a><?php else: ?><span></span><?php endif ?></div>
        <p class="keyboard muted">← → to navigate · W to toggle watched</p></section>
    <?php else: ?>
        <div class="actions"><?php if ($detail['next_episode']!==null): ?><a class="button primary" href="<?= h(url('watch',['id'=>$detail['id'],'episode'=>$detail['next_episode']])) ?>"><?= $detail['watched']?'Continue':'Start watching' ?> · Episode <?= $detail['next_episode'] ?> →</a><?php endif ?><?php if ($detail['watched']<$detail['episodes']) actionForm((int)$detail['id'],'watch_all','Mark all watched',null,true); if ($detail['watched']) actionForm((int)$detail['id'],'unwatch_all','Reset series progress',null,true); ?></div>
        <div class="tags"><?php foreach ($detail['tags'] as $tag): ?><a href="<?= h(url('series',['q'=>$tag])) ?>"><?= h(str_replace('_',' ',$tag)) ?></a><?php endforeach ?></div>
    <?php endif ?>
    <div class="section-heading"><h2>Episodes</h2><span class="muted">✓ = watched</span></div><div class="episode-grid"><?php foreach ($detail['episodes_list'] as $ep): ?><a class="episode <?= $ep['is_watched']?'watched':'' ?>" href="<?= h(url('watch',['id'=>$detail['id'],'episode'=>$ep['episode_number']])) ?>" aria-label="Episode <?= $ep['episode_number'] ?><?= $ep['is_watched']?', watched':'' ?>" <?= $page==='watch' && $episode===$ep['episode_number']?'aria-current="page"':'' ?>><?= $ep['episode_number'] ?><?= $ep['is_watched']?' ✓':'' ?></a><?php endforeach ?></div>
<?php elseif ($page === 'stats'): ?>
    <p class="eyebrow">YOUR TOKUSATSU JOURNEY</p><h1><?= h($title) ?></h1><p class="muted"><?= number_format($stats['watched']) ?> episodes watched across <?= $stats['completed'] ?> completed series.</p>
    <div class="stats-list"><?php foreach (franchises() as $key=>$f): $group=array_filter($all,static fn($s)=>$s['franchise']===$key); $total=array_sum(array_column($group,'episodes')); $watched=array_sum(array_column($group,'watched')); ?><article class="panel <?= h($key) ?>"><div class="section-heading"><h2><?= h($f['icon'].' '.$f['name']) ?></h2><strong><?= $total?round(100*$watched/$total,1):0 ?>%</strong></div><progress value="<?= $watched ?>" max="<?= max(1,$total) ?>" aria-label="<?= h($f['name']) ?> progress"></progress><p class="muted"><?= count($group) ?> series · <?= number_format($watched) ?> / <?= number_format($total) ?> episodes</p></article><?php endforeach ?></div>
    <div class="panel export"><h2>Keep a copy of your journey.</h2><p class="muted">Download your watched episodes and timestamps as JSON.</p><a class="button secondary" href="<?= h(url('export')) ?>">Export watch history ↓</a></div>
<?php endif ?>
</main><footer class="container footer"><span>TOKU TRACKER</span><span>Your heroes. Your pace.</span></footer>
</body></html>
