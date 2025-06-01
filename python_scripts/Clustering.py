import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import os

# Load data
grades = pd.read_csv("python_scripts/db/grades.csv")
attendances = pd.read_csv("python_scripts/db/attendances.csv")
users = pd.read_csv("python_scripts/db/users.csv")
enrollments = pd.read_csv("python_scripts/db/enrollments.csv")
courses = pd.read_csv("python_scripts/db/courses.csv")

# Filter for second semester courses
second_semester_courses = courses[courses["semester"] == "second"]
course_ids = sorted(second_semester_courses["id"].unique())

# Filter enrollments for after August 1st, 2025
enrollments["enrolled_at"] = pd.to_datetime(enrollments["enrolled_at"])
filtered_enrollments = enrollments[enrollments["enrolled_at"] >= "2025-08-01"]

# Filter grades based on filtered enrollments
grades = grades.merge(
    filtered_enrollments[["student_id", "course_id"]],
    on=["student_id", "course_id"],
    how="inner",
)

# Dictionary to store results for all courses
all_courses_results = {}

for course_id_input in course_ids:
    print(f"\nProcessing Course ID: {course_id_input}")

    # Filter students in the course
    students_in_course = grades[grades["course_id"] == course_id_input].copy()

    # Check if any students are found for the course
    if students_in_course.empty:
        print(f"No students found for course ID {course_id_input}. Skipping...")
        continue

    # Merge with student names
    students_in_course = students_in_course.merge(
        users[["id", "name", "year", "email"]],
        left_on="student_id",
        right_on="id",
        how="left",
    )

    # Calculate attendance percentage
    attendance_in_course = attendances[attendances["course_id"] == course_id_input]
    attendance_summary = (
        attendance_in_course.groupby("student_id")["status"]
        .value_counts()
        .unstack()
        .fillna(0)
    )
    attendance_summary["total_classes"] = attendance_summary.sum(axis=1)
    attendance_summary["present_count"] = attendance_summary["present"] + (
        attendance_summary["late"] * 0.5
    )  # Late as half-present
    attendance_summary["attendance_percentage"] = (
        attendance_summary["present_count"] / attendance_summary["total_classes"]
    ) * 100

    # Merge attendance percentage
    students_in_course = students_in_course.merge(
        attendance_summary[["attendance_percentage"]],
        left_on="student_id",
        right_index=True,
        how="left",
    )

    # Handle missing attendance data
    students_in_course["attendance_percentage"] = students_in_course[
        "attendance_percentage"
    ].fillna(0)

    # Handle missing or invalid grades
    grade_columns = ["quiz1", "midterm", "assignments"]
    students_in_course[grade_columns] = students_in_course[grade_columns].fillna(0)
    students_in_course[grade_columns] = students_in_course[grade_columns].clip(
        lower=0
    )  # Ensure non-negative

    # Normalize grades
    max_scores = {
        "quiz1": 10,
        "midterm": 30,
        "assignments": 20,
    }
    for col in grade_columns:
        students_in_course[f"{col}_normalized"] = (
            students_in_course[col] / max_scores[col]
        )

    # Calculate weighted total score
    students_in_course["total_score"] = (
        0.20 * students_in_course["quiz1_normalized"]
        + 0.40 * students_in_course["midterm_normalized"]
        + 0.40 * students_in_course["assignments_normalized"]
    )

    # Normalize attendance_percentage to 0–1
    students_in_course["attendance_normalized"] = (
        students_in_course["attendance_percentage"] / 100
    )

    # Prepare features for clustering
    features = students_in_course[["total_score", "attendance_normalized"]]
    features = features.fillna(0)

    # Scale features using StandardScaler
    scaler = StandardScaler()
    features_scaled = scaler.fit_transform(features)

    # Apply KMeans clustering with 3 clusters
    kmeans = KMeans(n_clusters=3, random_state=42, n_init=10)
    students_in_course["cluster"] = kmeans.fit_predict(features_scaled)

    # Calculate cluster statistics
    cluster_means = (
        students_in_course.groupby("cluster")["total_score"]
        .mean()
        .sort_values(ascending=False)
    )
    sorted_clusters = cluster_means.index.tolist()
    cluster_mapping = {
        sorted_clusters[0]: "High performers",
        sorted_clusters[1]: "Average performers",
        sorted_clusters[2]: "At risk",
    }

    # Assign initial performance groups
    students_in_course["Performance Group"] = students_in_course["cluster"].map(
        cluster_mapping
    )

    # Refine performance groups
    def refine_performance_group(row):
        total_score = row["total_score"]
        final = row["final"]

        if total_score > 0.67:
            return "High performers"
        elif total_score < 0.4:
            return "At risk"
        else:
            return "Average performers"

    students_in_course["Performance Group"] = students_in_course.apply(
        refine_performance_group, axis=1
    )

    # Get course name
    course_name = courses.loc[courses["id"] == course_id_input, "name"].values[0]
    course_code = courses.loc[courses["id"] == course_id_input, "code"].values[0]
    print(f"Processed Course: {course_name} ({course_code})")

    # Prepare final columns for this course
    final_columns = [
        "name",
        "student_id",
        "year",
        "email",
        "quiz1",
        "midterm",
        "assignments",
        "total",
        "attendance_percentage",
        "total_score",
        "Performance Group",
    ]

    # Store results for this course
    performance_counts = students_in_course["Performance Group"].value_counts()

    # Create a dictionary of students with student_id as key
    students_dict = {}
    for _, student in students_in_course[final_columns].iterrows():
        student_id = str(student["student_id"])
        students_dict[student_id] = {
            "name": student["name"],
            "year": student["year"],
            "email": student["email"],
            "quiz1": student["quiz1"],
            "midterm": student["midterm"],
            "assignments": student["assignments"],
            "total": student["total"],
            "attendance_percentage": student["attendance_percentage"],
            "total_score": student["total_score"],
            "performance_group": student["Performance Group"],
        }

    all_courses_results[str(course_id_input)] = {
        "course_code": course_code,
        "high_performance_count": int(performance_counts.get("High performers", 0)),
        "average_performance_count": int(
            performance_counts.get("Average performers", 0)
        ),
        "at_risk_students_count": int(performance_counts.get("At risk", 0)),
        "students": students_dict,
    }

# Save final output
output_file = "python_scripts/results/clustering_results.json"
try:
    with open(output_file, "w") as f:
        pd.Series(all_courses_results).to_json(f, indent=4)
    print(f"\nClustering Results for all courses saved to {output_file}\n")
except PermissionError:
    print(
        f"\nError: Cannot write to {output_file}. Ensure the file is not open in another program and try again.\n"
    )
    raise
