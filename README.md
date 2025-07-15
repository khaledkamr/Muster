# Muster: University Dashboard System with AI-Driven Analytics

##  Overview

**Muster** is a web-based university dashboard developed as a graduation project at **Misr University for Science and Technology**. It enhances educational decision-making by integrating academic data with AI-powered insights through customized interfaces for **professors**, **students**, and **parents**.

The system provides visualizations and predictive analytics on grades, attendance, assignments, and course data, leveraging cutting-edge AI techniques such as:

-  **Logistic Regression**: Predicts student dropout risk  
-  **LSTM-based RNN**: Forecasts future GPA  
-  **K-Means Clustering**: Categorizes student performance  
-  **Content-Based Filtering**: Recommends personalized courses

---

##  Professor Interface

Professors can manage courses, track performance, and act on predictive insights with dashboards tailored to academic engagement:

- **Course Management**: View/manage course data including enrollment and schedules.
- **Attendance Dashboard**: Charts for attendance rates by course and section type.
- **Grades Dashboard**: Searchable tables for all types of assessments.
- **Assignments Dashboard**: Track student submissions and grades.
- **Predictive Analytics**:
  - Dropout and failure risks using logistic regression.
  - GPA predictions using LSTM-RNN (79.6% accuracy).
  - Performance clustering (high, average, at-risk).
- **Student & Course Metrics**: Aggregated views for strategic decisions.

---

##  Student Interface

Students receive a personalized dashboard to monitor academic progress and receive AI-backed recommendations:

- **Grade Summary**: Breakdown of grades by semester/course.
- **Assignment Tracker**: Completion charts and score trends.
- **Attendance Record**: Weekly and overall attendance visualizations.
- **Course Recommendations**: AI-based elective suggestions tailored to strengths and progress.
- **Course Details**: Full info on enrolled courses and schedules.

<img width="1918" height="1077" alt="Screenshot 2025-07-15 191823" src="https://github.com/user-attachments/assets/add85ddc-4237-4fdc-9ff2-c7da331380c3" />
<img width="1918" height="1078" alt="Screenshot2 2025-07-15 191933" src="https://github.com/user-attachments/assets/51ceb545-d4f4-44a0-945c-b5ea88202b09" />

---

##  Parent Interface

Parents are offered an intuitive dashboard to stay engaged with their child’s academic journey:

- **Grade Summary**: View course grades by semester.
- **Assignment Completion**: See pending/submitted assignments and deadlines.
- **Class Attendance Rate**: Charts showing attendance performance.
- **Child Profile**: Overview of academic major, year, and key metrics.

---

##  Technical Details

###  Technologies

- **Backend**:  
  - PHP Laravel (core logic)  
  - Flask API (AI model integration)  
  - MySQL (data storage)

- **Frontend**:  
  - JavaScript + Bootstrap (responsive UI)  
  - Chart.js (interactive charts)

- **AI Models**:  
  - Python with `scikit-learn` and `TensorFlow`  
    - Logistic Regression (Dropout prediction)  
    - K-means Clustering (Performance segmentation)  
    - Content-Based Filtering (Course suggestions)  
    - LSTM RNN (GPA forecasting)

- **Data**:  
  - Synthetic datasets generated using Laravel Faker (`users`, `grades`, `courses`, `assignments`, etc.)

###  Architecture

- Modular, role-based structure with secure authentication  
- Laravel handles user/session logic and calls Flask for AI predictions  
- Dashboards rendered dynamically using Chart.js and AJAX requests

---

##  Testing and Validation

- **Security & Auth**: Tested using PHPUnit, Selenium, and OWASP ZAP  
- **AI Models**:
  - Logistic Regression: 99% accuracy on synthetic data  
  - LSTM GPA Predictor: 79.6% accuracy (±0.3 GPA points)
- **Interface**: Functional testing for dashboard filters, chart responsiveness, and data accuracy

---

##  Conclusion

**Muster** redefines academic monitoring through AI-powered dashboards, enabling proactive decisions and personalized learning. Built with scalability, privacy, and usability in mind, it's adaptable for universities worldwide.

**Future Enhancements**:
- Mobile app version  
- Real-time data support  
- Expanded AI features (e.g., behavioral analysis, anomaly detection)

---

