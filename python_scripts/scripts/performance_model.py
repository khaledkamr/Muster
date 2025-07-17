import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import os
from tensorflow.keras.models import Model
import pickle

class StudentPerformanceModel:
    def __init__(self):
        self.scaler = StandardScaler()
        self.kmeans = KMeans(n_clusters=3, random_state=42, n_init=10)
        self.max_scores = {"quiz1": 10, "midterm": 30, "assignments": 20}
        self.cluster_mapping = None
        self.is_trained = False

    def load_data(self):
        """Load and preprocess all necessary data"""
        grades = pd.read_csv("python_scripts/db/grades.csv")
        attendances = pd.read_csv("python_scripts/db/attendances.csv")
        users = pd.read_csv("python_scripts/db/users.csv")
        enrollments = pd.read_csv("python_scripts/db/enrollments.csv")
        courses = pd.read_csv("python_scripts/db/courses.csv")
        return grades, attendances, users, enrollments, courses

    def preprocess_data(self, grades, attendances, users, enrollments, courses):
        """Preprocess data for training"""
        # Filter for second semester courses
        second_semester_courses = courses[courses["semester"] == "second"]
        course_ids = second_semester_courses["id"].unique()

        # Filter enrollments for after August 1st, 2025
        enrollments["enrolled_at"] = pd.to_datetime(enrollments["enrolled_at"])
        filtered_enrollments = enrollments[enrollments["enrolled_at"] >= "2025-08-01"]

        # Filter grades based on filtered enrollments
        grades = grades.merge(
            filtered_enrollments[["student_id", "course_id"]],
            on=["student_id", "course_id"],
            how="inner",
        )

        return grades, attendances, users, course_ids

    def calculate_features(self, grades, attendances, course_id, users):
        """Calculate features for a specific course"""
        # Filter students in the course
        students_in_course = grades[grades["course_id"] == course_id].copy()

        if students_in_course.empty:
            return None

        # Merge with student names
        students_in_course = students_in_course.merge(
            users[["id", "name", "year", "email"]],
            left_on="student_id",
            right_on="id",
            how="left",
        )

        # Calculate attendance percentage
        attendance_in_course = attendances[attendances["course_id"] == course_id]
        attendance_summary = (
            attendance_in_course.groupby("student_id")["status"]
            .value_counts()
            .unstack()
            .fillna(0)
        )
        attendance_summary["total_classes"] = attendance_summary.sum(axis=1)
        attendance_summary["present_count"] = attendance_summary["present"] + (
            attendance_summary["late"] * 0.5
        )
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
        students_in_course["attendance_percentage"] = students_in_course[
            "attendance_percentage"
        ].fillna(0)

        # Handle missing or invalid grades
        grade_columns = ["quiz1", "midterm", "assignments"]
        students_in_course[grade_columns] = students_in_course[grade_columns].fillna(0)
        students_in_course[grade_columns] = students_in_course[grade_columns].clip(lower=0)

        # Normalize grades
        for col in grade_columns:
            students_in_course[f"{col}_normalized"] = (
                students_in_course[col] / self.max_scores[col]
            )

        # Calculate weighted total score
        students_in_course["total_score"] = (
            0.20 * students_in_course["quiz1_normalized"]
            + 0.40 * students_in_course["midterm_normalized"]
            + 0.40 * students_in_course["assignments_normalized"]
        )

        # Normalize attendance_percentage
        students_in_course["attendance_normalized"] = (
            students_in_course["attendance_percentage"] / 100
        )

        return students_in_course

    def train(self):
        """Train the model on all courses"""
        grades, attendances, users, enrollments, courses = self.load_data()
        grades, attendances, users, course_ids = self.preprocess_data(
            grades, attendances, users, enrollments, courses
        )

        all_features = []
        for course_id in course_ids:
            students_in_course = self.calculate_features(grades, attendances, course_id, users)
            if students_in_course is not None:
                features = students_in_course[["total_score", "attendance_normalized"]]
                all_features.append(features)

        if not all_features:
            raise ValueError("No valid data available for training")

        # Combine all features
        all_features = pd.concat(all_features)
        all_features = all_features.fillna(0)

        # Scale features
        features_scaled = self.scaler.fit_transform(all_features)

        # Train KMeans
        self.kmeans.fit(features_scaled)

        # Determine cluster mapping based on mean total_score
        temp_df = all_features.copy()
        temp_df["cluster"] = self.kmeans.labels_
        cluster_means = temp_df.groupby("cluster")["total_score"].mean().sort_values(ascending=False)
        sorted_clusters = cluster_means.index.tolist()
        self.cluster_mapping = {
            sorted_clusters[0]: "High performers",
            sorted_clusters[1]: "Average performers",
            sorted_clusters[2]: "At risk",
        }

        self.is_trained = True

    def predict(self, student_ids, course_id):
        """Predict performance groups for a list of students in a specific course"""
        if not self.is_trained:
            raise ValueError("Model must be trained before making predictions")

        # Ensure student_ids is a list
        if not isinstance(student_ids, list):
            student_ids = [student_ids]

        # Convert student_ids to integers and remove duplicates
        try:
            student_ids = list(set(int(sid) for sid in student_ids))
        except (ValueError, TypeError):
            return []

        grades, attendances, users, enrollments, courses = self.load_data()
        grades, attendances, users, _ = self.preprocess_data(
            grades, attendances, users, enrollments, courses
        )

        # Calculate features for the specific course
        students_in_course = self.calculate_features(grades, attendances, course_id, users)
        
        if students_in_course is None:
            return []

        # Filter for requested student IDs
        student_data = students_in_course[students_in_course["student_id"].isin(student_ids)]
        if student_data.empty:
            return []

        # Prepare features
        features = student_data[["total_score", "attendance_normalized"]]
        features = features.fillna(0)

        # Scale features
        features_scaled = self.scaler.transform(features)

        # Predict clusters
        clusters = self.kmeans.predict(features_scaled)

        # Refine performance groups
        high_performers_count = 0
        average_performers_count = 0
        at_risk_students_count = 0
        results = []
        for idx, student_id in enumerate(student_data["student_id"]):
            total_score = student_data.iloc[idx]["total_score"]
            if total_score > 0.67:
                performance_group = "High performers"
            elif total_score < 0.4:
                performance_group = "At risk"
            else:
                performance_group = "Average performers"

            high_performers_count += 1 if performance_group == "High performers" else 0
            average_performers_count += 1 if performance_group == "Average performers" else 0
            at_risk_students_count += 1 if performance_group == "At risk" else 0

            results.append({
                "student_id": int(student_id),
                "name": student_data.iloc[idx]["name"],
                "year": student_data.iloc[idx]["year"],
                "email": student_data.iloc[idx]["email"],
                "performance_group": performance_group
            })

        return [results, high_performers_count, average_performers_count, at_risk_students_count]

    def save(self, path="python_scripts/models/performance_model.pkl"):
        """Save the model components"""
        model_data = {
            "scaler": self.scaler,
            "kmeans": self.kmeans,
            "max_scores": self.max_scores,
            "cluster_mapping": self.cluster_mapping
        }
        with open(path, "wb") as f:
            pickle.dump(model_data, f)

    @classmethod
    def load(cls, path="python_scripts/models/performance_model.pkl"):
        """Load the model components"""
        with open(path, "rb") as f:
            model_data = pickle.load(f)
        
        model = cls()
        model.scaler = model_data["scaler"]
        model.kmeans = model_data["kmeans"]
        model.max_scores = model_data["max_scores"]
        model.cluster_mapping = model_data["cluster_mapping"]
        model.is_trained = True
        return model

# Example usage
if __name__ == "__main__":
    # Initialize and train model
    model = StudentPerformanceModel()
    model.train()
    
    # Example prediction
    student_ids = [31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50]  # Replace with actual student IDs
    course_id = 7               # Replace with actual course ID
    performance_groups = model.predict(student_ids, course_id)
    
    if performance_groups:
        # for result in performance_groups:
        #     print(f"Student {result['student_id']} in course {result['course_id']}: {result['performance_group']}")
        print(performance_groups)
    else:
        print(f"No data found for students {student_ids} in course {course_id}")
    
    # Save model
    model.save()