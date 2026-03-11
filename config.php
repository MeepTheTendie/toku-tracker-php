<?php
/**
 * Toku Tracker - Configuration
 * Complete Tokusatsu Database
 */

// Paths
if (!defined('DATA_DIR')) {
    define('DATA_DIR', __DIR__ . '/data');
}
if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', __DIR__ . '/cache');
}
if (!defined('DB_FILE')) {
    define('DB_FILE', DATA_DIR . '/toku.db');
}

// Ensure directories exist
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(CACHE_DIR)) mkdir(CACHE_DIR, 0755, true);

// Session for user progress
session_start();
if (!isset($_SESSION['watched'])) {
    $_SESSION['watched'] = [];
}

// Franchise definitions with colors
if (!defined('FRANCHISES')) {
    define('FRANCHISES', [
    'kamen_rider' => [
        'name' => 'Kamen Rider',
        'icon' => '🦗',
        'color' => '#00a8ff',
        'bg_gradient' => 'linear-gradient(135deg, #0066cc 0%, #00a8ff 100%)',
        'eras' => ['Showa', 'Heisei Phase 1', 'Heisei Phase 2', 'Reiwa']
    ],
    'super_sentai' => [
        'name' => 'Super Sentai',
        'icon' => '🦖',
        'color' => '#e74c3c',
        'bg_gradient' => 'linear-gradient(135deg, #c0392b 0%, #e74c3c 100%)',
        'eras' => ['Showa', 'Heisei', 'Reiwa']
    ],
    // 'ultraman' => [
    //     'name' => 'Ultraman',
    //     'icon' => '✨',
    //     'color' => '#f39c12',
    //     'bg_gradient' => 'linear-gradient(135deg, #d68910 0%, #f39c12 100%)',
    //     'eras' => ['Showa', 'Heisei', 'New Generation', 'Reiwa']
    // ],
    'metal_hero' => [
        'name' => 'Metal Hero',
        'icon' => '🤖',
        'color' => '#9b59b6',
        'bg_gradient' => 'linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%)',
        'eras' => ['Showa', 'Heisei']
    ],
    'garo' => [
        'name' => 'GARO',
        'icon' => '🐺',
        'color' => '#f1c40f',
        'bg_gradient' => 'linear-gradient(135deg, #d4ac0d 0%, #f1c40f 100%)',
        'eras' => ['Heisei', 'Reiwa']
    ]]);
}

/**
 * Complete Tokusatsu Series Database
 */
const FULL_SERIES_DATABASE = [
    // ============================================
    // KAMEN RIDER - SHOWA ERA (1971-1994)
    // ============================================
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider', 'era' => 'Showa', 'year' => 1971, 'episodes' => 98, 'tags' => ['classic', 'ichigo']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider V3', 'era' => 'Showa', 'year' => 1973, 'episodes' => 52, 'tags' => ['classic']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider X', 'era' => 'Showa', 'year' => 1974, 'episodes' => 35, 'tags' => ['classic']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Amazon', 'era' => 'Showa', 'year' => 1974, 'episodes' => 24, 'tags' => ['classic', 'violent']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Stronger', 'era' => 'Showa', 'year' => 1975, 'episodes' => 39, 'tags' => ['classic']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Skyrider', 'era' => 'Showa', 'year' => 1979, 'episodes' => 54, 'tags' => ['classic']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Super-1', 'era' => 'Showa', 'year' => 1980, 'episodes' => 48, 'tags' => ['classic']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Black', 'era' => 'Showa', 'year' => 1987, 'episodes' => 51, 'tags' => ['classic', 'dark', 'kotaro']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Black RX', 'era' => 'Showa', 'year' => 1988, 'episodes' => 47, 'tags' => ['classic', 'dark', 'kotaro']],
    ['franchise' => 'kamen_rider', 'name' => 'Shin Kamen Rider', 'era' => 'Showa', 'year' => 1992, 'episodes' => 1, 'tags' => ['movie', 'violent', 'ishinomori']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider ZO', 'era' => 'Showa', 'year' => 1993, 'episodes' => 1, 'tags' => ['movie']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider J', 'era' => 'Showa', 'year' => 1994, 'episodes' => 1, 'tags' => ['movie']],
    
    // ============================================
    // KAMEN RIDER - HEISEI PHASE 1 (2000-2009)
    // ============================================
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Kuuga', 'era' => 'Heisei Phase 1', 'year' => 2000, 'episodes' => 49, 'tags' => ['heisei', 'godai', 'mystery', 'dark']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Agito', 'era' => 'Heisei Phase 1', 'year' => 2001, 'episodes' => 51, 'tags' => ['heisei', 'mystery', 'trinity']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Ryuki', 'era' => 'Heisei Phase 1', 'year' => 2002, 'episodes' => 50, 'tags' => ['heisei', 'battle_royale', 'mirrors']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider 555', 'era' => 'Heisei Phase 1', 'year' => 2003, 'episodes' => 50, 'tags' => ['heisei', 'faiz', 'phones', 'orphnoch']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Blade', 'era' => 'Heisei Phase 1', 'year' => 2004, 'episodes' => 49, 'tags' => ['heisei', 'cards', 'joker']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Hibiki', 'era' => 'Heisei Phase 1', 'year' => 2005, 'episodes' => 48, 'tags' => ['heisei', 'music', 'oni', 'different']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Kabuto', 'era' => 'Heisei Phase 1', 'year' => 2006, 'episodes' => 49, 'tags' => ['heisei', 'speed', 'clock_up', 'worms']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Den-O', 'era' => 'Heisei Phase 1', 'year' => 2007, 'episodes' => 49, 'tags' => ['heisei', 'time_travel', 'trains', 'comedy', 'momotaros']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Kiva', 'era' => 'Heisei Phase 1', 'year' => 2008, 'episodes' => 48, 'tags' => ['heisei', 'vampire', 'time_skip', 'fangires']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Decade', 'era' => 'Heisei Phase 1', 'year' => 2009, 'episodes' => 31, 'tags' => ['heisei', 'anniversary', 'worlds', 'tsukasa']],
    
    // ============================================
    // KAMEN RIDER - HEISEI PHASE 2 (2009-2019)
    // ============================================
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider W', 'era' => 'Heisei Phase 2', 'year' => 2009, 'episodes' => 49, 'tags' => ['heisei', 'detective', 'gaia_memories', 'two_in_one']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider OOO', 'era' => 'Heisei Phase 2', 'year' => 2010, 'episodes' => 48, 'tags' => ['heisei', 'medals', 'greeed', 'ankh']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Fourze', 'era' => 'Heisei Phase 2', 'year' => 2011, 'episodes' => 48, 'tags' => ['heisei', 'space', 'high_school', 'astroswitches']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Wizard', 'era' => 'Heisei Phase 2', 'year' => 2012, 'episodes' => 53, 'tags' => ['heisei', 'magic', 'rings', 'phantoms']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Gaim', 'era' => 'Heisei Phase 2', 'year' => 2013, 'episodes' => 47, 'tags' => ['heisei', 'fruit', 'war', 'genesis', 'uzumes']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Drive', 'era' => 'Heisei Phase 2', 'year' => 2014, 'episodes' => 48, 'tags' => ['heisei', 'cars', 'police', 'roidmudes']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Ghost', 'era' => 'Heisei Phase 2', 'year' => 2015, 'episodes' => 50, 'tags' => ['heisei', 'ghosts', 'eyecons', 'historical']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Ex-Aid', 'era' => 'Heisei Phase 2', 'year' => 2016, 'episodes' => 45, 'tags' => ['heisei', 'games', 'doctors', 'bugsters']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Build', 'era' => 'Heisei Phase 2', 'year' => 2017, 'episodes' => 49, 'tags' => ['heisei', 'science', 'best_match', 'evolto']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Zi-O', 'era' => 'Heisei Phase 2', 'year' => 2018, 'episodes' => 49, 'tags' => ['heisei', 'anniversary', 'time', 'ohma']],
    
    // ============================================
    // KAMEN RIDER - REIWA ERA (2019-)
    // ============================================
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Zero-One', 'era' => 'Reiwa', 'year' => 2019, 'episodes' => 45, 'tags' => ['reiwa', 'ai', 'humagear', 'ark']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Saber', 'era' => 'Reiwa', 'year' => 2020, 'episodes' => 48, 'tags' => ['reiwa', 'swords', 'books', 'wonder_world']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Revice', 'era' => 'Reiwa', 'year' => 2021, 'episodes' => 50, 'tags' => ['reiwa', 'demons', 'contracts', 'vistamps']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Geats', 'era' => 'Reiwa', 'year' => 2022, 'episodes' => 49, 'tags' => ['reiwa', 'games', 'desire', 'dgp']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Gotchard', 'era' => 'Reiwa', 'year' => 2023, 'episodes' => 50, 'tags' => ['reiwa', 'alchemy', 'chemies', 'cards']],
    ['franchise' => 'kamen_rider', 'name' => 'Kamen Rider Gavv', 'era' => 'Reiwa', 'year' => 2024, 'episodes' => 48, 'tags' => ['reiwa', 'candy', 'granute', 'ongoing']],
    
    // ============================================
    // SUPER SENTAI - SHOWA ERA (1975-1989)
    // ============================================
    ['franchise' => 'super_sentai', 'name' => 'Himitsu Sentai Gorenger', 'era' => 'Showa', 'year' => 1975, 'episodes' => 84, 'tags' => ['first', 'classic', 'spy']],
    ['franchise' => 'super_sentai', 'name' => 'J.A.K.Q. Dengekitai', 'era' => 'Showa', 'year' => 1977, 'episodes' => 35, 'tags' => ['classic', 'cards']],
    ['franchise' => 'super_sentai', 'name' => 'Battle Fever J', 'era' => 'Showa', 'year' => 1979, 'episodes' => 52, 'tags' => ['classic', 'disco', 'international']],
    ['franchise' => 'super_sentai', 'name' => 'Denshi Sentai Denziman', 'era' => 'Showa', 'year' => 1980, 'episodes' => 51, 'tags' => ['classic']],
    ['franchise' => 'super_sentai', 'name' => 'Taiyo Sentai Sun Vulcan', 'era' => 'Showa', 'year' => 1981, 'episodes' => 50, 'tags' => ['classic', 'animals']],
    ['franchise' => 'super_sentai', 'name' => 'Dai Sentai Goggle-V', 'era' => 'Showa', 'year' => 1982, 'episodes' => 50, 'tags' => ['classic', 'gymnastics']],
    ['franchise' => 'super_sentai', 'name' => 'Kagaku Sentai Dynaman', 'era' => 'Showa', 'year' => 1983, 'episodes' => 51, 'tags' => ['classic', 'science']],
    ['franchise' => 'super_sentai', 'name' => 'Choudenshi Bioman', 'era' => 'Showa', 'year' => 1984, 'episodes' => 51, 'tags' => ['classic', 'bio']],
    ['franchise' => 'super_sentai', 'name' => 'Dengeki Sentai Changeman', 'era' => 'Showa', 'year' => 1985, 'episodes' => 55, 'tags' => ['classic', 'military']],
    ['franchise' => 'super_sentai', 'name' => 'Choushinsei Flashman', 'era' => 'Showa', 'year' => 1986, 'episodes' => 50, 'tags' => ['classic', 'space']],
    ['franchise' => 'super_sentai', 'name' => 'Hikari Sentai Maskman', 'era' => 'Showa', 'year' => 1987, 'episodes' => 51, 'tags' => ['classic', 'aura']],
    ['franchise' => 'super_sentai', 'name' => 'Choujuu Sentai Liveman', 'era' => 'Showa', 'year' => 1988, 'episodes' => 49, 'tags' => ['classic', 'academia']],
    
    // ============================================
    // SUPER SENTAI - HEISEI ERA (1989-2019)
    // ============================================
    ['franchise' => 'super_sentai', 'name' => 'Kousoku Sentai Turboranger', 'era' => 'Heisei', 'year' => 1989, 'episodes' => 51, 'tags' => ['high_school', 'cars']],
    ['franchise' => 'super_sentai', 'name' => 'Chikyuu Sentai Fiveman', 'era' => 'Heisei', 'year' => 1990, 'episodes' => 48, 'tags' => ['teachers', 'family']],
    ['franchise' => 'super_sentai', 'name' => 'Choujin Sentai Jetman', 'era' => 'Heisei', 'year' => 1991, 'episodes' => 51, 'tags' => ['birds', 'mature', 'classic']],
    ['franchise' => 'super_sentai', 'name' => 'Kyoryu Sentai Zyuranger', 'era' => 'Heisei', 'year' => 1992, 'episodes' => 50, 'tags' => ['dinosaurs', 'first_power_rangers']],
    ['franchise' => 'super_sentai', 'name' => 'Gosei Sentai Dairanger', 'era' => 'Heisei', 'year' => 1993, 'episodes' => 50, 'tags' => ['martial_arts', 'mythical']],
    ['franchise' => 'super_sentai', 'name' => 'Ninja Sentai Kakuranger', 'era' => 'Heisei', 'year' => 1994, 'episodes' => 53, 'tags' => ['ninja', 'yokai']],
    ['franchise' => 'super_sentai', 'name' => 'Chouriki Sentai Ohranger', 'era' => 'Heisei', 'year' => 1995, 'episodes' => 48, 'tags' => ['ancient', 'robots']],
    ['franchise' => 'super_sentai', 'name' => 'Gekisou Sentai Carranger', 'era' => 'Heisei', 'year' => 1996, 'episodes' => 48, 'tags' => ['parody', 'cars', 'comedy']],
    ['franchise' => 'super_sentai', 'name' => 'Denji Sentai Megaranger', 'era' => 'Heisei', 'year' => 1997, 'episodes' => 51, 'tags' => ['video_games', 'space']],
    ['franchise' => 'super_sentai', 'name' => 'Seijuu Sentai Gingaman', 'era' => 'Heisei', 'year' => 1998, 'episodes' => 50, 'tags' => ['nature', 'beasts']],
    ['franchise' => 'super_sentai', 'name' => 'Kyuukyuu Sentai GoGoFive', 'era' => 'Heisei', 'year' => 1999, 'episodes' => 50, 'tags' => ['rescue', 'family']],
    ['franchise' => 'super_sentai', 'name' => 'Mirai Sentai Timeranger', 'era' => 'Heisei', 'year' => 2000, 'episodes' => 50, 'tags' => ['time_travel', 'police']],
    ['franchise' => 'super_sentai', 'name' => 'Hyakujuu Sentai Gaoranger', 'era' => 'Heisei', 'year' => 2001, 'episodes' => 51, 'tags' => ['animals', 'anniversary']],
    ['franchise' => 'super_sentai', 'name' => 'Ninpuu Sentai Hurricaneger', 'era' => 'Heisei', 'year' => 2002, 'episodes' => 51, 'tags' => ['ninja', 'gouraiger']],
    ['franchise' => 'super_sentai', 'name' => 'Bakuryuu Sentai Abaranger', 'era' => 'Heisei', 'year' => 2003, 'episodes' => 50, 'tags' => ['dinosaurs', 'comedy']],
    ['franchise' => 'super_sentai', 'name' => 'Tokusou Sentai Dekaranger', 'era' => 'Heisei', 'year' => 2004, 'episodes' => 50, 'tags' => ['police', 'aliens']],
    ['franchise' => 'super_sentai', 'name' => 'Mahou Sentai Magiranger', 'era' => 'Heisei', 'year' => 2005, 'episodes' => 49, 'tags' => ['magic', 'family', 'fantasy']],
    ['franchise' => 'super_sentai', 'name' => 'GoGo Sentai Boukenger', 'era' => 'Heisei', 'year' => 2006, 'episodes' => 49, 'tags' => ['adventure', 'treasure', 'anniversary']],
    ['franchise' => 'super_sentai', 'name' => 'Juken Sentai Gekiranger', 'era' => 'Heisei', 'year' => 2007, 'episodes' => 49, 'tags' => ['martial_arts', 'beasts']],
    ['franchise' => 'super_sentai', 'name' => 'Engine Sentai Go-Onger', 'era' => 'Heisei', 'year' => 2008, 'episodes' => 50, 'tags' => ['engines', 'pollution', 'comedy']],
    ['franchise' => 'super_sentai', 'name' => 'Samurai Sentai Shinkenger', 'era' => 'Heisei', 'year' => 2009, 'episodes' => 49, 'tags' => ['samurai', 'kamon', 'serious']],
    ['franchise' => 'super_sentai', 'name' => 'Tensou Sentai Goseiger', 'era' => 'Heisei', 'year' => 2010, 'episodes' => 50, 'tags' => ['angels', 'cards', 'tribute']],
    ['franchise' => 'super_sentai', 'name' => 'Kaizoku Sentai Gokaiger', 'era' => 'Heisei', 'year' => 2011, 'episodes' => 51, 'tags' => ['pirates', 'anniversary', 'tribute', 'legend']],
    ['franchise' => 'super_sentai', 'name' => 'Tokumei Sentai Go-Busters', 'era' => 'Heisei', 'year' => 2012, 'episodes' => 50, 'tags' => ['spy', 'technology', 'darker']],
    ['franchise' => 'super_sentai', 'name' => 'Zyuden Sentai Kyoryuger', 'era' => 'Heisei', 'year' => 2013, 'episodes' => 48, 'tags' => ['dinosaurs', 'dancing', 'brave']],
    ['franchise' => 'super_sentai', 'name' => 'Ressha Sentai ToQger', 'era' => 'Heisei', 'year' => 2014, 'episodes' => 47, 'tags' => ['trains', 'imagination', 'color_swap']],
    ['franchise' => 'super_sentai', 'name' => 'Shuriken Sentai Ninninger', 'era' => 'Heisei', 'year' => 2015, 'episodes' => 47, 'tags' => ['ninja', 'family', 'tribute']],
    ['franchise' => 'super_sentai', 'name' => 'Doubutsu Sentai Zyuohger', 'era' => 'Heisei', 'year' => 2016, 'episodes' => 48, 'tags' => ['animals', 'cubes', 'zyuman']],
    ['franchise' => 'super_sentai', 'name' => 'Uchu Sentai Kyuranger', 'era' => 'Heisei', 'year' => 2017, 'episodes' => 48, 'tags' => ['space', 'constellations', 'nine_rangers']],
    ['franchise' => 'super_sentai', 'name' => 'Kaitou Sentai Lupinranger VS Keisatsu Sentai Patranger', 'era' => 'Heisei', 'year' => 2018, 'episodes' => 51, 'tags' => ['thieves', 'police', 'dual', 'vs']],
    ['franchise' => 'super_sentai', 'name' => 'Kishiryu Sentai Ryusoulger', 'era' => 'Heisei', 'year' => 2019, 'episodes' => 48, 'tags' => ['knights', 'dinosaurs']],
    
    // ============================================
    // SUPER SENTAI - REIWA ERA (2019-)
    // ============================================
    ['franchise' => 'super_sentai', 'name' => 'Mashin Sentai Kiramager', 'era' => 'Reiwa', 'year' => 2020, 'episodes' => 45, 'tags' => ['gemstones', 'vehicles', 'kiramei']],
    ['franchise' => 'super_sentai', 'name' => 'Kikai Sentai Zenkaiger', 'era' => 'Reiwa', 'year' => 2021, 'episodes' => 49, 'tags' => ['robots', 'anniversary', 'alternate_world', 'tribute']],
    ['franchise' => 'super_sentai', 'name' => 'Avataro Sentai Donbrothers', 'era' => 'Reiwa', 'year' => 2022, 'episodes' => 50, 'tags' => ['momotaro', 'chaos', 'comedy', 'wild']],
    ['franchise' => 'super_sentai', 'name' => 'Ohsama Sentai King-Ohger', 'era' => 'Reiwa', 'year' => 2023, 'episodes' => 50, 'tags' => ['insects', 'kings', 'politics', 'mature']],
    ['franchise' => 'super_sentai', 'name' => 'Bakuage Sentai Boonboomger', 'era' => 'Reiwa', 'year' => 2024, 'episodes' => 48, 'tags' => ['cars', 'delivery', 'road_trip']],
    ['franchise' => 'super_sentai', 'name' => 'No.1 Sentai Gozyuger', 'era' => 'Reiwa', 'year' => 2025, 'episodes' => 48, 'tags' => ['no1', 'wedding', 'ongoing']],
    
    // ============================================
    // ULTRAMAN - SHOWA ERA (1966-1989)
    // ============================================
    ['franchise' => 'ultraman', 'name' => 'Ultraman', 'era' => 'Showa', 'year' => 1966, 'episodes' => 39, 'tags' => ['first', 'classic', 'hayata']],
    ['franchise' => 'ultraman', 'name' => 'Ultra Seven', 'era' => 'Showa', 'year' => 1967, 'episodes' => 49, 'tags' => ['classic', 'dan', 'masterpiece']],
    ['franchise' => 'ultraman', 'name' => 'Return of Ultraman', 'era' => 'Showa', 'year' => 1971, 'episodes' => 51, 'tags' => ['classic', 'go']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Ace', 'era' => 'Showa', 'year' => 1972, 'episodes' => 52, 'tags' => ['classic', 'host_swap']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Taro', 'era' => 'Showa', 'year' => 1973, 'episodes' => 53, 'tags' => ['classic', 'family', 'kotaro']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Leo', 'era' => 'Showa', 'year' => 1974, 'episodes' => 51, 'tags' => ['classic', 'martial_arts', 'dark', 'training']],
    ['franchise' => 'ultraman', 'name' => 'The Ultraman', 'era' => 'Showa', 'year' => 1979, 'episodes' => 50, 'tags' => ['anime', 'joneus']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman 80', 'era' => 'Showa', 'year' => 1980, 'episodes' => 50, 'tags' => ['classic', 'teacher', 'takeshi']],
    
    // ============================================
    // ULTRAMAN - HEISEI/NEW GENERATION (1996-2020)
    // ============================================
    ['franchise' => 'ultraman', 'name' => 'Ultraman Tiga', 'era' => 'Heisei', 'year' => 1996, 'episodes' => 52, 'tags' => ['revival', 'mystery', 'daigo', 'masterpiece']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Dyna', 'era' => 'Heisei', 'year' => 1997, 'episodes' => 51, 'tags' => ['space', 'asuka', 'sequel']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Gaia', 'era' => 'Heisei', 'year' => 1998, 'episodes' => 51, 'tags' => ['dual_hero', 'earth', 'gamu', 'masterpiece']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Cosmos', 'era' => 'Heisei', 'year' => 2001, 'episodes' => 65, 'tags' => ['peaceful', 'muscashi', 'long']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Nexus', 'era' => 'Heisei', 'year' => 2004, 'episodes' => 37, 'tags' => ['dark', 'mature', 'continuous', 'horror']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Max', 'era' => 'Heisei', 'year' => 2005, 'episodes' => 39, 'tags' => ['tribute', 'kaito']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Mebius', 'era' => 'Heisei', 'year' => 2006, 'episodes' => 50, 'tags' => ['anniversary', 'mirai', 'tribute', 'guards']],
    ['franchise' => 'ultraman', 'name' => 'Ultraseven X', 'era' => 'Heisei', 'year' => 2007, 'episodes' => 12, 'tags' => ['dark', 'mature', 'short', 'alternate']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Ginga', 'era' => 'New Generation', 'year' => 2013, 'episodes' => 11, 'tags' => ['new_gen', 'spark_dolls', 'hikaru']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Ginga S', 'era' => 'New Generation', 'year' => 2014, 'episodes' => 16, 'tags' => ['new_gen', 'victory', 'sho']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman X', 'era' => 'New Generation', 'year' => 2015, 'episodes' => 22, 'tags' => ['new_gen', 'cyber', 'daichi']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Orb', 'era' => 'New Generation', 'year' => 2016, 'episodes' => 25, 'tags' => ['new_gen', 'cards', 'fusion', 'gai']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Geed', 'era' => 'New Generation', 'year' => 2017, 'episodes' => 25, 'tags' => ['new_gen', 'villain_son', 'rise', 'leito']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman R/B', 'era' => 'New Generation', 'year' => 2018, 'episodes' => 25, 'tags' => ['new_gen', 'brothers', 'family', 'isami']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Taiga', 'era' => 'New Generation', 'year' => 2019, 'episodes' => 26, 'tags' => ['new_gen', 'taro_son', 'tri_squad', 'hiroyuki']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Z', 'era' => 'Reiwa', 'year' => 2020, 'episodes' => 25, 'tags' => ['reiwa', 'student', 'ultraman_zero', 'haruki']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Trigger', 'era' => 'Reiwa', 'year' => 2021, 'episodes' => 25, 'tags' => ['reiwa', 'tiga_tribute', 'ancient', 'kengo']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Decker', 'era' => 'Reiwa', 'year' => 2022, 'episodes' => 25, 'tags' => ['reiwa', 'dyna_tribute', 'future', 'kanata']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Blazar', 'era' => 'Reiwa', 'year' => 2023, 'episodes' => 25, 'tags' => ['reiwa', 'mature', 'defense_force', 'gento']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Arc', 'era' => 'Reiwa', 'year' => 2024, 'episodes' => 25, 'tags' => ['reiwa', 'imagination', 'skip']],
    ['franchise' => 'ultraman', 'name' => 'Ultraman Omega', 'era' => 'Reiwa', 'year' => 2025, 'episodes' => 25, 'tags' => ['reiwa', 'ongoing']],
    
    // ============================================
    // METAL HERO SERIES (1982-1998)
    // ============================================
    ['franchise' => 'metal_hero', 'name' => 'Space Sheriff Gavan', 'era' => 'Showa', 'year' => 1982, 'episodes' => 44, 'tags' => ['first', 'space', 'retsu']],
    ['franchise' => 'metal_hero', 'name' => 'Space Sheriff Sharivan', 'era' => 'Showa', 'year' => 1983, 'episodes' => 51, 'tags' => ['space', 'den']],
    ['franchise' => 'metal_hero', 'name' => 'Space Sheriff Shaider', 'era' => 'Showa', 'year' => 1984, 'episodes' => 49, 'tags' => ['space', 'dai']],
    ['franchise' => 'metal_hero', 'name' => 'Kyojuu Tokusou Juspion', 'era' => 'Showa', 'year' => 1985, 'episodes' => 46, 'tags' => ['space', 'giant_monsters']],
    ['franchise' => 'metal_hero', 'name' => 'Jikuu Senshi Spielban', 'era' => 'Showa', 'year' => 1986, 'episodes' => 44, 'tags' => ['time', 'space']],
    ['franchise' => 'metal_hero', 'name' => 'Choujinki Metalder', 'era' => 'Showa', 'year' => 1987, 'episodes' => 39, 'tags' => ['dark', 'war', 'nazis']],
    ['franchise' => 'metal_hero', 'name' => 'Sekai Ninja Sen Jiraiya', 'era' => 'Showa', 'year' => 1988, 'episodes' => 50, 'tags' => ['ninja', 'family']],
    ['franchise' => 'metal_hero', 'name' => 'Kidou Keiji Jiban', 'era' => 'Showa', 'year' => 1989, 'episodes' => 52, 'tags' => ['police', 'cyborg']],
    ['franchise' => 'metal_hero', 'name' => 'Tokkei Winspector', 'era' => 'Heisei', 'year' => 1990, 'episodes' => 49, 'tags' => ['rescue', 'police']],
    ['franchise' => 'metal_hero', 'name' => 'Tokkyuu Shirei Solbrain', 'era' => 'Heisei', 'year' => 1991, 'episodes' => 53, 'tags' => ['rescue', 'police']],
    ['franchise' => 'metal_hero', 'name' => 'Tokusou Exceedraft', 'era' => 'Heisei', 'year' => 1992, 'episodes' => 49, 'tags' => ['rescue', 'police']],
    ['franchise' => 'metal_hero', 'name' => 'Tokusou Robo Janperson', 'era' => 'Heisei', 'year' => 1993, 'episodes' => 50, 'tags' => ['robot', 'police']],
    ['franchise' => 'metal_hero', 'name' => 'Blue SWAT', 'era' => 'Heisei', 'year' => 1994, 'episodes' => 51, 'tags' => ['police', 'darker', 'aliens']],
    ['franchise' => 'metal_hero', 'name' => 'Juukou B-Fighter', 'era' => 'Heisei', 'year' => 1995, 'episodes' => 53, 'tags' => ['insects', 'beetles']],
    ['franchise' => 'metal_hero', 'name' => 'B-Fighter Kabuto', 'era' => 'Heisei', 'year' => 1996, 'episodes' => 50, 'tags' => ['insects', 'beetles']],
    ['franchise' => 'metal_hero', 'name' => 'Beetle Fighter Kabuto', 'era' => 'Heisei', 'year' => 1996, 'episodes' => 50, 'tags' => ['us_adaptation']],
    ['franchise' => 'metal_hero', 'name' => 'B-Robo Kabutack', 'era' => 'Heisei', 'year' => 1997, 'episodes' => 52, 'tags' => ['comedy', 'robots']],
    ['franchise' => 'metal_hero', 'name' => 'Tetsuwan Tantei Robotack', 'era' => 'Heisei', 'year' => 1998, 'episodes' => 45, 'tags' => ['comedy', 'robots', 'detective']],
    
    // ============================================
    // GARO SERIES (2005-)
    // ============================================
    ['franchise' => 'garo', 'name' => 'GARO', 'era' => 'Heisei', 'year' => 2005, 'episodes' => 25, 'tags' => ['mature', 'horror', 'knights', 'kouga']],
    ['franchise' => 'garo', 'name' => 'GARO: Beast of the White Night', 'era' => 'Heisei', 'year' => 2006, 'episodes' => 2, 'tags' => ['special', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Red Requiem', 'era' => 'Heisei', 'year' => 2010, 'episodes' => 1, 'tags' => ['movie', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Makai Senki', 'era' => 'Heisei', 'year' => 2011, 'episodes' => 24, 'tags' => ['mature', 'sequel', 'kouga']],
    ['franchise' => 'garo', 'name' => 'GARO: Soukoku no Maryu', 'era' => 'Heisei', 'year' => 2013, 'episodes' => 1, 'tags' => ['movie', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Yami wo Terasu Mono', 'era' => 'Heisei', 'year' => 2013, 'episodes' => 25, 'tags' => ['mature', 'new_cast', 'ryuga']],
    ['franchise' => 'garo', 'name' => 'GARO: Makai no Hana', 'era' => 'Heisei', 'year' => 2014, 'episodes' => 25, 'tags' => ['mature', 'new_cast', 'raiga']],
    ['franchise' => 'garo', 'name' => 'GARO: Gold Storm Sho', 'era' => 'Heisei', 'year' => 2015, 'episodes' => 23, 'tags' => ['mature', 'ryuga']],
    ['franchise' => 'garo', 'name' => 'GARO: Ashura', 'era' => 'Heisei', 'year' => 2016, 'episodes' => 1, 'tags' => ['movie', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Kami no Kiba', 'era' => 'Heisei', 'year' => 2018, 'episodes' => 1, 'tags' => ['movie', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Versus Road', 'era' => 'Reiwa', 'year' => 2020, 'episodes' => 12, 'tags' => ['mature', 'new_cast', 'game']],
    ['franchise' => 'garo', 'name' => 'GARO: Hagane wo Tsugu Mono', 'era' => 'Reiwa', 'year' => 2024, 'episodes' => 12, 'tags' => ['mature', 'new_cast']],
    ['franchise' => 'garo', 'name' => 'Zero: Black Blood', 'era' => 'Heisei', 'year' => 2014, 'episodes' => 2, 'tags' => ['spinoff', 'mature', 'zero']],
    ['franchise' => 'garo', 'name' => 'Zero: Dragon Blood', 'era' => 'Heisei', 'year' => 2017, 'episodes' => 13, 'tags' => ['spinoff', 'mature', 'zero']],
    ['franchise' => 'garo', 'name' => 'Kiba: Ankoku Kishi Gaiden', 'era' => 'Heisei', 'year' => 2011, 'episodes' => 1, 'tags' => ['spinoff', 'mature']],
    ['franchise' => 'garo', 'name' => 'GARO: Guren no Tsuki', 'era' => 'Heisei', 'year' => 2015, 'episodes' => 23, 'tags' => ['anime', 'different']],
    ['franchise' => 'garo', 'name' => 'GARO: Vanishing Line', 'era' => 'Heisei', 'year' => 2017, 'episodes' => 24, 'tags' => ['anime', 'different']],
];

// Calculate total episodes (only for enabled franchises)
$enabledSeries = array_filter(FULL_SERIES_DATABASE, fn($s) => isset(FRANCHISES[$s['franchise']]));
define('TOTAL_SERIES_COUNT', count($enabledSeries));
define('TOTAL_EPISODE_COUNT', array_sum(array_column($enabledSeries, 'episodes')));
