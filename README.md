# Muster: University Dashboard System with AI-Driven Analytics

## Overview

**Muster** is a web-based university dashboard developed as a graduation project at **Misr University for Science and Technology**. It enhances educational decision-making by integrating academic data with AI-powered insights through customized interfaces for **professors**, **admins**, **students**, and **parents**.

The system provides visualizations and predictive analytics on grades, attendance, assignments, and course data, leveraging cutting-edge AI techniques such as:

-  **Logistic Regression**: Predicts student dropout risk  
-  **LSTM-based RNN**: Forecasts future GPA  
-  **K-Means Clustering**: Categorizes student performance  
-  **Content-Based Filtering**: Recommends personalized courses

---

## Database Schema

<img width="1615" height="900" alt="Screenshot 2025-07-31 152757" src="https://github.com/user-attachments/assets/4e7a3317-6a72-4332-99b7-db25490c4bd7" />

---

##  Professor Interface

Professors can manage courses, track students performance, and act on predictive insights with dashboards tailored to academic engagement:

- **Students Dashboard**: view all students and cluster them as groups based on their performance.
- **Exams Dashboard**: view all assessments with detailed statistics.
- **Grades Dashboard**: 
  - Searchable tables for all the students with all types of assessments.
  - Classify students (***on track*** or ***at risk***) and send feedbacks.
- **Attendance Dashboard**: 
  - view all the attendances over the weeks with the session type.
  - charts to visualize the weekly attendance trend and attendance distribution.
- **Assignments Dashboard**: Track student submissions and scores.
- **Student & Course Metrics**: view all the academic statistics for one student in specific course.
  
<img width="1917" height="907" alt="prof" src="https://github.com/user-attachments/assets/77f3f9f6-097d-4274-a5dc-4cad4af24d01" /> 

---

## Admin Interface

Admins can manage courses and users with full CRUD operations. They can also view analytics on courses, users, and professor feedbacks.

- **Courses Dashboards**: 
  - manage CRUD operations on courses.
  - charts to visualize courses analytics.
- **Users Dashboard**: 
  - manage CRUD operations on users
  - charts to visualize users analytics (professors, students).
- **Feedbacks Dashboard**: 
  - track professors feedback about students.
  - track students feedback about professors.
  - track students feedback about courses content.
 
<img width="1918" height="1078" alt="Screenshot 2025-07-31 151801" src="https://github.com/user-attachments/assets/c2e0fd20-afcb-4e2d-8c94-1eff727e40d2" />

---

##  Student Interface

Students receive a personalized dashboard to monitor academic progress and receive AI-backed recommendations:

- **Courses Dashboard**: 
  - view all current semester courses with overview and progress.
  - courses total grades chart to visualize difference performance between courses.
  - GPA and CGPA prediction for the current semester based on performance.
- **Course Recommendations**: AI-based elective courses suggestions tailored to strengths and progress.
- **Grade Summary**: 
  - show grades statistics for selected semester.
  - charts to visualize grades distribution and GPA trend over semesters.
- **Assignment Tracker**: 
  - view assignments status and upcoming assignments.
  - Completion charts and score trends.
- **Attendance Record**: 
  - Weekly and overall attendance visualizations with attendance rate.
  - show attendance details and rate for each course.
- **Course Details**: view all the academic statistics for each course.
 
<div style="display: flex;">
  <img width="959" height="538" alt="Screenshot 2025-07-15 191823" src="https://github.com/user-attachments/assets/add85ddc-4237-4fdc-9ff2-c7da331380c3" />
  <img width="959" height="538" alt="Screenshot2 2025-07-15 191933" src="https://github.com/user-attachments/assets/51ceb545-d4f4-44a0-945c-b5ea88202b09" /> 
</div>

---

##  Parent Interface

Parents are offered an intuitive dashboard to stay engaged with their child’s academic journey:

- **Child Dashboards**: Dashboard for each child if having more than one.
- **Courses Dashboard**: current semester courses with overview and progress.
- **Grade Summary**: show grades statistics for selected semester.
- **Assignment Completion**: See pending/submitted assignments and deadlines.
- **Class Attendance Rate**: Charts showing attendance performance and rate.
- **Professors Feedbacks**: Review professors feedbacks about student.

<img width="1918" height="1078" alt="par" src="https://github.com/user-attachments/assets/8d7fabfb-a0e7-4e61-bcd8-acc0d7fb5765" /> 

---

##  Technical Details

###  Technologies

  - PHP Laravel (core logic)  
  - Flask API (AI model integration)  
  - MySQL (data storage)

  - JavaScript + Bootstrap (responsive UI)  
  - Chart.js (interactive charts)

  - Python with `scikit-learn` and `TensorFlow`  
    - Logistic Regression (Dropout prediction)  
    - K-means Clustering (Performance segmentation)  
    - Content-Based Filtering (Course suggestions)  
    - LSTM RNN (GPA forecasting)

---

## How to use

### Prerequisites
- PHP 8.2.12
- Composer
- MySQL
- web server (Apache/Nginx)

### Install project
```bash
git clone https://github.com/khaledkamr/Muster.git
```

### configurations
- Copy the `.env.example` file to create a `.env` file:
  ```bash
  cp .env.example .env
  ```

- Edit the `.env` file to configure your environment settings, such as:
    - Database connection.
    - App URL (`APP_URL`) and other settings as required.

- Generate an application key:
  ```bash
  php artisan key:generate
  ```

### Set Up the Database

- Create a database in your database management system.
- Update the `.env` file with your database credentials.
- Run migrations to set up the database schema:
  ```bash
  php artisan migrate
  ```
- Run database seeders:
  ```bash
  php artisan db:seed
  ```

### Run the Application
- Run laravel server
  ```bash
  php artisan serve
  ```
  This will start the server, typically at `http://localhost:8000`.

- Run flask APIs
  ```bash
  python python_scripts/APIs/model_endpoints.py
  ```
  This will start the server, typically at `http://127.0.0.1:5000`.
