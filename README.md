# Christening Invitation Website for Baby Calix

A beautiful, responsive single-page virtual christening invitation with RSVP functionality.

## Features

✨ **Beautiful Design**
- Soft blue watercolor theme with baby-themed illustrations
- Smooth fade-in animations
- Responsive layout for mobile, tablet, and desktop

📋 **Complete Event Information**
- Event details (date, time, venue)
- Godparents listing (Ninang and Ninong)
- Dress code guidelines
- Gift guide information

📝 **Interactive RSVP Form**
- Modal-based form with validation
- Collects guest name, number of attendees, and personal message
- Real-time form validation
- Success/error message feedback

🎵 **Background Music**
- Optional audio toggle
- Elegant play/pause control

💾 **PHP Backend**
- Secure form processing
- JSON file-based storage
- Input validation and sanitization
- Error handling

## Requirements

- Web server (Apache, Nginx, or similar)
- PHP 7.0 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Installation

1. **Upload Files**
   - Copy all files to your web server directory
   - Ensure the following structure:
     ```
     /
     ├── index.html
     ├── styles.css
     ├── script.js
     ├── submit_rsvp.php
     ├── assets/
     │   ├── baby-photo.jpg (add your baby photo)
     │   └── music.mp3 (optional background music)
     └── data/
         └── rsvps.json (will be created automatically)
     ```

2. **Set Permissions**
   - Ensure the `data/` directory is writable by the web server:
     ```bash
     chmod 755 data/
     ```

3. **Add Your Content**
   - Replace `assets/baby-photo.jpg` with your actual baby photo
   - (Optional) Add background music as `assets/music.mp3`

4. **Customize Content**
   - Edit `index.html` to update:
     - Baby name
     - Event date, time, and venue
     - Godparents names
     - RSVP contact information
     - Dress code details
     - Gift guide text

## File Structure

### Frontend Files
- **index.html** - Main HTML structure with all content
- **styles.css** - Complete styling with responsive design and animations
- **script.js** - Interactive JavaScript (modal, form validation, RSVP, audio)

### Backend Files
- **submit_rsvp.php** - Processes RSVP submissions and stores to JSON

### Asset Files
- **assets/baby-photo.jpg** - Baby photo (placeholder included)
- **assets/music.mp3** - Background music (optional)

### Data Files
- **data/rsvps.json** - Stores RSVP submissions (auto-created)

## Usage

### For Guests
1. Open the website in a browser
2. View event details and information
3. Click "Count Me In (RSVP)" button
4. Fill out the RSVP form
5. Submit and receive confirmation

### For Hosts (Viewing RSVPs)
RSVPs are stored in `data/rsvps.json`. You can:
- View the file directly in a text editor
- Create a simple admin page to display RSVPs
- Download the file for backup

RSVP data structure:
```json
[
  {
    "id": "rsvp_unique_id",
    "name": "Guest Name",
    "guests": 3,
    "message": "Congratulations message",
    "timestamp": "2024-12-15 14:30:22",
    "ip": "192.168.1.1"
  }
]
```

## Customization

### Colors
Edit CSS variables in `styles.css`:
```css
:root {
    --primary-blue: #a3c4e5;
    --soft-blue: #d4e6f7;
    --accent-blue: #7ba7d1;
    /* Add more customizations */
}
```

### Content
All content can be edited directly in `index.html`:
- Event details
- Godparents names
- Dress code text
- Gift guide information

### Animations
Animation timing can be adjusted in `styles.css`:
```css
.fade-in {
    animation: fadeIn 1s ease-out;
}
```

## Browser Compatibility

Tested and working on:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Security Features

- Input validation and sanitization
- SQL injection prevention (no database)
- XSS protection through JSON encoding
- File locking for concurrent write protection
- Maximum input length limits
- Rate limiting recommended for production

## Troubleshooting

### RSVP Form Not Working
1. Check PHP is enabled on your server
2. Verify `data/` directory permissions (755)
3. Check browser console for JavaScript errors
4. Verify `submit_rsvp.php` path is correct

### Audio Not Playing
1. Ensure `assets/music.mp3` exists
2. Check audio file format (MP3 recommended)
3. Some browsers require user interaction before playing audio
4. Check browser console for audio errors

### Styling Issues
1. Clear browser cache
2. Verify `styles.css` is loading (check browser developer tools)
3. Check for CSS syntax errors

### PHP Errors
1. Enable PHP error logging
2. Check server error logs
3. Verify PHP version (7.0+)
4. Ensure JSON extension is enabled

## Performance

- Optimized CSS with minimal animations
- Vanilla JavaScript (no framework overhead)
- Lightweight design (~50KB total)
- Fast loading on mobile devices

## License

This project is provided as-is for personal use. Feel free to customize and use for your own events.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review browser console for errors
3. Verify server configuration
4. Test on different browsers

## Credits

Design inspired by soft watercolor baby themes with modern web development best practices.

---

**Enjoy your special day! 🎉👶✨**
