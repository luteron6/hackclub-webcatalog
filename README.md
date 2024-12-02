



# Garment-Catalog
This project is a simple web-based inventory catalog system running on a Raspberry Pi. It serves as a database to manage garment products, their sizes, colors, bins, and stock quantities. The web application allows for easy addition, editing, and management of inventory entries.

This repo is the location for the backup of Awesome T-Shirt Printing garment catalog database.
The database runs on a Raspberry Pi LAMP (Linux, Apache, MySQL, PHP) server.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
  - [Requirements](#requirements)
  - [Setup Steps](#setup)
- [Usage](#usage)
  - [Adding New Products](#adding-new-products)
  - [Editing Products](#editing-products)
  - [Managing Stock](#managing-stock)
  - [Filtering Products](#filtering-products)
- [Contributing](#contributing)
- [Contact](#contact)


## Features

- **Search & Filter:** Search for products by name, SKU, color, or bin location.
- **Product Management:** Add new products, update existing ones, and manage stock quantities in different sizes.
- **Stock Management:** Track and update stock for garments stored in different bins, including multiple sizes (e.g., Youth XS, Small, Medium, Large, etc.).
- **Editable Inventory:** Easily update details like product name, SKU, colors, bin location, and stock quantities.
  
## Installation

### Requirements

- **Raspberry Pi** (or any device running a web server with PHP and MySQL support)
- **PHP**: Version 7.4+ (for running `index.php`)
- **MySQL/MariaDB**: For database management
- **Web server**: Apache or Nginx

### Setup

1. **Install Apache, PHP, and MySQL (MariaDB)** on your Raspberry Pi or server:
   ```bash
   sudo apt update
   sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql
2. ** Clone the repository to your server:
   ```git clone https://github.com/yourusername/raspberry-pi-lamp-server.git
   cd raspberry-pi-lamp-server
3. Setup the MySQL database
   `mysql -u root -p` (if on Raspberry Pi use `mysql -u root`)
4. Configure Database Connection
   Edit the  `index.php` file to update your database credentials:
   ```define('DB_HOST', 'localhost');
   define('DB_NAME', 'inventory');  // Change to your database name
   define('DB_USER', 'screenprinting');  // Change to your MySQL username
   define('DB_PASS', 'arch');  // Change to your MySQL password
5. Move `index.php` to Apache's default web directory (probably `/var/www/html/`):
   `sudo mv raspberry-pi-lamp-server/* /var/www/html/`
6. Restart proper services:
   ```sudo systemctl restart apache2
   sudo systemctl restart mysql
7. Access the Web Application
   Open your web browser and navigate to `http://<raspberry-pi-ip-address>/`
   You should see the inventory catalog page. Navigating to `/phpmyadmin` gives access to the backend SQL database.


## Usage
### Adding New Products
To add a new product to the catalog:

Use the `Add New Product` form at the bottom of the page.
Enter the product details (Name, SKU, Color, Quantities,) and submit the form.

### Editing Products

To edit an existing product:

Use the `Edit` button next to the product listing.
Modify the product details and submit the form to update the product in the database.

### Managing Stock
You can add new stock entries for existing products or edit existing stock quantities:

Use the `Add Stock for Product` form to enter additional stock into specific bins.
To update stock quantities, use the Edit Stock option.

### Filtering Products
You can filter products by color and bin location using the filter options at the top of the catalog table. Simply select the desired color or bin and hit the Search button. Leave the search bar empty to search only by color and/or bin #.

### Contributing
Feel free to fork this repository and submit pull requests. If you encounter any bugs or issues, please file an issue.

### Contact
For any questions, feel free to reach out to me.

   
