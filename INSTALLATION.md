# Installation Guide

Complete step-by-step instructions for deploying the Christening Invitation Website.

## Prerequisites

### Server Requirements
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.0 or higher (PHP 7.4+ recommended)
- **Disk Space**: Minimum 50MB (100MB+ recommended for RSVPs)
- **Browser Support**: Modern browsers (Chrome, Firefox, Safari, Edge)

### Local Development (Optional)
- **XAMPP**, **WAMP**, **MAMP**, or **Local by Flywheel**
- Or use PHP's built-in server: `php -S localhost:8000`

## Installation Steps

### Method 1: Standard Web Hosting (cPanel, Plesk, etc.)

#### Step 1: Upload Files
1. Compress all files into a ZIP archive
2. Log in to your web hosting control panel (cPanel/Plesk)
3. Navigate to File Manager
4. Go to `public_html` or `www` directory
5. Upload and extract the ZIP file
   - OR use FTP client (FileZilla, Cyberduck) to upload files directly

#### Step 2: Verify File Structure
Ensure the following structure exists:
```
public_html/
├── index.html
├── styles.css
├── script.js
├── submit_rsvp.php
├── .htaccess
├── README.md
├── assets/
│   ├── README.md
│   ├── baby-photo.jpg (you need to add this)
│   └── music.mp3 (optional)
└── data/
    └── .gitkeep
```

#### Step 3: Set Directory Permissions
Using File Manager or FTP:
1. Set `data/` directory permissions to **755** (rwxr-xr-x)
   ```bash
   chmod 755 data/
   ```
2. Ensure web server can write to `data/` directory
3. All other files should be **644** (rw-r--r--)
4. Directories should be **755** (rwxr-xr-x)

#### Step 4: Add Your Content
1. Upload your baby photo to `assets/` directory
   - Name it `baby-photo.jpg` or `baby-photo.png`
   - Recommended size: 500x500px or larger
2. (Optional) Upload background music as `assets/music.mp3`

#### Step 5: Customize Content
Edit `index.html` to update:
- Baby name (if different from "Calix")
- Event date and time
- Venue information
- Godparents names
- RSVP contact information
- Dress code details
- Gift guide text

#### Step 6: Test the Website
1. Open your website URL in a browser
2. Verify all content displays correctly
3. Test the RSVP form:
   - Click "Count Me In (RSVP)"
   - Fill out the form
   - Submit and verify success message
4. Check that `data/rsvps.json` was created
5. Test on mobile devices
6. Test audio toggle button

---

### Method 2: Using PHP Built-in Server (Local Testing)

Perfect for testing before uploading to production server.

#### Step 1: Install PHP
- **Windows**: Download from [php.net](https://windows.php.net/download/)
- **Mac**: Pre-installed, or use Homebrew: `brew install php`
- **Linux**: `sudo apt install php` or `sudo yum install php`

#### Step 2: Navigate to Project Directory
```bash
cd "c:\Users\Administrator\Documents\Downloads\CALIX CHRISTENING"
```

#### Step 3: Start PHP Server
```bash
php -S localhost:8000
```

#### Step 4: Open in Browser
Visit: `http://localhost:8000`

#### Step 5: Test All Features
- View all sections
- Submit test RSVP
- Check `data/rsvps.json` file is created
- Test responsive design (resize browser)
- Test audio toggle

---

### Method 3: Using XAMPP/WAMP/MAMP

#### Step 1: Install XAMPP/WAMP/MAMP
Download from:
- **XAMPP**: https://www.apachefriends.org/
- **WAMP**: https://www.wampserver.com/
- **MAMP**: https://www.mamp.info/

#### Step 2: Copy Project Files
- **XAMPP**: Copy to `C:\xampp\htdocs\christening\`
- **WAMP**: Copy to `C:\wamp64\www\christening\`
- **MAMP**: Copy to `/Applications/MAMP/htdocs/christening/`

#### Step 3: Start Apache Server
- Open XAMPP/WAMP/MAMP control panel
- Start Apache server
- (PHP should start automatically)

#### Step 4: Open in Browser
Visit:
- XAMPP/WAMP: `http://localhost/christening/`
- MAMP: `http://localhost:8888/christening/`

---

## Post-Installation Configuration

### 1. Security Hardening

#### Protect Data Directory
The `.htaccess` file already includes protection, but verify:
```apache
<FilesMatch "data/rsvps\.json">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

#### Enable HTTPS (Recommended)
1. Obtain SSL certificate (Let's Encrypt, Cloudflare, or hosting provider)
2. Uncomment HTTPS redirect in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 2. Customize Colors and Styling

Edit CSS variables in `styles.css`:
```css
:root {
    --primary-blue: #a3c4e5;     /* Main blue color */
    --soft-blue: #d4e6f7;        /* Light blue backgrounds */
    --accent-blue: #7ba7d1;      /* Buttons and highlights */
    --text-dark: #2c3e50;        /* Main text color */
    --text-light: #5a6c7d;       /* Secondary text */
}
```

### 3. Update Content

Open `index.html` and find/replace:
- **Baby name**: Search for "Calix" and replace
- **Event date**: Update "27 December, 2026"
- **Time**: Update "10:00am"
- **Venue**: Update "OUR LADY OF FATIMA PARISH CHURCH"
- **Location**: Update "Lawa, Calamba City"
- **RSVP contact**: Update contact information
- **Godparents**: Add/remove names in the godparents section

### 4. Set Up RSVP Notifications (Optional)

Create `view_rsvps.php` to view submissions:
```php
<?php
// Simple admin page to view RSVPs
$file = 'data/rsvps.json';
if (file_exists($file)) {
    $rsvps = json_decode(file_get_contents($file), true);
    echo '<h1>RSVPs</h1>';
    echo '<table border="1">';
    echo '<tr><th>Name</th><th>Guests</th><th>Message</th><th>Timestamp</th></tr>';
    foreach ($rsvps as $rsvp) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($rsvp['name']) . '</td>';
        echo '<td>' . htmlspecialchars($rsvp['guests']) . '</td>';
        echo '<td>' . htmlspecialchars($rsvp['message']) . '</td>';
        echo '<td>' . htmlspecialchars($rsvp['timestamp']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo 'No RSVPs yet.';
}
?>
```

**Security Note**: Password-protect this page or add authentication.

---

## Troubleshooting

### Issue: RSVP Form Not Working

**Symptoms**: Form submits but no success message, or error message appears

**Solutions**:
1. Check PHP is enabled:
   ```bash
   php -v
   ```
2. Verify `data/` directory exists and is writable:
   ```bash
   chmod 755 data/
   ```
3. Check PHP error logs:
   - Linux: `/var/log/apache2/error.log` or `/var/log/php/error.log`
   - XAMPP: `C:\xampp\apache\logs\error.log`
   - Check browser console (F12) for JavaScript errors
4. Test PHP directly:
   ```bash
   php submit_rsvp.php
   ```

### Issue: Baby Photo Not Displaying

**Solutions**:
1. Verify filename is exactly `baby-photo.jpg` or `baby-photo.png`
2. Check file is in `assets/` directory
3. Clear browser cache (Ctrl+F5 or Cmd+Shift+R)
4. Check file permissions (should be 644)
5. Try a different image format

### Issue: Audio Not Playing

**Solutions**:
1. Verify file is named `music.mp3` in `assets/` directory
2. Check file format (must be MP3)
3. Try clicking audio toggle after page loads (some browsers block autoplay)
4. Test in different browser
5. Check browser console for errors
6. Verify audio file isn't corrupted (try playing in media player)

### Issue: Styling Looks Broken

**Solutions**:
1. Verify `styles.css` file exists and is in the same directory as `index.html`
2. Clear browser cache
3. Check browser console for CSS loading errors
4. Verify file wasn't corrupted during upload
5. Check file permissions (should be 644)

### Issue: Mobile View Doesn't Look Right

**Solutions**:
1. Add viewport meta tag (already included in template)
2. Test in browser device emulation mode (F12 > Toggle Device Toolbar)
3. Clear mobile browser cache
4. Check CSS media queries are loaded

---

## Performance Optimization

### 1. Image Optimization
Compress images using:
- **Online tools**: TinyPNG, Squoosh.app, Compressor.io
- **Command line**: ImageMagick, pngquant
```bash
# Example: Optimize JPEG
convert baby-photo.jpg -quality 85 -strip baby-photo-optimized.jpg
```

### 2. Enable Caching
The `.htaccess` file includes caching rules. Verify they're active:
```bash
# Check Apache modules
apachectl -M | grep expires
apachectl -M | grep headers
```

### 3. Minify CSS and JavaScript (Optional)
Use online tools or build tools:
- CSS: cssnano, clean-css
- JavaScript: Terser, UglifyJS

### 4. CDN Integration (Advanced)
For high-traffic sites, consider using a CDN like Cloudflare.

---

## Backup and Maintenance

### Regular Backups
1. **Backup RSVP data** regularly:
   ```bash
   cp data/rsvps.json backups/rsvps_$(date +%Y%m%d).json
   ```
2. **Download RSVPs** via FTP or cPanel File Manager
3. **Database-free advantage**: Easy to backup (just copy files)

### Monitoring
- Check `data/rsvps.json` periodically for new submissions
- Monitor server disk space
- Check PHP error logs for issues

### Updates
- Keep PHP updated for security
- Update `.htaccess` rules as needed
- Review and update security headers

---

## Going Live Checklist

Before sharing the invitation:

- [ ] All files uploaded to server
- [ ] Baby photo added (`assets/baby-photo.jpg`)
- [ ] (Optional) Background music added (`assets/music.mp3`)
- [ ] Content customized (names, dates, venue)
- [ ] `data/` directory permissions set to 755
- [ ] RSVP form tested and working
- [ ] Audio toggle tested (if using music)
- [ ] Tested on mobile devices
- [ ] Tested in multiple browsers (Chrome, Firefox, Safari, Edge)
- [ ] Responsive design verified
- [ ] All links and buttons work
- [ ] SSL certificate installed (HTTPS)
- [ ] Backup plan in place for RSVP data
- [ ] Error pages customized (optional)
- [ ] Analytics added (Google Analytics, optional)

---

## Support and Resources

### Documentation
- `README.md` - Project overview
- `assets/README.md` - Asset management guide
- `INSTALLATION.md` - This file

### Common Issues
Check troubleshooting section above or:
1. Review browser console (F12)
2. Check PHP error logs
3. Verify file permissions
4. Test in different browser

### Technical Stack
- **HTML5** - Structure
- **CSS3** - Styling and animations
- **Vanilla JavaScript** - Interactivity
- **PHP 7+** - Backend processing
- **JSON** - Data storage

---

**Congratulations! Your christening invitation website is ready to share.** 🎉👶✨
