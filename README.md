Task Management System

Project Overview

This repository contains a simple, yet robust, Task Management System built using Core PHP and MySQL. It is structured into distinct Admin and User modules, focusing on secure authentication, comprehensive CRUD operations (Create, Read, Update, Delete) for projects and tasks, and dynamic, deadline-based user interfaces.

The system features real-time deadline tracking and user-specific dashboards to manage assigned workload efficiently.

Technical Stack

Backend: Core PHP

Database: MySQL (via XAMPP/phpMyAdmin)

Frontend: Bootstrap 5.3 (for clean UI and responsiveness)

Logic: Date-based calculations using PHP's native time functions.

Setup Instructions

Follow these steps to set up and run the Task Management System on your local XAMPP server.

1. Prerequisites

XAMPP: Ensure XAMPP is installed and running (Apache and MySQL services are started).

2. Project Installation

Clone the Repository:

git clone [YOUR_REPOSITORY_URL] Task_Management_System


Move to htdocs: Place the cloned Task_Management_System folder directly into your XAMPP's web root directory (C:\xampp\htdocs\ or equivalent).

3. Database Setup (MySQL via phpMyAdmin)

Since this is a PHP/MySQL application running on XAMPP, we will use phpMyAdmin for database management.

Access phpMyAdmin: Open your web browser and navigate to http://localhost/phpmyadmin/.

Create Database:

Click on the "New" button on the left sidebar.

Name the new database: task_manager_db

Click "Create".

Import Schema:

Select the newly created task_manager_db from the left sidebar.

Click the "Import" tab in the main panel.

Click "Choose file" and select the task_manager_db.sql file provided in this repository.

Scroll down and click "Import".

4. Database Connection

Locate your database connection file (e.g., db_connect.php).

Ensure the credentials match the standard XAMPP configuration:

$host = 'localhost';
$db = 'task_manager_db'; // The name you created
$user = 'root';        // Default XAMPP user
$pass = '';            // Default XAMPP password (usually empty)


5. Run the Application

Ensure Apache and MySQL are running in your XAMPP control panel.

Open your browser and navigate to the starting URL:

Start URL: http://localhost/Task_Management_System/index.php

Application Modules & Access Details

Default Login Credentials

Use the following credentials to test the system:

Role

Email

Password

URL

Admin

admin@task.com

adminpass

User

1. u-id: user@task.com, password: userpass
2. u-id: user@01task.com, password: userpass
3. u-id: user@02task.com, password: userpass
4. u-id: user@03task.com, password: userpass
5. u-id: user@04task.com, password: userpass
6. u-id: user@05task.com, password: userpass
7. u-id: user@06task.com, password: userpass
8. u-id: user@07task.com, password: userpass

Module 1: Admin Module (Project & Task Management)

The Admin is the operational manager of the system.

Admin Dashboard: Central view for managing projects and creating tasks.

Project Management (CRUD):

Create, view, edit, and delete projects.

Assign projects to users (via dropdown).

Task Management (CRUD):

Create multiple tasks under specific projects.

Set Task Title, Description, Priority (Low/Medium/High), Assigned User, and Deadline.

View all tasks grouped by their parent project.

Module 2: User Module (Dashboard & Profile)

The User manages their assigned work and personal information.

My Dashboard:

Displays only the projects and tasks assigned to the logged-in user.

Projects are displayed as collapsible sections, showing associated tasks below.

Users can update task status: Not Started , In Progress , Completed.

My Profile:

Form to update Name, Email, and Password.

Module 3: Deadline Tracking Module

The system includes dynamic visual indicators for time-sensitive elements.

Status

Color Indicator

PHP Logic

Passed

🟥 Red

End Date < Current Date

Today

🟧 Orange

End Date == Current Date

Future

🟩 Green

End Date > Current Date

This logic is implemented using PHP's date(), strtotime(), and comparison operators, adhering to the requirement of not using external APIs.
