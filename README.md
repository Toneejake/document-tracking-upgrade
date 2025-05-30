# 📁 Document Tracking System for NIA CALABARZON

A **web-based document tracking and management system** developed for the **National Irrigation Administration Regional Office IV-A (CALABARZON), Pila, Laguna**. This system helps manage internal document flow, status monitoring, and tracking across departments with ease and transparency.

---

## 🚀 Features

- 🔐 User authentication (Admin and Employee access)
- 📄 Upload, track, and manage official documents
- 🟢 Real-time status updates and document history
- 📊 Dashboard with summarized reports
- 📥 Downloadable files and document previews
- 🔎 Document filtering and search
- 🛡️ Access control and user activity logs

---

## 🛠️ Tech Stack

| Category         | Technologies Used              |
|------------------|--------------------------------|
| Frontend         | HTML, CSS, JavaScript, Bootstrap |
| Backend          | PHP                            |
| Database         | MySQL                          |
| Hosting (local)  | XAMPP/Laragon/WAMP             |

---

## 📁 Folder Structure

document-tracking/
├── assets/ # Images, logos, CSS/JS
├── config/ # Database connection and config files
├── docs/ # Supporting documentation (if any)
├── src/ # Main source code (PHP files, views, controllers)
├── database.sql # MySQL database structure (import to phpMyAdmin)
├── index.php # Homepage entry point
└── README.md # Project documentation

yaml
Copy
Edit

---

## 🧪 How to Set Up Locally

1. **Clone this repository:**
   ```bash
   git clone https://github.com/yourusername/document-tracking.git
Import the database:

Open phpMyAdmin

Create a new database (e.g., document_tracking)

Import the file: database.sql

Update database credentials:

Go to /config/db.php

Set your host, username, password, and database name

Run the system:

Open XAMPP and start Apache/MySQL

Access the system at:

arduino
Copy
Edit
http://localhost/document-tracking