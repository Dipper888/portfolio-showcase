# Student Portfolio & FYP Showcase System

This is a web-based system developed for **DFP40443 Full Stack Web Development (Mini Project 2)**. It provides a place for students to submit their portfolio or Final Year Project (FYP) details and for admins to manage the categories and check student submissions.

The system has two user roles: **Student** and **Admin**. Each role has its own dashboard and functions.

## Contents

- [Main Features](#main-features)
- [Technologies Used](#technologies-used)
- [System Requirements](#system-requirements)
- [Installation and Setup](#installation-and-setup)
- [How to Use the System](#how-to-use-the-system)
- [Database Structure](#database-structure)
- [Project Folder Structure](#project-folder-structure)
- [Validation, Search and File Upload](#validation-search-and-file-upload)
- [Basic Testing Checklist](#basic-testing-checklist)
- [Notes](#notes)

## Main Features

### Student

- Register an account and log in to the system.
- View the Project Showcase Directory containing student portfolio submissions.
- Search projects by project title, technology stack or student name without refreshing the page.
- Submit a project with its title, category, description, technology stack and documentation file.
- View submitted projects on the **My Portfolio** page.
- Download documentation files from their own submissions.

### Admin

- Log in and access the Admin Dashboard.
- View the total number of students, categories and project submissions.
- View recent project submissions.
- Add and view project categories.
- View all student submissions, including student information and project details.
- Download documentation files submitted by students.

## Technologies Used

| Technology | Used for |
| --- | --- |
| HTML | Page structure and forms |
| CSS | Additional styling shared across pages |
| Bootstrap 5 | Responsive layout, navbar, forms, buttons, tables and alerts |
| JavaScript | Checking form inputs before submission |
| AJAX (`fetch`) | Live search without reloading the whole dashboard |
| PHP | Login, sessions, server-side validation and database operations |
| MySQL | Storing users, categories and project information |
| Laragon | Running the PHP website and MySQL locally |

## System Requirements

- Laragon with Apache and MySQL running.
- PHP 8.1 or a compatible newer version, with MySQLi (including mysqlnd) and Fileinfo enabled.
- MySQL 8.0 or compatible MySQL server. The supplied SQL export uses the `utf8mb4_0900_ai_ci` collation.
- A browser such as Chrome, Edge or Firefox.
- A code editor such as Visual Studio Code (for changing the source files).

## Installation and Setup

### 1. Download the source code

Download the repository from GitHub using **Code > Download ZIP**, or clone it using Git.

Extract the folder and rename the main system folder to `portfolio-showcase`.

### 2. Move the folder into Laragon

Place the system folder here:

```text
C:\laragon\www\portfolio-showcase\
```

Make sure `index.php`, `portfolio_db.sql`, `validation.js` and `styles.css` are inside this folder, not inside another nested copy of it.

### 3. Start Laragon

Open Laragon and start **Apache** and **MySQL**. Wait until both services are running.

### 4. Create and import the database

1. Open phpMyAdmin or another MySQL database manager connected to Laragon.
2. Create a new database named **`portfolio_db`**.
3. Select the new database.
4. Open the **Import** tab.
5. Choose the `portfolio_db.sql` file from the main system folder.
6. Run the import.
7. Confirm that `users`, `categories` and `projects` are created.

### 5. Check the database connection

Open `includes/db.php`. The local settings currently used by the system are:

```php
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "portfolio_db";
```

Only change these values if your own Laragon/MySQL setup uses different credentials.

### 6. Check the uploads folder

Make sure this folder exists:

```text
portfolio-showcase/uploads/
```

PHP needs to be able to write uploaded files into this folder. Git does not keep an empty directory by itself, so create `uploads` manually if it is missing after cloning the repository.

### 7. Open the website

Type this address in your browser:

```text
http://localhost/portfolio-showcase/
```

The system starts from `index.php` and redirects users to the Login page if they are not logged in.

## How to Use the System

### First-time setup

1. Open **Register** and create a normal account. New accounts are given the **Student** role automatically.
2. To prepare an Admin account for local testing, register another account first. Then open the `users` table in your database manager and change that account's `role` from `student` to `admin`.
3. Log in with the Admin account and add at least one category under **Categories**.
4. Log in with a Student account to submit a project using the categories created by Admin.

### Student workflow

1. Register or log in using a Student account.
2. Open **Dashboard** to view the Project Showcase Directory.
3. Type a keyword into the search box to filter projects.
4. Select **Submit Project** and enter the title, category, tech stack, description and documentation file.
5. Submit the form. If the PHP checks pass, the file is saved and the project details are inserted into MySQL.
6. Open **My Portfolio** to view your own submissions and use **Download** to get your uploaded document.

### Admin workflow

1. Log in using an account with the `admin` role.
2. Open **Admin Dashboard** to view the system totals and recent submissions.
3. Open **Categories** to add, view or delete project categories.
4. Open **All Submissions** to view projects submitted by students and download their documentation files.

## Database Structure

The system uses the `portfolio_db` database with three tables:

| Table | Important columns | Purpose |
| --- | --- | --- |
| `users` | `id`, `full_name`, `email`, `password`, `role`, `created_at` | Stores Student and Admin accounts |
| `categories` | `id`, `category_name`, `description`, `created_at` | Stores categories created by Admin |
| `projects` | `id`, `user_id`, `category_id`, `title`, `description`, `tech_stack`, `file_path`, `created_at` | Stores submitted project details and the uploaded file path |

**Relationships:**

- One user can submit many projects: `projects.user_id` refers to `users.id`.
- One category can be used by many projects: `projects.category_id` refers to `categories.id`.
- The `email` in `users` and `category_name` in `categories` are unique.
- Passwords are stored as hashes, not plain text.

The actual documentation files are saved in `uploads/`. The `projects` table stores their file paths, not the document contents.

## Project Folder Structure

```text
portfolio-showcase/
├── admin/
│   ├── dashboard.php         # Admin statistics and recent submissions
│   ├── categories.php        # Add, view and delete categories
│   └── submissions.php       # View all student submissions
├── student/
│   ├── dashboard.php         # Project directory and AJAX search response
│   ├── submit_project.php    # Student project submission form
│   └── my_projects.php       # Logged-in student's submissions
├── includes/
│   ├── db.php                # MySQL connection
│   ├── header.php            # Shared navbar and role checking
│   └── footer.php            # Shared footer and Bootstrap JavaScript
├── uploads/                  # Submitted documentation files
│   └── .gitkeep              # Keep the folder in Git
├── .gitignore                # Ignore local or generated files
├── index.php                 # Redirect based on login role
├── register.php              # Student registration
├── login.php                 # Login and session setup
├── logout.php                # End login session
├── download.php              # Check permission and download a file
├── validation.js             # Client-side validation and AJAX live search
├── styles.css                # Shared custom CSS
├── portfolio_db.sql          # Database tables and relationships
└── README.md                 # Setup guide and system information
```

There is **no separate `search.php` or `search.js`**. The search feature has been combined into `student/dashboard.php` and `validation.js`.

## Validation, Search and File Upload

### Form validation

The Registration, Login, Category and Submit Project forms use two checks:

1. **JavaScript (client-side):** checks for empty input fields. If any field is empty, the form is stopped and a Bootstrap error message is shown.
2. **PHP (server-side):** checks for empty fields again before processing data, even if JavaScript is disabled. Empty submissions are rejected without repeating the same error message. PHP also checks the other form rules, such as email format, passwords, existing categories and file upload restrictions.

PHP displays Bootstrap error messages for other validation failures and success messages after a successful operation.

PHP also uses prepared statements for database queries and `password_hash()` / `password_verify()` for account passwords.

### AJAX live search

The search box on the Student Dashboard uses JavaScript `fetch()` to request:

```text
student/dashboard.php?ajax=1&keyword=your_keyword
```

The PHP page searches project title, tech stack and student name using a prepared SQL query. It returns matching project cards, which JavaScript displays on the same Dashboard without reloading the full page.

### Project file upload and download

- Supported file types: **PDF, DOCX, TXT and ZIP**.
- Maximum file size: **5 MB**.
- PHP checks the submitted fields, selected category, file extension, file type and file size before saving.
- Uploaded documents are stored in `uploads/`, and the saved file path is recorded in `projects`.
- Students can download documents from their own submissions. Admins can download student submissions through `download.php`.

## Basic Testing Checklist

Use these checks after setting up the full system:

- [ ] Register a new Student account.
- [ ] Log in and log out with the Student account.
- [ ] Log in with an Admin account and check the dashboard totals.
- [ ] Add a category and confirm it appears in the category list.
- [ ] Submit a project with a supported document file under 5 MB.
- [ ] Check that the project is listed in **My Portfolio**.
- [ ] Check that Admin can see the submission under **All Submissions**.
- [ ] Download an uploaded file using the allowed account.
- [ ] Try an invalid form input and an unsupported/oversized file.
- [ ] Search by title, tech stack and student name without a page reload.
- [ ] Check that a Student cannot open Admin pages.

This checklist is for manual testing. It does not mean all tests have already passed.

## Notes

- This version is prepared for local use with Laragon and has not been confirmed for public hosting.
- Edit and Delete functions are not included on the current Student Portfolio page. Admin category deletion is a separate feature in the Categories page.
- For a real online deployment, direct web access to files in `uploads/` must be restricted so users cannot bypass the permissions in `download.php`.
- If the website cannot connect to MySQL, check that MySQL is running and that the details in `includes/db.php` match your local setup.
- If an uploaded file fails, check the `uploads` folder and PHP upload limits (`upload_max_filesize` and `post_max_size`).
- If Bootstrap styling or icons do not load, check your internet connection because the website currently uses CDN links.