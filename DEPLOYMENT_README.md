# Church Management System - Production Deployment Guide
# Deployed from: https://github.com/mkokroy53-cmyk/crosslife-church-management

## 🚀 QUICK DEPLOYMENT OPTIONS

### Option 1: Hostinger (Recommended for Beginners)
1. Sign up at https://www.hostinger.com
2. Choose "Web Hosting" plan ($2.99/month)
3. Upload files via File Manager or FTP
4. Create MySQL database in cPanel
5. Update config_production.php with database details
6. Import database.sql via phpMyAdmin

### Option 2: 000WebHost (Free)
1. Sign up at https://www.000webhost.com
2. Upload files via File Manager
3. Create MySQL database
4. Update config_production.php
5. Import database.sql

### Option 3: DigitalOcean Droplet ($6/month)
1. Sign up at https://www.digitalocean.com
2. Create Ubuntu droplet
3. Install LAMP stack (Apache, MySQL, PHP)
4. Clone from GitHub: `git clone https://github.com/mkokroy53-cmyk/crosslife-church-management.git`
5. Configure database and update config_production.php

## 📋 DEPLOYMENT CHECKLIST

- [ ] Choose hosting provider
- [ ] Upload all files from GitHub
- [ ] Create MySQL database
- [ ] Update config_production.php with real database credentials
- [ ] Import database.sql file
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Test login functionality
- [ ] Configure domain (optional)

## 🔧 CONFIGURATION STEPS

1. **Database Setup:**
   - Create new MySQL database
   - Import the `database.sql` file
   - Note down: DB_HOST, DB_USER, DB_PASS, DB_NAME

2. **File Upload:**
   - Upload all files to public_html/ or www/ directory
   - Ensure .htaccess file is uploaded (for URL rewriting)

3. **Configuration:**
   - Rename config_production.php to config.php
   - Update database credentials in config.php

4. **Permissions:**
   - Set 755 permissions on directories
   - Set 644 permissions on files

## 🌐 DOMAIN SETUP (Optional)

- Point your domain to hosting nameservers
- Update DNS records if needed
- Enable SSL certificate (Let's Encrypt is free)

## 🔒 SECURITY NOTES

- Change default passwords after setup
- Keep PHP updated
- Regular backups of database
- Monitor for suspicious activity

## 📞 SUPPORT

If you encounter issues:
1. Check error logs in cPanel
2. Verify database connection
3. Ensure all files uploaded correctly
4. Test with a simple PHP file first

---
**Last updated:** April 26, 2026
**Repository:** https://github.com/mkokroy53-cmyk/crosslife-church-management</content>
<parameter name="filePath">c:\wamp64\www\church pro\DEPLOYMENT_README.md