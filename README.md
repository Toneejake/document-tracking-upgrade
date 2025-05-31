# WEB-BASED DOCUMENT TRACKING SYSTEM AND MANAGEMENT FOR NATIONAL IRRIGATION ADMINISTRATION REGIONAL OFFICE IV-A (CALABARZON) OF PILA, LAGUNA
## Project Description
A comprehensive document tracking system designed to streamline the process of document management, tracking, and workflow across different departments and user roles. The system enables efficient document routing, status tracking via QR codes, and provides role-based access control.

## Features

- **Role-Based Access Control** : Different interfaces and permissions for admin, record office, handler, and guest users
- **QR Code Integration** : Generate and scan QR codes for quick document tracking and status updates
- **Document Workflow Management** : Track documents as they move through different offices and handlers
- **Notification System** : Real-time notifications for document transfers, receipts, and status changes
- **Idle Document Monitoring** : Automated system to identify and report documents that haven't been processed within a specified timeframe
- **User Management** : Add, edit, and archive user accounts with different role assignments
- **Conversation System** : In-app messaging related to specific documents
- **Reporting** : Generate reports on document status and workflow metrics

## Enhanced / New Features

- **Modular Code Architecture** : Restructured code into reusable model classes 
- **Secure Configuration Handling** : Centralized config management with dynamic base path and email settings
- **Database Abstraction Layer** : Unified DB connection handling using `Config\Database`
- **PSR-4 Autoloading** : Clean class loading via Composer
- **PHPUnit Test Coverage** : Unit tests for core logic and database interactions
- **GitHub Actions Automation** : Tests run automatically on every push
- **Database Optimization Tools** : Archive old records and optimize tables for performance
- **Idle Document Detection** : Automatically detect and notify about documents not updated in 7+ days
- **Scheduled Maintenance Monitor** : Run optimization and checks via `monitor.php` (ideal for cron jobs)
- **Guest Dashboard** : View current document status and progress from a dedicated interface

## Technologies Used
- Backend : PHP
- Database : MySQL
- Libraries :
  - PHPMailer for email notifications
  - phpqrcode for QR code generation
  - TCPDF for PDF generation
- Frontend :
  - HTML/CSS/JavaScript
  - jQuery
  - DataTables for data presentation
  - SweetAlert for user-friendly alerts
  - Boxicons for UI icons
- Testing : PHPUnit
- Development Environment : Laragon
## Installation Instructions
1. Clone the repository to your local environment
2. Import the document-tracking-db.sql file to create the database schema and sample data
3. Configure your database connection in config/database.php
4. Ensure PHP version 7.x or higher is installed
5. Install required dependencies using Composer:
   ```
   composer install
   ```
6. Set up a cron job to run cron/check-idle-documents.php daily for idle document monitoring
7. Ensure the following directories have write permissions:
   - assets/qr-codes/
   - assets/uploaded-pdf/
   - assets/user-profile/
## How to Use / Run the Project
1. Start your web server (Apache/Nginx) and MySQL database
2. Navigate to the project URL in your browser
3. Log in with the appropriate credentials based on your role:
   - Admin: [admin credentials]
   - Record Office: [record office credentials]
   - Handler: [handler credentials]
   - Guest: [guest credentials]
4. Follow the intuitive interface to manage documents based on your role
## Demo Video Link

[Demo Video](https://drive.google.com/file/d/1zN2t-6JoFvrbQN35jUo8qgS45Im46LtX/view?usp=drive_link)

## Folder Structure
- assets/ : Contains all static resources (CSS, JS, images, QR codes, uploaded documents)
- config/ : Configuration files for database and application settings
- controller/ : PHP controllers handling business logic
- cron/ : Scheduled tasks like idle document checking
- db-optimization/ : Database maintenance scripts
- model/ : Database models and data access layer
- PHPMailer/ : Email functionality library
- phpqrcode/ : QR code generation library
- tests/ : PHPUnit test files
- vendor/ : Composer dependencies
- views/ : User interface files organized by user role

## Creators
- De Leon, Melvin R.
- Esmas, Dan Kenneth D.
- Sotto, Jerwin A.

## Upgrade Contributors
- Evangelista, John Kervin D.
- Marquez, Jethro
- Ponce, John Paul
- Santos, Gabriel Scott
- Sotomayor, Rolan C.

## NOTE:
Github automation won't work because of the required file structure but can be easily modified by moving the test scripts and required files into the root.