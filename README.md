# Database Management Web Application

A PHP and MySQL web application for managing a professional resume portfolio. Users can register, log in, update personal details, upload profile and certification content, generate a resume, and download it as a Microsoft Word document.

## Features

- User registration and login
- Secure password storage using MD5 hashing
- Profile updates and profile image uploads
- Portfolio, certifications, projects, skills, education, and experience management
- Resume generation and Word-document download
- Responsive interface built with HTML, CSS, and PHP

## Technology Stack

- PHP 7.4 or newer
- MySQL or MariaDB
- Apache or another PHP-compatible web server
- Bootstrap-free custom CSS

## Project Structure

- `config.php` — database connection settings
- `register.php` — user registration
- `login.php` — user authentication
- `home.php` — authenticated user dashboard
- `update_profile.php` — profile and portfolio updates
- `generate_resume.php` — resume presentation
- `resume_download.php` — Word document download
- `display.php` — user profile display
- `delete.php` — profile deletion
- `css/style.css` — shared styles
- `images/` — public image assets
- `uploaded_img/` — uploaded user profile images
- `uploaded_certifications/` — uploaded certification files
- `uploaded_portfolio/` — uploaded portfolio files
- `mysql databse table.txt` — MySQL schema instructions

## Setup

1. Place the project in your PHP web server document root, such as `htdocs/database-management-web-application`.
2. Start Apache and MySQL.
3. Create a MySQL database named `user_db`.
4. Import or run the SQL from `mysql databse table.txt`.
5. Update `config.php` with your local database host, username, password, and database name if required.
6. Ensure the following directories are writable by the web server:
   - `uploaded_img/`
   - `uploaded_certifications/`
   - `uploaded_portfolio/`
7. Open the application in your browser, for example:

   ```text
   http://localhost/database-management-web-application/login.php
   ```

## Database Schema

The application uses the `user6_form` table. Its schema includes user account details, professional information, uploaded file references, and timestamp fields.

## Important Security Notice

This project uses MD5 for password hashing. MD5 is no longer considered secure for password storage. It should be replaced with a password hashing function such as `password_hash()` and `password_verify()` before production use.

The application should also be hardened before deployment by using parameterized queries, validating and sanitizing all user input, restricting uploads, enabling HTTPS, and configuring an appropriate server-side security policy.

## License

This project is provided for educational and personal use. No license is currently specified.

## Contributing

Contributions are welcome. Open an issue or submit a pull request with a clear description of the change and any necessary database migration notes.
