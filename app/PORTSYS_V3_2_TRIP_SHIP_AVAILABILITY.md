# PortSys V3.2 - Trip Ship Availability Hotfix

## What changed

- The trip create screen now disables any ship that has an active or future trip overlapping the selected departure/arrival period.
- The trip edit screen uses the same protection while ignoring the trip currently being edited.
- Ship availability is recalculated instantly when the user changes departure date, arrival date, or selected ship.
- If a previously selected ship becomes unavailable after changing dates, the field is cleared and an explanatory message appears.
- Server-side conflict protection remains active in `Trip::scheduleError()`, so a manipulated POST request cannot create an overlapping trip.
- New Arabic/English language keys were added for the availability notice and conflict messages.

## Files changed

- `app/Controllers/Trip.php`
- `app/Views/trips/create.php`
- `app/Views/trips/edit.php`
- `app/Language/ar/App.php`
- `app/Language/en/App.php`

## Database

No migration is required for this hotfix.
