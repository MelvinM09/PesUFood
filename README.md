# PesUFood - Online Food Ordering System

PesUFood is a simple online food ordering system built with PHP and Bootstrap. This project allows users to add items to their cart, update quantities, and proceed to checkout.

## Installation Requirements

### Prerequisites

Ensure you have the following installed on your system:

- [XAMPP](https://www.apachefriends.org/download.html) (Includes PHP and MySQL)
- PHP v7.4 or later
- MySQL Database
- Composer (For dependency management)

## Installation Guide

### Step 1: Download & Extract the Files

1. Download the project ZIP file from GitHub.
2. Extract the ZIP file into `C:\xampp\htdocs\pesufood` (Make sure the folder name is `pesufood`).

### Step 2: Configure MySQL Database

1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open `http://localhost/phpmyadmin/` in your browser.
3. Create a new database named `pesufood_db`.
4. Import the provided `pesufood_db.sql` file into the database.

### Step 3: Update Database Configuration

1. Open the `config.php` file located in the root directory.
2. Update the database credentials:

```php
$servername = "localhost";
$username = "root";
$password = ""; // Leave empty for XAMPP
$dbname = "pesufood_db";
```

### Step 4: Configure PHP Mailer (If Needed)

1. Install PHPMailer using Composer:
   ```sh
   composer require phpmailer/phpmailer
   ```
2. Open the `registration.php` and `forgot_password.php` files located in the root directory.
3. Update the following lines with your email credentials:

```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-google-app-password';
```

Alternatively, you can set SMTP credentials via Apache config:
- Open Apache's `httpd.conf` and add:
  ```
  SetEnv SMTP_USERNAME "your-email@gmail.com"
  SetEnv SMTP_PASSWORD "your-app-password"
  ```
- Restart Apache after saving the file.

### Step 5: Start the Server

1. Open XAMPP and start **Apache** and **MySQL**.
2. Open your browser and navigate to:
   ```
   http://localhost/pesufood/
   ```
3. The homepage should load with available food items.

## Features

- Add/remove items from cart
- Update item quantity
- Checkout page with order summary
- PHP Mailer support for order confirmation
- Simple Bootstrap UI for responsiveness

## File Structure

```
PesUFood/
│-- config.php
│-- check_out.php
│-- index.php
│-- order_confirm.php
│-- send_email.php
│-- assets/
│   ├── css/
│   ├── js/
│   ├── images/
│-- db/
│   ├── pesufood_db.sql
```

## Troubleshooting

- If the database does not connect, ensure MySQL is running and the credentials in `config.php` are correct.
- If emails are not sent, enable **Less Secure Apps** or use an **App Password** in Gmail.
- If pages are not loading, ensure the project is in `C:\xampp\htdocs\pesufood`.

## Contribution

Feel free to fork and contribute to the project!

## License

This project is open-source under the MIT License.
