<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/bootstrap.php';
set_error_handler(static function ($severity,$message,$file,$line) { throw new ErrorException($message,0,$severity,$file,$line); });
$dir = sys_get_temp_dir() . '/toku-tests-' . bin2hex(random_bytes(6));
mkdir($dir,0700);
$count = 0;
function check(bool $ok, string $message): void { global $count; if (!$ok) throw new RuntimeException($message); $count++; }
function rejects(callable $work, string $class): void {
    try { $work(); } catch (Throwable $e) { check($e instanceof $class, 'Unexpected exception: ' . get_class($e)); return; }
    throw new RuntimeException('Expected ' . $class);
}
try {
    $catalog = [
        ['key'=>'rider-test','franchise'=>'kamen_rider','name'=>'Test Rider','era'=>'Heisei','year'=>2000,'episodes'=>3,'tags'=>['time_travel']],
        ['key'=>'garo-test','franchise'=>'garo','name'=>'Test GARO','era'=>'Reiwa','year'=>2020,'episodes'=>2,'tags'=>['dark']],
    ];
    $store = new Store($dir.'/fresh.db',$catalog);
    $id = (int)$store->series(['q'=>'Test Rider'])[0]['id'];
    check($store->detail($id)['next_episode'] === 1,'First episode');
    $store->change($id,'watch',2); $store->change($id,'watch',2);
    check($store->detail($id)['watched'] === 1,'Duplicate watches are idempotent');
    check($store->detail($id)['next_episode'] === 1,'Find first gap');
    check(count($store->series(['filter'=>'watching']))===1,'Watching filter');
    check(count($store->series(['q'=>'Heisei']))===1,'Era search');
    check(count($store->series(['q'=>'time_travel']))===1,'Tag search');
    check(count($store->series(['q'=>'%']))===0,'Literal LIKE wildcard');
    rejects(fn()=> $store->change($id,'watch',0),InvalidArgumentException::class);
    rejects(fn()=> $store->change($id,'watch',4),InvalidArgumentException::class);
    rejects(fn()=> $store->change(9999,'watch',1),OutOfBoundsException::class);
    rejects(fn()=> $store->change($id,'delete',1),InvalidArgumentException::class);
    $store->change($id,'watch_all');
    check($store->detail($id)['next_episode']===null,'Completed series has no next episode');
    check(count($store->series(['filter'=>'completed']))===1,'Completed filter');
    $store->change($id,'unwatch',2); check($store->detail($id)['next_episode']===2,'Unwatch opens gap');
    $store->change($id,'unwatch_all'); check($store->detail($id)['watched']===0,'Bulk reset');
    $store->change($id,'watch',3); $before=$store->export()['watched'];
    $catalog[0]['name']='Renamed Rider'; $catalog[0]['episodes']=4;
    $store->syncCatalog($catalog);
    check($store->detail($id)['name']==='Renamed Rider','Stable key renames');
    check(count($store->detail($id)['episodes_list'])===4,'Catalog extends episode list');
    check($store->export()['watched'][0]['watched_at']===$before[0]['watched_at'],'Catalog preserves timestamps');
    $catalog[0]['episodes']=2; $store->syncCatalog($catalog);
    check($store->detail($id)['watched']===0,'Reduced count excludes out-of-range history');
    check(count($store->export()['watched'])===1,'Reduced count retains history for recovery');
    $catalog[0]['episodes']=4; $store->syncCatalog($catalog);
    check($store->detail($id)['watched']===1,'History restored when count grows');
    $store->backup($dir.'/backup.db');
    $backup = new PDO('sqlite:'.$dir.'/backup.db');
    check((int)$backup->query('SELECT COUNT(*) FROM watched')->fetchColumn()===1,'Backup includes WAL writes');
    rejects(fn()=> $store->backup($dir.'/backup.db'),RuntimeException::class);
    $bad=$catalog; $bad[]=$catalog[0]; rejects(fn()=> $store->syncCatalog($bad),InvalidArgumentException::class);
    check(count($store->series())===2,'Invalid catalog changes nothing');
    for($i=0;$i<10;$i++)$store->recordLogin('test',false,1000);
    check(!$store->loginAllowed('test',1001),'Rate limit');
    check($store->loginAllowed('other',1001),'Rate limits isolated by address');
    check($store->loginAllowed('test',1900),'Rate limit expires');
    $store->recordLogin('test',true,1001); check($store->loginAllowed('test',1001),'Successful login clears failures');
    $reflection = new ReflectionProperty(Store::class,'db');
    $reflection->getValue($store)->exec('PRAGMA query_only=ON');
    rejects(fn()=> $store->change($id,'watch',1),PDOException::class);
    $reflection->getValue($store)->exec('PRAGMA query_only=OFF');
    $legacy=new PDO('sqlite:'.$dir.'/legacy.db');
    $legacy->exec("CREATE TABLE series (id INTEGER PRIMARY KEY AUTOINCREMENT,franchise TEXT,name TEXT,era TEXT,year INTEGER,episodes INTEGER,tags TEXT);
        CREATE TABLE episodes (id INTEGER PRIMARY KEY AUTOINCREMENT,series_id INTEGER,episode_number INTEGER,title TEXT);
        CREATE TABLE watched (id INTEGER PRIMARY KEY AUTOINCREMENT,series_id INTEGER,episode_number INTEGER,watched_at TEXT,UNIQUE(series_id,episode_number));
        INSERT INTO series VALUES (42,'kamen_rider','Renamed Rider','Heisei',2000,3,'[]');
        INSERT INTO episodes VALUES (1,42,1,'Episode 1');
        INSERT INTO watched VALUES (1,42,1,'2024-01-01 12:00:00');");
    $legacy=null;
    $migrated=new Store($dir.'/legacy.db',$catalog);
    check($migrated->detail(42)['watched']===1,'Migration preserves IDs/history');
    check($migrated->export()['watched'][0]['watched_at']==='2024-01-01 12:00:00','Migration preserves original timestamp');
    check(count(glob($dir.'/legacy.db.pre-rewrite-*.db'))===1,'Migration creates original backup');
    $migrated=new Store($dir.'/legacy.db',$catalog);
    check(count($migrated->series())===2,'Repeated migration is idempotent');
    $full = new Store($dir.'/catalog.db',require dirname(__DIR__).'/catalog/series.php');
    check(count($full->series(['franchise'=>'ultraman']))>0,'Full catalog enables Ultraman');
    check(count($full->series())===count(require dirname(__DIR__).'/catalog/series.php'),'Every catalog entry seeds once');
    check($store->detail(9999)===null,'Missing series returns null');
    check($store->series(['filter'=>'bogus'])===[],'Unknown status filter returns nothing');
    $big = new Store($dir.'/big.db',[['key'=>'big','franchise'=>'kamen_rider','name'=>'Big Rider','era'=>'Reiwa','year'=>2021,'episodes'=>1000,'tags'=>[]]]);
    $bigId=(int)$big->series()[0]['id'];
    check(count($big->detail($bigId)['episodes_list'])===1000,'Large episode list seeds fully');
    check($big->detail($bigId)['next_episode']===1,'Large series starts at first episode');
    $big->change($bigId,'watch',500);
    check($big->detail($bigId)['next_episode']===1,'Large series finds first gap');
    check(count($big->series(['filter'=>'watching']))===1,'Large series status filter in SQL');
    check($big->detail($bigId)['id']===$bigId,'Detail returns the requested series only');
    check(h('<script>')==='&lt;script&gt;','HTML escaping');
    $_SESSION=['csrf'=>'test']; check(Security::csrf('test')&&!Security::csrf(['test'])&&!Security::csrf('bad'),'CSRF validation');
    echo "$count checks passed.\n";
} finally {
    $store=$migrated=$full=$backup=null;
    foreach(glob($dir.'/*') as $file)unlink($file);
    rmdir($dir);
}
