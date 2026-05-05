# Astro + PHPMailer Contact Form

A professional and simple solution for adding a contact form to an Astro website using a PHP backend.

## 🚀 Overview

This project provides a professional way to add a contact form to an Astro website **without relying on 3rd-party services** (like Formspree, SendGrid, or Netlify Forms). By using your existing PHP-capable hosting, you keep full control over your data and avoid monthly subscriptions or third-party branding.

### Key Features
- **🚫 No 3rd-Party Services**: Send emails directly from your own server—no registration or subscriptions required.
- **📧 PHPMailer Core**: Uses the industry-standard [PHPMailer](https://github.com/PHPMailer/PHPMailer) library (manual inclusion) for reliable SMTP delivery.
- **🔒 Automated Security**: Includes an `.htaccess` firewall to block public access to the `.mail.env` file.
- **✨ Dynamic HTML Templates**: Responsive, professional email design with easy-to-use placeholders.
- **⚡ AJAX Submissions**: Form submissions are handled via the Fetch API for a seamless user experience.
- **🍯 Anti-Spam Honeypot**: Built-in hidden field trap to automatically block bot submissions without annoying captchas.
- **🤖 Google ReCAPTCHA v3**: Integrated invisible CAPTCHA verification to block bots without annoying users.

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

## 📋 Requirements

To use this solution, your hosting environment must provide:
1. **PHP 7.4+**: The server-side environment to process the form.
2. **PHPMailer Files**: The core library files (`Exception.php`, `PHPMailer.php`, `SMTP.php`) from [PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer) must be placed in `public/api/PHPMailer/`.

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

# ReCAPTCHA Configuration
RECAPTCHA_SITE_KEY=your-recaptcha-site-key
RECAPTCHA_SECRET_KEY=your-recaptcha-secret-key
```

### 2. Google ReCAPTCHA Settings
The form uses Google ReCAPTCHA v3 (invisible) to prevent spam without interrupting the user.
1. Get your Site Key and Secret Key from the [Google ReCAPTCHA Admin Console](https://www.google.com/recaptcha/admin/create) (ensure you create a **v3** property).
2. Add both your **Site Key** and **Secret Key** to the `.mail.env` file (as shown above).
*(The component will fall back to test keys if you haven't set these up yet).*

### 3. PHPMailer Files
This project uses a manual installation. The following files are required in `public/api/PHPMailer/`:
- `Exception.php`, `PHPMailer.php`, and `SMTP.php`.

### 4. Build & Deployment
When you run the build command, the project automatically prepares your PHP environment:

```bash
npm run build
```

**What happens during build?**
1. Astro builds your static site into the `dist/` folder.
2. A **postbuild** script automatically copies your `.mail.env` file into `dist/api/.mail.env`.
3. The included `.htaccess` file ensures that this file remains private and inaccessible to browsers.

---

## 📡 Form API Integration

The `Contact.astro` component handles form submissions by sending data to the PHP backend via an asynchronous Fetch API request.

### How it Works:
1. **Event Listener**: The form intercepts the default `submit` event using a client-side `<script>` tag within the component.
2. **Data Collection**: It uses `FormData` to collect all the input values (`name`, `email`, `subject`, `message`).
3. **Fetch API**: It sends a `POST` request to `/api/send.php` (defined in the form's `action` attribute) with the form data.
4. **JSON Response**: The PHP script processes the email and returns a JSON response indicating success or failure.
5. **UI Update**: The component dynamically updates the UI to show a success message or an error message based on the JSON response, without reloading the page.

If JavaScript is disabled, the form will fall back to a standard `POST` request directly to the PHP script.

---

## ✨ Email Customization

The system uses a separate HTML template for outgoing emails, allowing you to customize the design without touching the PHP logic.

### Template Location
The template is located at `public/api/email_template.html`.

### Available Placeholders
You can use the following placeholders in your HTML template:

| Placeholder | Description |
| :--- | :--- |
| `{{name}}` | The name of the sender |
| `{{email}}` | The sender's email address |
| `{{subject}}` | The subject of the message |
| `{{message}}` | The message content (handles line breaks) |

### Design Features
The default template features a modern, premium design:
- **Responsive Layout**: Works beautifully on desktops and mobile devices.
- **Clean Aesthetics**: Gradient headers and structured data fields.
- **Safe Handling**: Automatically escapes HTML and preserves line breaks in messages.

### ➕ Adding Custom Fields
To add new fields (like a "Phone Number" or "Company" field), follow these 3 steps:

1. **Update the Frontend Component (`src/components/Contact.astro`)**
   Add your new HTML input. Ensure it has a unique `name` attribute:
   ```html
   <input type="tel" id="phone" name="phone" placeholder="Phone Number" />
   ```

2. **Update the PHP Processor (`public/api/send.php`)**
   Capture the new field from the `$_POST` array and add it to the placeholder replacements:
   ```php
   // Capture the field (Around line 45)
   $phone = strip_tags(trim($_POST["phone"] ?? ''));
   
   // Add to replacements (Around line 82)
   $email_body = str_replace(
       ['{{name}}', '{{email}}', '{{subject}}', '{{message}}', '{{phone}}'],
       [$name, $email, $subject_input, nl2br(htmlspecialchars($message_content)), $phone],
       $template
   );
   ```

3. **Update the HTML Template (`public/api/email_template.html`)**
   Add your new placeholder `{{phone}}` anywhere in the email template.

---

## 🌐 Deployment Note

1. Upload the contents of the `dist/` folder to your server.
2. Ensure your server supports PHP 7.4+.

---

## 📜 License
MIT
