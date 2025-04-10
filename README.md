
# 🍽️ PesUFood - Online Food Ordering System

**PesUFood** is a simple and responsive online food ordering platform built with PHP, MySQL, and Bootstrap. Users can browse food items, add them to a cart, update quantities, and place orders with email confirmation support.

## 🚀 Features

- Add to cart & update quantity  
- Order confirmation & summary  
- Email verification & password reset using OTP  
- User registration/login with dark mode option  
- Environment testing (`test_db.php`, `test_env.php`)  
- PHPMailer integration for email services  
- Bootstrap UI with responsive design

## 🧰 Prerequisites

Ensure the following are installed:

- XAMPP (includes Apache, PHP & MySQL)  
- PHP v7.4 or later  
- Composer (for dependency management)  
- A Google account for email (with App Passwords)

## ⚙️ Installation Guide

### 1. Clone or Download the Repository

Place it inside XAMPP's htdocs folder:  
`C:/xampp/htdocs/PesUFood`

Or download the ZIP and extract to the same location.

### 2. Configure the MySQL Database

1. Open XAMPP and start Apache and MySQL.  
2. Visit `http://localhost/phpmyadmin`.  
3. Create a database named `pesufood_db`.  
4. Import the SQL file: `PesUFood/db/pesufood_db.sql`.

### 3. Configure PHP Files

Open `db.php` and update credentials if needed:

```
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pesufood_db";
```

### 4. Configure PHPMailer

Install PHPMailer using Composer:  
`composer require phpmailer/phpmailer`

Then in files like `registration.php`, `forgot_password.php`, update:

```
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-google-app-password';
```

Or load credentials from environment variables (see below).

### 5. Run the App

Start Apache & MySQL in XAMPP, then go to:  
`http://localhost/PesUFood/`

## 🔐 Securely Setting SMTP Credentials in `httpd.conf`

Instead of hardcoding SMTP credentials in your PHP files, store them as Apache environment variables:

### ✅ Steps to Set Environment Variables:

1. Open `httpd.conf`:  
   In XAMPP Control Panel → Apache → Config → `httpd.conf`

2. Add the following lines at the end of the file:

```
SetEnv SMTP_USERNAME "your-email@gmail.com"
SetEnv SMTP_PASSWORD "your-google-app-password"
```

3. Restart Apache from XAMPP Control Panel.

### 💡 How to Access These in PHP

```php
$mail->Username = getenv('SMTP_USERNAME');
$mail->Password = getenv('SMTP_PASSWORD');
```

### ⚠️ Notes

- Ensure `mod_env` is enabled in Apache (enabled by default in XAMPP).
- Never commit credentials to version control.

## ✉️ PHP.ini and Sendmail Configuration (Optional)

If you're using `mail()` or fallback methods, configure this:

### 1. Edit `php.ini`

```
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from = your-email@gmail.com
sendmail_path = ""C:\xampp\sendmail\sendmail.exe" -t"
```

### 2. Edit `sendmail.ini`

```
smtp_server=smtp.gmail.com
smtp_port=587
smtp_ssl=auto
auth_username=your-email@gmail.com
auth_password=your-google-app-password
from=your-email@gmail.com
```

Then restart Apache.

## 🗂️ Project File Structure

PesUFood/  
├── add_to_cart.php  
├── Check_out.php  
├── db.php  
├── index.php  
├── login.php  
├── logout.php  
├── order.php  
├── order_confirm.php  
├── registration.php  
├── forgot_password.php  
├── reset_password.php  
├── verify_otp.php  
├── verify_reset_otp.php  
├── update_cart.php  
├── user_dark_mode.php  
├── temp.php / maintenance.php  
├── test_db.php / test_env.php  
├── db/pesufood_db.sql  
├── composer.json / composer.lock  
├── assets/ (if included)  
├── README.md

## 🛠️ Troubleshooting

- **Database connection issues**: Verify MySQL is running and credentials in `db.php` are correct.  
- **Email not sending**: Ensure PHPMailer is installed, Google App Password is used, and Apache is restarted.  
- **Pages not loading**: Confirm folder is under `htdocs` and filenames are correct.

## 🤝 Contribution

Pull requests are welcome! Fork the repo, make changes, and submit a PR.

## 📜 License

MIT License
