Muster: University Dashboard System with AI-Driven Analytics
Overview
Muster is a web-based university dashboard system designed to enhance educational decision-making by integrating academic data and delivering AI-driven insights through role-specific interfaces for professors, students, and parents. Built as a graduation project at Misr University for Science and Technology, Muster centralizes data on grades, attendance, assignments, and courses, providing tailored visualizations and predictive analytics. The system leverages advanced AI techniques, including logistic regression for dropout prediction, K-means clustering for student performance categorization, content-based filtering for course recommendations, and LSTM-based RNN for GPA forecasting. Developed using the Waterfall methodology, Muster employs PHP Laravel for backend logic, Flask API for AI integration, MySQL for data storage, JavaScript with Bootstrap for responsive interfaces, and Chart.js for dynamic visualizations. Synthetic datasets ensure robust testing while maintaining privacy, making Muster a scalable and secure solution for higher education institutions.

Professor Interface
The professor interface empowers educators to manage courses, monitor student performance, and access predictive insights to support academic interventions. It provides comprehensive dashboards with interactive visualizations tailored to course-level and student-level analytics.

Course Management:Professors can view and manage course details, including enrollment statistics, course schedules, and associated assignments, linked to the courses and enrollments datasets in the MySQL database.

Attendance Dashboard:Displays pie charts for attendance status (present, absent, late) and weekly trend charts, filterable by course and section type (e.g., lectures, labs). Data is sourced from the attendances dataset, with late attendance weighted as half-present.

Grades Dashboard:Provides searchable tables showing student scores for quizzes, midterms, assignments, projects, and final exams, derived from the grades dataset. Professors can filter by semester or course for detailed performance analysis.

Assignments Dashboard:Features pie charts for submitted vs. pending assignments and detailed tables listing student IDs, assignment titles, statuses, and scores, linked to the assignments and assignment_submissions datasets.

Predictive Analytics:Displays AI-driven insights, including:

Dropout Risk Prediction: Logistic regression model (99% accuracy) identifies at-risk students based on attendance, grades, and assignment submissions.
Course Failure Risk: Derived from logistic regression and grade data to highlight potential failures.
GPA Forecasts: LSTM-based RNN predicts next-semester GPAs (79.6% accuracy within ±0.3 GPA points) using historical GPA sequences.


Clustering Analysis:K-means clustering categorizes students into high, average, and at-risk performance groups based on normalized academic scores and attendance percentages, visualized as charts and tables for targeted interventions.

Student Demographics and Course Metrics:Shows aggregate data, such as enrollment statistics and course-level performance summaries (e.g., average grades, attendance rates), to support strategic teaching decisions.



Student Interface
The student interface provides a personalized view of academic progress, enabling students to track their performance, manage assignments, and receive tailored course recommendations. It is designed for ease of use and accessibility across devices.

Grade Summary:Displays a filterable table of course grades (e.g., quizzes, midterms, finals) from the grades dataset, with options to view detailed breakdowns by semester or course, helping students monitor their academic standing.

Assignment Tracker:Shows completion charts (submitted vs. pending) and score trend charts, alongside a table detailing assignment information (course, professor, status, due date, score) from the assignment_submissions dataset.

Attendance Record:Presents round charts for total attendance rates and weekly trend charts, filterable by section type (e.g., lectures, labs), sourced from the attendances dataset to encourage consistent participation.

Course Recommendations:Uses a content-based filtering model to suggest elective courses based on academic strengths (e.g., normalized assignment scores, composite performance scores) and course difficulty, drawn from the courses and grades datasets. Recommendations exclude previously taken courses and match the student's capability level.

Course Details:Provides access to enrolled course information, including descriptions, schedules, and professor details, linked to the enrollments and courses datasets, enabling students to stay informed about their academic commitments.



Parent Interface
The parent interface allows guardians to monitor their child's academic progress, fostering engagement with the educational process through accessible and intuitive dashboards.

Grade Summary:Displays a table of the child’s course grades, filterable by semester, sourced from the grades dataset, enabling parents to track academic performance across courses.

Assignment Completion:Provides a searchable table listing assignment statuses, deadlines, and scores from the assignment_submissions dataset, helping parents ensure timely submissions.

Class Attendance Rate:Shows charts and summaries of the child’s attendance, filterable by section type (e.g., lectures, labs), derived from the attendances dataset, to monitor engagement and consistency.

Child Profile:Offers a summary of the child’s academic details, including major, academic year, and performance metrics, linked to the users dataset, providing a holistic view of their educational journey.



Technical Details

Technologies:  

Backend: PHP Laravel for server-side logic, Flask API for AI model integration, MySQL for data storage.  
Frontend: JavaScript with Bootstrap for responsive interfaces, Chart.js for interactive visualizations.  
AI Models: Python with scikit-learn and TensorFlow for logistic regression (dropout prediction), K-means clustering (student categorization), content-based filtering (course recommendations), and LSTM-based RNN (GPA forecasting).  
Data: Synthetic datasets (users, courses, grades, enrollments, assignments, assignment_submissions, attendances) generated using Laravel’s Faker library for testing and validation.


Architecture:A modular, scalable design with role-based authentication, where the Laravel backend retrieves preprocessed AI outputs from the Flask API and user-specific data from MySQL, rendering tailored dashboards via Chart.js.

Testing and Validation:  

User authentication tested with PHPUnit, Selenium, and OWASP ZAP for security.  
AI models validated with synthetic datasets, achieving 99% accuracy for logistic regression and 79.6% for LSTM-based RNN.  
Dashboards tested for functionality and usability, ensuring accurate data display and interactive filters.




Conclusion
Muster represents a transformative approach to educational data management, integrating AI-driven analytics to empower professors, students, and parents. By providing role-specific dashboards with actionable insights, the system enhances academic monitoring, supports timely interventions, and personalizes the learning experience. Its modular architecture and robust technologies ensure scalability, security, and user-friendliness, making it adaptable for university settings worldwide. The use of synthetic data has validated its effectiveness, while future enhancements, such as mobile app integration and real-time data support, promise to further elevate its impact on higher education.
