# Final Project for CS443G AU23 Semester

# User Guide

## Table of Contetnts
### 1. [Installation](#1installation)
#### 1.1 [Running Locally](#11running-locally)
#### 1.2 [Configuration](#12configuration)
### 2. [Operation](#2operation)
#### 2.1. [Navigating the Application](#21navigating-the-application)
#### 2.2. [Regulations](#22regulations)
#### 2.3. [Facilities](#23facilities)
#### 2.4 [Units and Limits](#24units-and-limits)

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

### 2.1.Navigating the Application
The structure of the application is as follows:\
Industry\
|_Index.php\
|_Regulations.php\
|_Facilities.php\
__|_Facility.php

From the home page you can navigate to either the Regulations or Facilities page using the appropriate links. From either page you can also navigate back to the home page using the "Return to Home" link at the top of each page. From the Facilities page, if any facility data has been added, you can click on the Agency Interest Number or facility name shown in the table to navigate to a Facility Page that displays details about the facility including any emission units and what limits they are subject to. Each facility page also has a link to return to the list of all facilities.

### 2.2.Regulations

The regulations page will display a list of all regulations recorded in the database. From this page you can add, edit, or delete regulations.

To add a new regulation to the list click the "Add New Regulation" button and include the regulations citation and text then click "Add Regulation". To cancel the addition click the X at the top right of the form. Once a regulation is added it should appear in the list below. Be aware that each regulation must have a unique citation. A regulation with a duplicate cittaion cannot be added.

To edit an existing regulation's text click the edit button that is in the same table row as that regulation. A text input will appear allowing you to edit the text of the regulation. When you are finished click the update button to the right of the input box to save the changes or click cancel to discard them. Be aware that only one regulation may be edited at a time. To edit another regulation, first save or discard the changes to the regulation you are currently editing.

To delete a regulation from the database simply click the delete button next to the regulation and confirm the deletion in the dialogue at the top of the web page.

### 2.3.Facilities

The facilities page includes an ordered list of facilities with their Agency interest (AI) numbers and names displayed. From this page you can add new facilites and navigate to the details of a specific facility by clicking on it.

To change the odering of facilites select the desired parameters in the "Order By:" menu and click the filter button. 

To add a new facility click on the "Add New Facility" button at the top of the page, input the facilities information into the form, and click add facility. Be aware that each facility must have a unique AI number, so a facility cannot be added if the AI number is already in use.

### 2.4.Units and Limits

By clicking on a specific facility on the facilities page you can access the details of the facility. On this page you can also add, edit, and delete emission units from the facility and add, emission limits to the units.

To add a new unit click the "Add Unit" button and fill out the required information and click "Add Unit" at the bottom of the form. Be aware that the Unit ID must be unique from other units in the facility. The unit name may be the same so if you have multiple units that are identical they can be given the same name as long as they have different IDs. The unit capacity will be rounded to the nearest tenth and fuel consumption will be rounded to the nearest hundreth.

To edit the information of a unit once it has been added click the edit button associated with that unit. Once you have made your edits either save them by clicking update or discard them by clicking cancel.

To delete a unit from the database simply click the delete button and confrim that you wish to delete the unit.

To add an emission limit to a specific unit click the "Add Limit" button associated with that unit and scroll to the top of the page. You will have the option of reusing existing limit or creating a new limit to add to the unit.

Once a limit has been added you can hover your mouse over the "i" icon to view additional details of the limit. 

To delete a limit press the "X" button next to the limit. Be aware that once the last instance of a limit is deleted it will be removed from the list of existing limits that can be reused.
