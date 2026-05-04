# Astro + PHPMailer Contact Form

A premium, lightweight, and zero-dependency solution for adding a contact form to an Astro website using a PHP backend.

## 🚀 Overview

This project demonstrates how to integrate a modern Astro frontend with a traditional PHP mailing backend. It is designed for developers who want a "lean" setup without the overhead of Composer or large JavaScript-based mailing libraries.

### Key Features
- **📧 PHPMailer Integration**: Uses the industry-standard PHPMailer library for reliable SMTP email delivery.
- **🛡️ Secure Config Handling**: A custom, zero-dependency PHP loader that handles the `.mail.env` configuration.
- **📦 No-Composer Setup**: Manually managed PHPMailer files to keep your `vendor/` folder clean.
- **🔒 Automated Security**: Includes an `.htaccess` firewall to block public access to the `.mail.env` file.
- **⚡ AJAX Submissions**: Form submissions are handled via the Fetch API for a seamless user experience.

---

## 📁 Project Structure

```text
├── .mail.env                  # SMTP Credentials (Private)
├── public/
│   ├── .htaccess              # Security firewall (Blocks access to .mail.env)
│   └── api/
│       ├── send.php           # PHP processing script
│       ├── email_template.html # HTML Email Template
│       └── PHPMailer/         # Core PHPMailer files (Manual install)

├── src/
│   ├── components/
│   │   └── Contact.astro      # The Contact Form component
│   └── pages/
│       └── index.astro        # Main landing page
└── package.json               # Includes automated 'postbuild' script
```

---

## 🛠️ Setup & Configuration

### 1. SMTP Settings
Create a `.mail.env` file in the project root and fill in your mail server details:

```env
SMTP_HOST=smtp.your-provider.com
SMTP_AUTH=true
SMTP_USERNAME=your-email@example.com
SMTP_PASSWORD=your-secure-password
SMTP_SECURE=tls
SMTP_PORT=587

MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="Website Contact"
MAIL_TO_ADDRESS=admin@your-domain.com
MAIL_TO_NAME="Admin"
```

### 2. PHPMailer Files
This project uses a manual installation. The following files are required in `public/api/PHPMailer/`:
- `Exception.php`, `PHPMailer.php`, and `SMTP.php`.

### 3. Build & Deployment
When you run the build command, the project automatically prepares your PHP environment:

```bash
npm run build
```

**What happens during build?**
1. Astro builds your static site into the `dist/` folder.
2. A **postbuild** script automatically copies your `.mail.env` file into `dist/api/.mail.env`.
3. The included `.htaccess` file ensures that this file remains private and inaccessible to browsers.

---

## 🌐 Deployment Note

1. Upload the contents of the `dist/` folder to your server.
2. Ensure your server supports PHP 7.4+.

---

## 📜 License
MIT
