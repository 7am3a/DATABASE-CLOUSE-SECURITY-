# DATABASE-CLOUSE-SECURITY-
# Secure Book Store

## Overview

Secure Book Store is a web-based bookstore application developed using PHP and MySQL. The system allows users to browse available books, manage book records, and perform common bookstore operations through a user-friendly interface. The project was developed in a local environment using XAMPP and focuses on implementing secure web development practices to protect user and database information.

## Features

* View available books
* Add new books
* Update existing book information
* Delete book records
* Search for books
* User-friendly web interface
* Secure database interaction using prepared statements
* Input validation and sanitization
* Protection against common web vulnerabilities

## Technologies Used

* PHP
* MySQL
* HTML5
* CSS3
* JavaScript
* Bootstrap
* XAMPP (Apache & MySQL)

## Project Structure

```text
SecureBookStore/
│── css/                 # Stylesheets
│── js/                  # JavaScript files
│── images/              # Images and assets
│── includes/            # Database connection and reusable files
│── database/            # SQL database file
│── index.php            # Home page
│── books.php            # Book management
│── add_book.php         # Add a new book
│── edit_book.php        # Update book details
│── delete_book.php      # Delete a book
│── search.php           # Search functionality
└── README.md
```

## Installation

### Requirements

* XAMPP
* PHP 8.0 or later
* MySQL
* Web browser

### Setup Instructions

1. Install and open XAMPP.
2. Start the **Apache** and **MySQL** services.
3. Copy the project folder into the `htdocs` directory.

Example:

```text
C:\xampp\htdocs\SecureBookStore
```

4. Open **phpMyAdmin**.
5. Create a new database.
6. Import the SQL file located in the `database` folder.
7. Open your browser and navigate to:

```text
http://localhost/SecureBookStore/
```

## Database

The application uses MySQL to store:

* Book information
* Categories
* User information (if enabled)
* Inventory records

## Security Features

* Prepared statements to prevent SQL Injection
* Server-side input validation
* Client-side form validation
* Data sanitization
* Secure database connection
* Error handling without exposing sensitive information

## Future Improvements

* User authentication and authorization
* Shopping cart functionality
* Online payment integration
* Order management
* Responsive mobile interface
* Admin dashboard with analytics

## Author

**Hamza Baharoon**

Bachelor of Computer Science (Cybersecurity)

Multimedia University (MMU)

## License

This project was developed for educational purposes as part of a university coursework assignment.
