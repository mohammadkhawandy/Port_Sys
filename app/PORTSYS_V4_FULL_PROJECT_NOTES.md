# PortSys Full Project Update - 2026-08-21

- Replaced the application logo with the uploaded PortSys identity in public, auth, admin, manifest, and favicon assets.
- Fixed runtime baseURL detection to prevent accidental routing under `portsys-assets`, which was causing the logo image to open instead of completing login/actions.
- Added asset-route safeguards for misdirected POST/document requests.
- Updated the About page team to: Mohammad Al-Khawandi (193347) and Lana Al-Hatem (205886).
- Cleaned and validated the project statically.
