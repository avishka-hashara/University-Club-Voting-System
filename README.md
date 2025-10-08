# Club Voting System (XAMPP + PHP + MySQL)

## Overview
A simple university club election system built with PHP 7.4+ (PDO), MySQL, and Bootstrap 5. Supports Admins (club admins) and Voters. Drop into XAMPP `htdocs` and import database.

## Steps to deploy (exact)
1. Copy the directory `club_voting/` into XAMPP `htdocs`:
   - Example: `C:\xampp\htdocs\club_voting\`

2. Ensure `uploads/` is writable:
   - On Windows with XAMPP this is usually fine. On Linux: `chmod 775 uploads` and ensure Apache user owns it.

3. Start XAMPP Apache & MySQL services.

4. Import database:
   - Open phpMyAdmin -> http://localhost/phpmyadmin
   - Click "Import" -> Choose file `database.sql` (located in project root) -> Go.
   - This creates database `club_voting` and sample data.

5. Open the app:
   - http://localhost/club_voting/

6. Default admin test account:
   - Admin: `adminvotekdu@kdu.ac.lk` / `hashara@123`

7. You can register as voters

## Notes & Config
- DB credentials in `config.php`.
- Candidate photos uploaded to `uploads/`.
- CSRF protection implemented for forms (see helpers.php).
- Passwords use `password_hash()` and `password_verify()`.

## Quick start (3 steps)
1. Place `club_voting/` into `C:\xampp\htdocs\`.
2. Import `database.sql'.
3. Open `http://localhost/club_voting/`, register or use test accounts.

## Additional setup (optional)
- Configure cron to auto-close elections (see bottom of README for instructions).
