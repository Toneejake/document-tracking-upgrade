# Document Tracking System
## Project Description
A comprehensive document tracking system designed to streamline the process of document management, tracking, and workflow across different departments and user roles. The system enables efficient document routing, status tracking via QR codes, and provides role-based access control.

## Features
- Role-Based Access Control : Different interfaces and permissions for admin, record office, handler, and guest users
- QR Code Integration : Generate and scan QR codes for quick document tracking and status updates
- Document Workflow Management : Track documents as they move through different offices and handlers
- Notification System : Real-time notifications for document transfers, receipts, and status changes
- Idle Document Monitoring : Automated system to identify and report documents that haven't been processed within a specified timeframe
- User Management : Add, edit, and archive user accounts with different role assignments
- Conversation System : In-app messaging related to specific documents
- Reporting : Generate reports on document status and workflow metrics
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
[Insert your demo video link here]

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
## Contributors
- [Your Name]
- [Team Member 1]
- [Team Member 2]
## License
[Specify your license here]

Note: This README template is based on the current project structure and can be edited to include more specific details about your implementation.