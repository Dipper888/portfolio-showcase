# Student Portfolio & FYP Showcase System

This is a web-based system for **DFP40443 Full Stack Web Development (Mini Project 2)**. Students can submit their portfolio and Final Year Project (FYP) details, while Admin can manage categories and view the submitted projects.

There are two user roles: **Student** and **Admin**.

## Table of Contents

- [Main Features](#main-features)
- [Technologies Used](#technologies-used)
- [System Requirements](#system-requirements)
- [Folder Structure](#folder-structure)
- [Installation and Setup](#installation-and-setup)
- [How to Use](#how-to-use)
- [Database Structure](#database-structure)
- [Validation, Search and Documents](#validation-search-and-documents)
- [Testing Checklist](#testing-checklist)
- [Notes](#notes)

## Main Features

### Student

- Register and log in to a student account.
- View student projects in the **Project Showcase Directory**.
- Search by project title, tech stack or student name without refreshing the page.
- Submit a project with its title, category, tech stack, description and document.
- View their own submissions in **My Portfolio**.
- Edit project details using the same form as **Submit Project**.
- Keep the existing document when editing, or upload a new file to replace it.
- Delete their own project and uploaded document.
- Download the document from their own submissions.

### Admin

- Log in and view the **Admin Dashboard** with system totals and recent submissions.
- Add, edit and delete project categories.
- Prevent deleting a category that is already used by a project.
- View all student projects in **All Submissions**.
- Download student project documents.

## Technologies Used

| Technology | Purpose |
| --- | --- |
| HTML and CSS | Page content and styling |
| Bootstrap 5 and Bootstrap Icons | Responsive pages, forms, cards, tables and icons |
| JavaScript | Check empty form fields before submitting |
| AJAX (`fetch`) | Live search without refreshing the dashboard |
| PHP | Login, sessions, form processing and file handling |
| MySQL | Store users, categories and project information |
| Laragon | Run the system locally with Apache and MySQL |

## System Requirements

- Laragon with Apache and MySQL.
-  MySQL 8.0, PHP 8.1 or compatible newer version with MySQLi and Fileinfo enabled.
- A web browser such as Chrome, Edge or Firefox.
- Internet connection for Bootstrap and Bootstrap Icons CDN files.

## Folder Structure

```text
portfolio-showcase/
├── admin/
│   ├── categories.php
│   ├── dashboard.php
│   └── submissions.php
|
├── includes/
│   ├── db.php
│   ├── footer.php
│   └── header.php
|
├── student/
│   ├── dashboard.php
│   ├── my_projects.php
│   └── submit_project.php
|
├── uploads/
│
├── download.php
├── index.php
├── register.php
├── login.php
├── logout.php
├── portfolio_db.sql
├── validation.js
├── styles.css
└── README.md
```

**Main files:**

| File | What it does |
| --- | --- |
| `register.php` | Register a new Student account |
| `login.php` / `logout.php` | Log in and log out |
| `index.php` | Redirect users based on their role |
| `includes/db.php` | Connect to the MySQL database |
| `includes/header.php` | Shared navigation and user access checks |
| `includes/footer.php` | Shared footer and Bootstrap scripts |
| `student/dashboard.php` | Project directory and AJAX search |
| `student/submit_project.php` | Submit a new project or edit an existing project |
| `student/my_projects.php` | View, edit, delete and download own submissions |
| `admin/dashboard.php` | Show totals and recent submissions |
| `admin/categories.php` | Add, edit and delete categories |
| `admin/submissions.php` | View all submitted projects |
| `download.php` | Check file download permission |
| `validation.js` | JavaScript empty-field checking and AJAX search |
| `styles.css` | Custom CSS loaded by the website |
| `portfolio_db.sql` | Database tables and relationships |

## Installation and Setup

### 1. Download the system

Download the repository using **Code > Download ZIP** on GitHub or clone it with Git.

Put the `portfolio-showcase` folder inside:

```text
C:\laragon\www\portfolio-showcase\
```

Make sure `index.php` is directly inside this folder.

### 2. Start Laragon

Open Laragon and start **Apache** and **MySQL**.

### 3. Import the database

1. Open phpMyAdmin.
2. Create a database named `portfolio_db`.
3. Select the database and click **Import**.
4. Choose `portfolio_db.sql` and click **Go**.
5. Check that `users`, `categories` and `projects` are created.

The SQL file contains the table structure only. It does not contain a default Admin account or saved project records.

### 4. Check the database connection

Open `includes/db.php`. The default local settings are:

```php
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "portfolio_db";
```

Change the settings only if your local database is different.

### 5. Open the website

Open this address in your browser:

```text
http://localhost/portfolio-showcase/
```

### 6. Prepare an Admin account

New accounts from the Register page are created with the `student` role. There is no default Admin user in the SQL file.

For local testing, register another account, open the `users` table in phpMyAdmin and change that account's `role` to `admin`. Then log out and log in again. Admin access depends on the account role, not its user ID.

## How to Use

### Student

1. Register and log in.
2. Open **Dashboard** to view student projects or search for a project.
3. Click **Submit Project** and fill in the details with a document file.
4. Open **My Portfolio** to view your submissions.
5. Click **Edit** to change the project details. The same form will show the saved information.
6. In Edit mode, the file input can stay empty to keep the current document. Select a new file only if you want to replace it.
7. Click **Save Changes** or **Cancel**. Use **Download** to get your document or **Delete** to remove the submission.

The **Report** button on the Student Dashboard is shown only for projects belonging to the logged-in student.

### Admin

1. Log in with an Admin account.
2. View system totals and recent projects on **Admin Dashboard**.
3. Open **Categories** to add, edit or delete a category.
4. A category linked to an existing project cannot be deleted. The page shows an error message for this case.
5. Open **All Submissions** to view student projects and download their files.

## Database Structure

The `portfolio_db` database has three tables:

| Table | Main columns | Purpose |
| --- | --- | --- |
| `users` | `id`, `full_name`, `email`, `password`, `role`, `created_at` | Student and Admin accounts |
| `categories` | `id`, `category_name`, `description`, `created_at` | Project categories |
| `projects` | `id`, `user_id`, `category_id`, `title`, `description`, `tech_stack`, `file_path`, `created_at` | Project details and document paths |

One user can have many projects, and one category can be used by many projects. The `projects` table uses foreign keys to link with `users` and `categories`.

Passwords are stored using `password_hash()` and checked using `password_verify()`.

## Validation, Search and Documents

**Form validation:** JavaScript checks required fields and shows `Please fill in all fields.` when an input is empty. On the Edit Project form, choosing a replacement document is optional. PHP also checks the submitted data before saving it to the database.

**File upload:** Supported files are **PDF, DOCX, TXT and ZIP**, up to **5 MB**. PHP checks the file extension, MIME type and size. Files are saved in `uploads/`, while their paths are stored in MySQL.

**File download:** Students can download their own project documents through `download.php`. Admin can download student documents through the same file.

**AJAX search:** `validation.js` sends the entered keyword to `student/dashboard.php?ajax=1&keyword=...`, and the matching project cards are updated without reloading the full page.

## Testing Checklist

- [ ] Register a Student account and log in.
- [ ] Log out and log in as Admin.
- [ ] Add, edit and delete an unused category.
- [ ] Try deleting a category used by a project.
- [ ] Submit a project with a valid document.
- [ ] Edit a project without changing its document.
- [ ] Replace a project document with a new file.
- [ ] Delete a project and check its uploaded file.
- [ ] Download a document using an allowed account.
- [ ] Test empty fields, unsupported file types and files over 5 MB.
- [ ] Test AJAX live search and the responsive dashboard cards.
- [ ] Check that Students cannot open Admin pages or edit other students' projects.

## Notes

- This system is set up for local development using Laragon.
- `portfolio_db.sql` creates the tables but does not include users or submissions.
- If database connection fails, check MySQL and `includes/db.php`.
- If an upload fails, check the `uploads` folder and PHP file upload settings.