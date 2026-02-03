# Template2Web - HTML to Dynamic PHP Converter

**Transform static HTML templates into fully-editable dynamic PHP websites in seconds.**

Template2Web is an intelligent automation tool that converts static HTML templates into dynamic, content-manageable PHP websites with live inline editing capabilities. Perfect for rapid prototyping, client projects, and turning purchased HTML templates into customizable web applications.

---

## Key Features

### 🚀 Automated HTML to PHP Conversion
- **Batch Processing**: Convert multiple HTML templates from ZIP files simultaneously
- **Smart Text Extraction**: Automatically identifies and extracts all text content from HTML
- **Intelligent Categorization**: Recognizes and categorizes emails, phone numbers, and content types
- **Link Conversion**: Automatically converts `.html` links to `.php` for seamless navigation
- **Structure Preservation**: Maintains original template structure and styling

### ✏️ Live Inline Editing System
- **Click-to-Edit**: Logged-in admins can click any text element to edit it directly on the page
- **Real-time Updates**: Changes are saved instantly without page refresh
- **Visual Feedback**: Clear indicators show editable elements with hover effects
- **Session-based**: Edit mode only visible to authenticated administrators

### 🎨 Advanced Image Management
- **Upload with Preview**: Replace any image directly from the live website
- **Automatic Resizing**: Uploaded images are automatically resized to match original dimensions
- **Dimension Display**: Shows image dimensions on upload buttons for easy reference
- **Smart Backup**: Original images are automatically backed up before replacement
- **Format Support**: Handles JPEG, PNG, and GIF formats with transparency preservation

### 📚 Dictionary-Based Language System
- **Centralized Content**: All text content stored in a single `lang.php` dictionary file
- **Key-Value Storage**: Each text element has a unique key for easy management
- **Reusable Text**: Duplicate content automatically uses the same key
- **Easy Translation**: Simple structure enables multi-language support
- **API Integration**: RESTful update endpoint for external integrations

### 🔐 Secure Admin Panel
- **Session Management**: Secure PHP session-based authentication
- **Login Protection**: Password-protected admin access
- **Logout Functionality**: Fixed logout button appears on all pages when logged in
- **Authorization Checks**: Image uploads and content updates require authentication

---

## How It Works

### 1. **Upload Templates**
Place your HTML template ZIP files in the `zip/` directory.

### 2. **Run Conversion**
Execute `home.php` to automatically:
- Extract ZIP files
- Parse HTML content
- Extract text into dictionary
- Convert to PHP format
- Add admin editing capabilities
- Copy necessary admin files

### 3. **Manage Content**
- Login through `admin.php`
- Click any text to edit inline
- Click image dimensions to upload new images
- Changes save automatically

---

## Technology Stack

- **Backend**: PHP 7.4+
- **Frontend**: Vanilla JavaScript (ES6+)
- **Image Processing**: GD Library
- **Authentication**: Session-based security
- **Data Storage**: PHP arrays exported as files
- **UI Enhancement**: Bootstrap 5

---

## Project Structure

```
template2web/
├── home.php              # Main conversion engine
├── admin.php             # Admin login interface
├── edittext.js           # Live editing JavaScript
├── update_lang.php       # API endpoint for content updates
├── upload_image.php      # Image upload & resize handler
├── logout.php            # Session termination
├── zip/                  # Template ZIP files directory
│   ├── LifeSure-1.0.0.zip
│   ├── elearning-1.0.0.zip
│   └── startup2-1.0.0.zip
└── brands/               # Output directory for converted sites
    └── tanxe.com/
        ├── [template1]/
        ├── [template2]/
        └── [template3]/
```

---

## Key Technical Achievements

### Intelligent Text Recognition
The system uses advanced regex patterns to:
- Identify valid text vs. code/numbers
- Detect email addresses automatically
- Recognize phone number patterns
- Filter out HTML tags and attributes
- Handle special characters and encoding

### Dynamic Key Generation
```php
// Automatically generates unique, meaningful keys
"About Us" → $dictionary['about_us']
"Contact" → $dictionary['contact']
"Contact" (2nd occurrence) → Uses same key (efficient!)
```

### Smart Content Replacement
Original HTML:
```html
<h1>Welcome to Our Site</h1>
```

Converted PHP:
```php
<h1><span data-key='welcome_to_our_site'><?php echo $dictionary['welcome_to_our_site']; ?></span></h1>
```

### Image Upload Flow
1. Admin clicks dimension button on image
2. File picker opens
3. Image uploads via AJAX
4. Server backs up original
5. New image auto-resizes to exact dimensions
6. Page refreshes with cache-busting parameter

---

## Use Cases

### 💼 Freelancers & Agencies
- Quickly convert client-provided HTML templates
- Deliver editable websites without CMS complexity
- Enable clients to update content themselves
- Rapid prototyping and MVP development

### 🎓 Developers & Learners
- Learn PHP templating concepts
- Understand MVC-style separation of content
- Study automated HTML parsing techniques
- Practice session management and security

### 🏢 Small Businesses
- Convert purchased themes into manageable websites
- Update content without technical knowledge
- Manage multiple brand websites from templates
- Cost-effective alternative to full CMS

---

## Security Features

- Session-based authentication
- CSRF protection via POST methods
- File upload validation
- Path traversal prevention
- Authorization checks on all admin actions
- Secure file handling with proper permissions

---

## Future Enhancement Ideas

- Multi-language support with language switcher
- Database integration for scalability
- User role management (admin, editor, viewer)
- Version control for content changes
- Import/export translation files
- Visual theme customizer
- Media library management
- SEO meta tag editor

---

## Installation & Setup

1. **Requirements**
   - PHP 7.4 or higher
   - Apache/Nginx web server
   - GD Library enabled
   - mod_rewrite enabled (optional)

2. **Quick Start**
   ```bash
   # Clone repository
   git clone <repository-url>

   # Place HTML templates in zip/ directory
   cp your-template.zip template2web/zip/

   # Run conversion (via browser)
   http://localhost/template2web/home.php

   # Login to admin
   http://localhost/template2web/brands/tanxe.com/[template-name]/admin.php
   # Default: username: wasim, password: 111111
   ```

3. **Configuration**
   - Edit base directory in `home.php` (line 4)
   - Change admin credentials in `admin.php` (lines 16-17)
   - Adjust file permissions as needed

---

## Performance Optimizations

- Recursive directory scanning for nested templates
- Efficient regex patterns for text extraction
- Memory-conscious image processing
- Minimal JavaScript footprint (6.8KB)
- No external dependencies for core functionality
- Fast AJAX-based updates without page reload

---

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## License

This project is available for personal and commercial use.

---

## About the Developer

This project demonstrates expertise in:
- **Full-stack PHP Development**: Complex file processing, session management, API endpoints
- **JavaScript/AJAX**: Modern ES6+, event delegation, async operations
- **Automation**: Batch processing, intelligent parsing, workflow optimization
- **UX Design**: Intuitive inline editing, visual feedback, responsive UI
- **Security**: Authentication, authorization, secure file handling
- **Problem Solving**: Converting static to dynamic, intelligent text recognition

**Built with attention to detail, security best practices, and user experience in mind.**

---

## Contact & Support

For questions, collaborations, or project inquiries, please open an issue or reach out via the repository.

---

**⭐ Star this repository if you find it useful!**

*Transform static templates into dynamic websites - one click at a time.*
