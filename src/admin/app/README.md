# Element Pack — React Admin App

A React 18 SPA that incrementally replaces the legacy PHP admin dashboard. It is
**additive and opt-out**: the classic dashboard (`admin/admin-settings.php`)
remains the default and untouched. This app mounts on a separate
**Dashboard (New)** submenu and talks to the plugin over a dedicated REST API.

## Architecture

```
src/admin/app/
├── index.js            # entry: registers store, mounts createRoot into #ep-admin-app
├── app.js              # HashRouter + AdminLayout + lazy routes (code splitting)
├── api/client.js       # apiFetch wrapper for the element-pack/v1 namespace
├── store/index.js      # @wordpress/data store (thunk actions/resolvers)
├── routes/index.js     # route + sidebar nav definitions (lazy pages)
├── layouts/            # AdminLayout (sidebar + content shell)
├── components/         # Sidebar, Topbar, StatCard, Spinner, ErrorState, EmptyState, UpgradeNotice
├── pages/              # Dashboard, widgets, settings (migrated), ComingSoon (placeholder)
├── hooks/              # useColorScheme (dark-mode-ready)
├── utils/config.js     # bootstrap config from wp_localize_script
└── styles/index.scss   # theme tokens + layout (light/dark via [data-ep-theme])
```

### Server side (PHP)

- `includes/API/` — REST controllers under `element-pack/v1`
  (`dashboard`, `modules`, `settings`, `license`). Every endpoint enforces
  `manage_options` + cookie/nonce auth and reuses existing services
  (`ModuleService`, option helpers) — no business logic is duplicated.
- `includes/Admin/class-dashboard-stats.php` — REST-safe stats service.
- `includes/Admin/class-react-app.php` — registers the submenu, enqueues the
  build, and localizes `elementPackAdmin`.
- `includes/react-admin-init.php` — wiring, required from `loader.php`.

## Build

```bash
npm install            # installs @wordpress/scripts + deps
npm run build:admin    # production build -> build/admin/
npm run start:admin    # dev build + watch
npm run lint:admin     # eslint
npm run format:admin   # prettier
```

Output goes to `build/admin/` (`index.js`, `index.asset.php`, `style-index.css`).
The PHP loader reads `index.asset.php` for dependency handles + cache-busting
version. The frontend asset pipeline (`scripts/build.mjs`, `src/ → assets/`) is
independent and unaffected.

## REST API

| Method | Route                       | Purpose                              |
| ------ | --------------------------- | ------------------------------------ |
| GET    | `element-pack/v1/dashboard` | Widget stats (total/active/used)     |
| GET    | `element-pack/v1/modules`   | Modules per tier + enabled state     |
| POST   | `element-pack/v1/modules`   | Save a tier's on/off map             |
| GET    | `element-pack/v1/settings`  | Settings schema + values + POST update   |
| GET    | `element-pack/v1/permissions` | Permission Manager meta + target data  |
| POST   | `element-pack/v1/permissions` | Save role/subsite widget access        |
| GET    | `element-pack/v1/license`   | License status (read-only)           |

## Disabling

```php
define( 'BDTEP_DISABLE_REACT_ADMIN', true );
// or
add_filter( 'element_pack/admin/react_enabled', '__return_false' );
```
