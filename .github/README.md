# Hostel Managemen System

![A screenshot of the Home page][Home-Screenshot]

This is a rewrite of Hostelia. This version makes use of the old-popular way of writting php code

---

**NB**: The system is not production ready and may need to be evaluated further.

## System Configuration

The first thing you should do is install the `0001.sql` migration file from your phpmyadmin. Neglecting that will cause errors. The file will create all the necessary tables.

### Managing Admin Accounts

As I was working on another project, I did not add a feature to manage the Admin Accounts. You can easily do so though from PHPMyAdmin. Simply change the `is_student` column to 0 for the user you want to make an admin. If they were already logged in, they may have to log out first to see changes as the status is stored in their session.

By default, I use the common database connection usernames and no password. You can change these in the `config.php` file in the backend folder.

In this file, you can also change the name of the School.

### Routing

The system does not use any type of special routing. However, it is important to note that all files in the **public** directory will be available for access from the browser.

[Home-Screenshot]: ./Hostelia%20Home.png