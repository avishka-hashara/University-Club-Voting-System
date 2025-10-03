<<<<<<< HEAD
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
   - Click "Import" -> Choose file `db.sql` (located in project root) -> Go.
   - This creates database `club_voting` and sample data.

5. Open the app:
   - http://localhost/club_voting/

6. Default test accounts (change passwords after login):
   - Admin: `admin@university.edu` / `AdminPass123`
   - Voter1: `voter1@university.edu` / `VoterPass1`
   - Voter2: `voter2@university.edu` / `VoterPass2`

## Notes & Config
- DB credentials in `config.php`.
- Candidate photos uploaded to `uploads/`.
- CSRF protection implemented for forms (see helpers.php).
- Passwords use `password_hash()` and `password_verify()`.

## Quick start (3 steps)
1. Place `club_voting/` into `C:\xampp\htdocs\`.
2. Import `db.sql` via phpMyAdmin.
3. Open `http://localhost/club_voting/`, register or use test accounts.

## Additional setup (optional)
- Configure cron to auto-close elections (see bottom of README for instructions).
=======
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
   - Click "Import" -> Choose file `db.sql` (located in project root) -> Go.
   - This creates database `club_voting` and sample data.

5. Open the app:
   - http://localhost/club_voting/

6. Default test accounts (change passwords after login):
   - Admin: `admin@university.edu` / `AdminPass123`
   - Voter1: `voter1@university.edu` / `VoterPass1`
   - Voter2: `voter2@university.edu` / `VoterPass2`

## Notes & Config
- DB credentials in `config.php`.
- Candidate photos uploaded to `uploads/`.
- CSRF protection implemented for forms (see helpers.php).
- Passwords use `password_hash()` and `password_verify()`.

## Quick start (3 steps)
1. Place `club_voting/` into `C:\xampp\htdocs\`.
2. Import `db.sql` via phpMyAdmin.
3. Open `http://localhost/club_voting/`, register or use test accounts.

## Additional setup (optional)
- Configure cron to auto-close elections (see bottom of README for instructions).
>>>>>>> 2ba3267 (Initial commit)
