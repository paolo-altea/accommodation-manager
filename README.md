# Accommodation Manager for Joomla 5/6

Joomla component for managing hotel rooms, categories, rate periods, rate typologies, and rates. Designed for multilingual accommodation websites.

## Requirements

- Joomla 5.x or 6.x
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+

## Installation

Upload `pkg_accommodation_manager-x.x.x.zip` via **System > Install > Extensions**.

The package installs:
- `com_accommodation_manager` — main component
- `mod_accommodation_categories` — categories module
- `mod_accommodation_rooms` — rooms module
- `mod_accommodation_rates` — rates grid module

### Upgrading from Joomla 3

If the legacy `com_accommodation_manager_four` is already installed, the installer automatically:
1. Migrates data (rate values, gallery paths to JSON, removes panorama column)
2. Upgrades database schema (new columns, type changes, indexes)
3. Removes the legacy component without dropping shared tables

## Features

### Backend (Admin)

- **Rooms** — name, multilingual titles/descriptions, thumbnail, floor plan, image gallery (repeatable subform), video, surface, capacity, price-from, CSS class
- **Room Categories** — hierarchical (parent/child), multilingual names/descriptions, category image
- **Rate Periods** — start/end dates, multilingual titles
- **Rate Typologies** — e.g. B&B, half board, full board
- **Rates** — grid editor: rooms x periods x typologies, DECIMAL values, NULL = not available

### Frontend (Site)

Five public views, all read-only:

| View | Menu item | Description |
|------|-----------|-------------|
| **Categories** | `categories` | List or Swiper slider of room categories |
| **Category** | `category` | Rooms filtered by a single category |
| **Rooms** | `rooms` | All rooms, flat or grouped by category |
| **Room** | `room` | Room detail with gallery, floor plan, rates |
| **Rates** | `rates` | Rate grid, optional season grouping (summer/winter) |

### Multilingual

Content is stored in separate columns per language (`*_de`, `*_it`, `*_en`, `*_fr`, `*_es`). The frontend automatically selects the column matching the active Joomla language. Languages can be enabled/disabled individually in component configuration.

### Modules

All three modules share the component's layouts and CSS/JS assets:

- **mod_accommodation_categories** — category cards with configurable title tag, image, description, link button, ordering
- **mod_accommodation_rooms** — room cards with category filter, gallery (Swiper optional), floor plan, action buttons, ordering
- **mod_accommodation_rates** — rate grid with season grouping, reads configuration from the component

### Joomla Layouts

Reusable layout files in `layouts/` for template overrides:

```
layouts/
  room/thumbnail.php      — room thumbnail image
  room/info.php           — surface, people, price-from
  room/floor-plan.php     — floor plan image with heading
  room/gallery.php        — image gallery (Swiper or plain)
  room/actions.php        — request/booking buttons
  room/detail-link.php    — link to room detail
  category/item.php       — category card
  rates/grid.php          — multi-room rate grid
  rates/room-grid.php     — single-room rate grid
```

Override any layout by copying it to `templates/{your_template}/html/layouts/`.

## Configuration

Component Options (**Components > Accommodation Manager > Options**):

| Tab | What it controls |
|-----|-----------------|
| **Component** | Version history, SEF IDs, enabled languages |
| **Categories** | Show/hide image, description, link button; Swiper slider settings |
| **Rooms** | Show/hide each section (surface, people, price, intro, description, floor plan, gallery, video, rates); Swiper settings; category filter; action buttons |
| **Rates** | Hide past periods, season split (summer/winter with configurable start dates) |
| **Links** | Request and booking URLs per language |
| **Permissions** | Joomla ACL |

### CSS/JS Assets

Each view loads its own CSS/JS via Joomla's WebAssetManager. Loading can be disabled per-view in configuration (useful when providing custom styles in the template).

CSS custom properties and JS custom events are documented in the configuration notes for each view.

## Database

Five tables (prefix `#__accommodation_manager_`):

| Table | Purpose |
|-------|---------|
| `rooms` | Room data, multilingual content, images, gallery JSON |
| `room_categories` | Hierarchical categories with parent/child |
| `rate_periods` | Date ranges for rate seasons |
| `rate_typologies` | Rate types (B&B, half board, etc.) |
| `rates` | Price grid: room_id x period_id x typology_id |

## Build

From the project root:

```bash
cd /path/to/accomodation_manager
bash build/build.sh
```

The script reads the version from `accommodation_manager.xml` and `pkg_accommodation_manager.xml`, then builds all ZIPs into `dist/`:

```
dist/
  com_accommodation_manager-3.5.3.zip
  mod_accommodation_categories-1.0.0.zip
  mod_accommodation_rooms-1.0.0.zip
  mod_accommodation_rates-1.0.0.zip
  pkg_accommodation_manager-3.5.3.zip   ← install this one
```

Upload `pkg_accommodation_manager-*.zip` to Joomla. It installs the component and all three modules in one step.

## Public API

The component exposes static methods to access room and category data from articles, templates, plugins, or external PHP scripts. See [docs/API.md](docs/API.md) for usage examples.

## Adding a New Language

See [docs/ADD-LANGUAGE.md](docs/ADD-LANGUAGE.md) for the complete checklist.

## License

GNU General Public License v2 or later.

## Author

[Altea Software Srl](https://www.altea.it) — web@altea.it
