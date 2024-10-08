# Bookmarks
An application for storing and sharing bookmarks written in PHP.

## Requirements
  * PHP 8.2.0 or later
  * MySQL 5.7 or later
  * SMTP mail server
  * Composer
  * Git (optional, if you decide to clone the repository instead of manual downloading)

## Installation
**1.** Get the application code.

**Option 1.** Download it and unzip the ZIP file.

**Option 2.** Clone the repository:

```
git clone https://github.com/Patrick642/Bookmarks.git
```

**2.** Go to root directory of the application:

```
cd Bookmarks
```

**3.** Install required packages and create autoload file:

```
composer install
```

**4.** Create a new database.

**5.** Import `data/bookmarks.sql` file to create a schema for the newly created database.

**6.** Go to `config/db.php` and set database connection details.

**7.** Go to `config/mailer.php` and set mailer settings.
