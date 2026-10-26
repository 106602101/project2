# FABWEB — Applied Web Project Part 2

**Database:** `fabweb_db`  
**SQL export:** `fabweb_db.sql`  
**Technology:** PHP 8+, MySQL/MariaDB, HTML, CSS. **No JavaScript.**


## Install on XAMPP / Windows

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Extract this ZIP so the structure is `C:\xampp\htdocs\project2\index.php`, **not** `project2\project2\index.php`.
3. Open `http://localhost/phpmyadmin`.
4. Select the **Import** tab, choose `C:\xampp\htdocs\project2\fabweb_db.sql` and click **Import**. The file creates the `fabweb_db` database and its tables.
5. Confirm that `settings.php` contains `localhost`, user `root`, **empty** password, database `fabweb_db`.
6. Open `http://localhost/project2/` and browse Home, Jobs, Apply and About.
7. Open `http://localhost/project2/login.php` and sign in with **username: `admin` / password: `admin`**. The password is stored as a PHP password hash, not plain text.



## Page routes

| Page | Purpose |
| --- | --- |
| `index.php` | Home page, shared layout, dynamic job count |
| `jobs.php` | SQL-driven open jobs, full descriptions, server-side search |
| `search.php` | Optional backwards-compatible search redirect |
| `apply.php` | EOI form with saved validation errors |
| `process_eoi.php` | POST-only form processing; creates `eoi` table if missing, validates and inserts; displays new EOI number |
| `about.php` | Reads company text and both-project member contributions from database |
| `login.php` | HR login with password verification and lockout throttling |
| `manage.php` | Authentication-required list/filter/sort/status update/delete-by-job |
| `logout.php` | POST logout protected by CSRF token |
| `change_password.php` | Optional password change for post-assessment use |

## Database tables

- `jobs`: job ref, title, summary, salary, employment type, location, reports-to, description, ordered responsibilities, essential and preferable requirements, open flag.
- `eoi`: auto-increment `EOInumber`, form fields, `status ENUM('New','Current','Final') DEFAULT 'New'`, submission timestamp; foreign key to jobs.
- `users`: username and `password_hash` using PHP's `password_hash()`.
- `about_sections`: dynamically rendered company/about content.
- `team_members`: member names, IDs and separate Part 1 / Part 2 contributions.

## Project structure

```text
project2/
  index.php         jobs.php          apply.php        about.php
  process_eoi.php   search.php        manage.php       login.php
  logout.php        change_password.php settings.php  fabweb_db.sql
  includes/
    functions.inc   db.inc            header.inc       nav.inc
    footer.inc
  images/
    logo.svg        hero-cybersecurity.svg
  styles/
    styles.css
    images/
      grid.svg
  README.md
```
