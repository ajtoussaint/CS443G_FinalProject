# Final Project for CS443G AU23 Semester

# User Guide

## Table of Contetnts
### 1. Installation and 
#### 1.1 Running Locally
#### 1.2 Configuration
### 2. Operation
#### 2.1. Navigating the Application
#### 2.2. Regulations
#### 2.3. Facilities
#### 2.4 Units and Limits

## 1.Installation

### 1.1.Running Locally

1. To run the applicaiton first download [all files from the main folder of this directory](https://github.com/ajtoussaint/CS443G_FinalProject).
2.  You will also need to download and install [XAMPP](https://www.apachefriends.org/). The Application was made with control panel v3.3.0.
3.  Once You have downloaded and installed the required software make a copy of the "Industry" directory in the "Client" directory of this project and paste it into the "xampp/htdocs" directory. To navigate to this folder start the xampp control panel and click on the Explorer button on the right side of the control panel then open the htdocs folder. After this has been done you may close the file explorer and return to the xampp control panel.
4.  Click the action buttons labeled "Start" for both the Apache and MySQL Modules. The Module names should be highlighted green and the start buttons should now read "stop". If this does not work consult the [XAMPP documentation](https://www.apachefriends.org/).
5.  Open the Admin page of the MySQL module by clicking on the "Admin" button that is next to the stop button for the module. This will open the admin page in your default web browser
6.  On the left panel of the admin page click on the option "New". In the create database field enter the name "industry" (no quotation marks) and keep the default database type in the dropdown. Then click create. 
7.  Navigate to the "Import" page using the horizontal navigation bar at the top of the admin page. (If you dont see int make sure you have the "industry" database you just created selected on the left side).
8.  On the import page choose one of the .sql files included in the Database folder of this project as the file to import. You may either choose the empty database to import the structure alone or choose the sample database if you would like some sample data for demonstration purposes. Keep all of the default settings, scroll to the bottom of the page and click the "import" button.
9.  The database should successfully import. If this does not work ensure you are importing into a newly created databae that is not populated with any data. Otherwise, consult the SQL error produced.
10.  To use the app open any web browser and type "localhost/industry/". This will pull up the home page of the application.

### 1.2.Configuration

If you wish to run the application using a database name other than 'industry' change the value in the Clien/Industry/config.php file.

## 2.Operation

### 2.1 Navigating the Application
