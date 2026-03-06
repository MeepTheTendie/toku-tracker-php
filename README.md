# Toku Tracker PHP - Complete Edition

A comprehensive tokusatsu episode tracker with **170+ series** covering Kamen Rider, Super Sentai, Ultraman, Metal Hero, and GARO. Built with vanilla PHP and SQLite for maximum simplicity and speed.

## 📊 Database Stats

| Franchise | Series | Episodes |
|-----------|--------|----------|
| 🦗 Kamen Rider | 38 | 2,000+ |
| 🦖 Super Sentai | 49 | 2,400+ |
| ✨ Ultraman | 27 | 900+ |
| 🤖 Metal Hero | 17 | 800+ |
| 🐺 GARO | 15+ | 200+ |
| **Total** | **~170** | **~6,300** |

## ✨ Features

### Core
- **Dashboard** with overall progress and per-franchise breakdown
- **Series browser** with franchise/era/status filters
- **Episode tracking** with visual progress grid
- **"Continue Watching"** recommendations
- **Search** by name, tags, or era
- **Statistics page** with detailed breakdowns

### Series Data Includes
- All 38 Kamen Rider series (Showa through Reiwa)
- All 49 Super Sentai teams
- 27+ Ultraman series (including New Generation)
- 17 Metal Hero shows
- GARO live-action seasons

### Tags for Discovery
Series are tagged with:
- **Themes**: `dinosaurs`, `ninja`, `magic`, `space`, `cyber`, `trains`
- **Tones**: `dark`, `mature`, `comedy`, `violent`
- **Types**: `tribute`, `anniversary`, `movie`, `ongoing`
- **Eras**: `showa`, `heisei`, `reiwa`

## 🚀 Quick Start

```bash
cd toku-tracker-php
./launch.sh
```

Open http://localhost:8080

### Requirements
- PHP 8.0+
- `pdo_sqlite` extension

## 📱 Pages

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/` | Stats, progress ring, continue watching |
| Series | `/series` | Browse all with filters |
| Filtered | `/series?franchise=kamen_rider` | By franchise |
| Watching | `/series?filter=watching` | In progress |
| Search | `/search?q=keyword` | Search by name/tag |
| Detail | `/series-detail?id=1` | Series with episode grid |
| Watch | `/watch?id=1&episode=5` | Episode tracker page |
| Stats | `/stats` | Detailed statistics |

## 🎮 Keyboard Shortcuts (Watch Page)

| Key | Action |
|-----|--------|
| `←` | Previous episode |
| `→` | Next episode |
| `Space` / `W` | Mark watched/unwatched |

## 🔌 API Endpoints

```bash
# Watch/unwatch single episode
POST /api/watch
{
    "series_id": 1,
    "episode": 5,
    "action": "watch" | "unwatch"
}

# Bulk operations
POST /api/bulk
{
    "series_id": 1,
    "action": "watch_all" | "unwatch_all"
}
```

## 🗂️ Project Structure

```
toku-tracker-php/
├── index.php              # Router
├── config.php             # Full 170+ series database
├── launch.sh              # Dev server
├── lib/
│   ├── Database.php       # SQLite setup with indexing
│   └── TokuTracker.php    # Business logic + search
├── templates/
│   ├── dashboard.php      # Stats & progress ring
│   ├── series.php         # Filterable grid
│   ├── series_detail.php  # Episode grid
│   ├── watch.php          # Tracking interface
│   ├── search.php         # Search results
│   └── stats.php          # Detailed stats
├── api/
│   ├── watch.php          # Episode tracking
│   └── bulk.php           # Bulk operations
└── data/                  # SQLite database (auto-created)
```

## 🎯 Search Examples

- `"Kamen Rider"` - All Rider series
- `"dinosaurs"` - Abaranger, Kyoryuger, Ryusoulger
- `"dark"` - Amazon, Shin, Nexus, Metalder
- `"anniversary"` - Decade, Gokaiger, Zenkaiger
- `"Heisei"` - Era filter
- `"tribute"` - Tribute seasons

## 💾 Database

SQLite auto-creates at `data/toku.db` on first run with:
- Series table with tags (JSON)
- Episodes table (auto-generated per series)
- Watched tracking table
- Full-text search via LIKE on tags

### Reset Database

```php
require_once 'lib/Database.php';
Database::reset();
```

## 🌟 Why PHP Works Here

1. **Zero build step** - Instant changes
2. **Single SQLite file** - No database server
3. **170 series = ~0.5MB data** - Loads instantly
4. **Shared-nothing** - Each request isolated
5. **Drop-in deploy** - Any PHP host

Compare to Node/React:
- No `node_modules`
- No webpack/Vite
- No hydration
- No state management complexity

## 📝 Adding Series

Edit `FULL_SERIES_DATABASE` in `config.php`:

```php
['franchise' => 'kamen_rider', 'name' => 'New Rider', 'era' => 'Reiwa', 'year' => 2026, 'episodes' => 50, 'tags' => ['tag1', 'tag2']],
```

Delete `data/toku.db` to regenerate with new data.

## 🎨 Customization

Franchise colors in `FRANCHISES`:
```php
'kamen_rider' => ['color' => '#00a8ff', 'icon' => '🦗'],
'super_sentai' => ['color' => '#e74c3c', 'icon' => '🦖'],
```

## License

MIT - Build your ultimate toku watchlist! 📺⚡
