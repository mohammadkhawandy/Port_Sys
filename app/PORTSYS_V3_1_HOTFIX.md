# PortSys V3.1 Hotfix

## Fixed

- Fixed the trip creation form on `index.php/trips/create`.
- Added a direct POST route for `trips/create`, while keeping the old `POST /trips` route for compatibility.
- Changed the trip form action to post to the same create endpoint to avoid cached or misrouted form submissions.
- Added a loading state to the trip create button so repeated clicks do not duplicate requests.
- Fixed the runtime base URL detection for XAMPP/subfolder installs. The project no longer forces `http://localhost:8080/`, which was the main reason internal form actions could jump to the wrong URL or asset path.
- Added the missing `saving` translation key in Arabic and English.

## Files changed

- `Config/App.php`
- `Config/Routes.php`
- `Views/trips/create.php`
- `Language/ar/App.php`
- `Language/en/App.php`

## Check

- PHP syntax check passed for all application PHP files.
