# Church Management System

A complete, beautiful, and modern church management system built with PHP and MySQL.

## Features

### Core Modules
- **Dashboard** - Overview with statistics and quick insights
- **Members Management** - Complete member profiles with family relationships
- **Attendance Tracking** - Mark and track service attendance
- **Contributions/Giving** - Record tithes, offerings, and donations
- **Events Management** - Create and manage church events
- **Groups & Ministries** - Manage small groups and ministry teams
- **Prayer Requests** - Track and manage prayer requests
- **Expenses** - Record and track church expenses
- **Reports & Analytics** - Financial reports and attendance analytics
- **User Management** - Role-based access control (Admin only)

### User Roles
- **Admin** - Full system access
- **Pastor** - Access to all modules except user management
- **Treasurer** - Focus on financial modules
- **Secretary** - Member and event management
- **Volunteer** - Limited access

## Installation Instructions

### Prerequisites
- WAMP Server (or XAMPP/LAMP)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Setup Database
1. Start WAMP Server
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Click on "Import" tab
4. Select the `database.sql` file from this project
5. Click "Go" to import the database

### Step 2: Configure Application
1. Copy all project files to your WAMP `www` directory
   - Default location: `C:\wamp64\www\church-pro\`
2. Open `config.php` and verify database settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Change if you have a password
   define('DB_NAME', 'church_management');
   ```

### Step 3: Access the System
1. Open your browser
2. Navigate to: `http://localhost/church-pro/`
3. Login with default credentials:
   - **Username:** admin
   - **Password:** admin123

### Step 4: Change Default Password
1. After first login, go to Users Management
2. Create a new admin user with a secure password
3. Delete or disable the default admin account

## Usage Guide

### Adding Members
1. Go to "Members" from the sidebar
2. Click "Add Member" button
3. Fill in member details
4. Click "Save Member"

### Recording Contributions
1. Go to "Contributions"
2. Click "Record Contribution"
3. Select member (or leave as Anonymous)
4. Enter amount, type, and payment method
5. Click "Save Contribution"

### Marking Attendance
1. Go to "Attendance"
2. Select service date and type
3. Check members who attended
4. Click "Save Attendance"

### Creating Events
1. Go to "Events"
2. Click "Create Event"
3. Fill in event details
4. Click "Save Event"

### Managing Groups
1. Go to "Groups"
2. Click "Create Group"
3. Assign a leader and meeting schedule
4. Click "Create Group"

### Viewing Reports
1. Go to "Reports"
2. View financial summaries
3. Check attendance trends
4. Analyze contribution patterns

## Security Features
- Password hashing with bcrypt
- Session-based authentication
- Role-based access control
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars

## Customization

### Changing Colors
Edit `assets/css/style.css` and modify the gradient colors:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Adding New Contribution Types
Edit `contributions.php` and add options to the contribution_type select:
```html
<option value="Your Type">Your Type</option>
```

### Adding New User Roles
1. Modify the users table in database
2. Update `config.php` role checking functions
3. Add role options in `users.php`

## Troubleshooting

### Database Connection Error
- Verify WAMP is running
- Check database credentials in `config.php`
- Ensure database was imported correctly

### Login Not Working
- Clear browser cache and cookies
- Verify database has the default admin user
- Check PHP session is enabled

### Pages Not Loading
- Ensure all files are in the correct directory
- Check file permissions
- Verify PHP is enabled in WAMP

## Support
For issues or questions, check:
- Database connection settings
- PHP error logs in WAMP
- Browser console for JavaScript errors

## License
This project is open source and available for church use.

## Credits
Built with modern web technologies:
- PHP 7.4+
- MySQL
- HTML5/CSS3
- JavaScript
- Font Awesome Icons
- Inter Font Family
