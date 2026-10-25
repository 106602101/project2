# FABWEB — Applied Web Project Part 2

**Submission folder:** `project2/` (case-sensitive)  
**Database:** `fabweb_db`  
**SQL export:** `fabweb_db.sql`  
**Technology:** PHP 8+, MySQL/MariaDB, HTML, CSS. **No JavaScript.**

This is a self-contained implementation based on the public FABWEB project structure at https://github.com/106602101/project2. The existing GitHub repository could be inspected but not directly cloned into the build environment. Therefore this package uses reconstructed styles and replacement SVG assets; it is **not** a byte-for-byte copy of the original project. Compare the seeded job descriptions, form fields, and group member contributions against your Part 1 website before submitting.

## Install on XAMPP / Windows

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Extract this ZIP so the structure is `C:\xampp\htdocs\project2\index.php`, **not** `project2\project2\index.php`.
3. Open `http://localhost/phpmyadmin`.
4. Select the **Import** tab, choose `C:\xampp\htdocs\project2\fabweb_db.sql` and click **Import**. The file creates the `fabweb_db` database and its tables.
5. Confirm that `settings.php` contains `localhost`, user `root`, **empty** password, database `fabweb_db`.
6. Open `http://localhost/project2/` and browse Home, Jobs, Apply and About.
7. Open `http://localhost/project2/login.php` and sign in with **username: `admin` / password: `admin`**. The password is stored as a PHP password hash, not plain text.

### Important content to update before submission

- **`team_members`** in phpMyAdmin: replace both sample names, student IDs and contributions with the **real members** and accurate work in **Part 1** and **Part 2**. The names are deliberately not fabricated.
- **`jobs`**: the four sample FABWEB cyber jobs are seeded for the complete working site. Check and update every field against the exact Part 1 job descriptions (salary, reporting line, employment, responsibilities, essential/preferable criteria). It is all loaded dynamically, so changes in MySQL appear automatically.
- Verify the Part 1 application form fields against the form in `apply.php`. The current form uses standard EOI fields including job reference, full name, birth date, gender, address, state, postcode, email, phone and skill selections.
- Site logo and hero graphic are self-contained SVG replacements. If keeping the original Part 1 visual design matters, copy your original site assets and reconcile CSS.

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

## Assessment demonstration / manual test checklist

1. Verify every internal link stays under `http://localhost/project2/`; all links to pages and assets use relative URLs.
2. Open `process_eoi.php` directly using GET: it redirects to `apply.php` without inserting anything.
3. Submit a complete EOI and record the automatically generated `EOInumber`. In phpMyAdmin, check it is saved in `eoi` with status **New**.
4. Try blank names, invalid DOB, 3-digit postcode, invalid email, incorrect phone, unselected skills, malformed job ref, invalid job ref: server displays errors even with browser validation switched off.
5. Search jobs by `cloud`, `FW1002`, etc. and confirm results are retrieved via SQL.
6. Access `manage.php` logged out: it redirects to login. Log in with `admin` / `admin`.
7. List all EOIs, filter by job ref, first name, last name and both names; sort by all provided fields (ascending/descending).
8. Update one EOI status to **Current**, then **Final**; confirm the stored value in phpMyAdmin.
9. Delete EOIs for a chosen job reference using the confirmation checkbox. Confirm other job references remain.
10. Edit an `about_sections` row and a `team_members` contribution in phpMyAdmin; reload `about.php` and observe the change.
11. Recheck that there is no `<script>` tag or JavaScript dependence anywhere in the site.

## Security notes

- MySQL credentials live in one location: `settings.php`, with a deliberately empty local DB password as specified.
- All SQL **data** queries use prepared statements; SQL sort field and direction are chosen from explicit allowlists.
- User input is stripped of dangerous tags/control characters, validated again in PHP, and HTML-escaped on output.
- Destructive actions, logins and EOI submissions use CSRF tokens. Login regenerates the session ID and throttles repeated wrong passwords.
- Default **admin/admin** credentials are included **only** to satisfy marker access. Do not deploy to the public Internet using this account. After marking, use `change_password.php` to set a stronger secret.
- For a real production deployment, configure HTTPS, do not use the MySQL root account, and add privacy and retention controls for applicants' personal data.

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

You can edit all page files using VS Code or another plain-text editor.
