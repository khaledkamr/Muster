import pandas as pd
import os

# Load the data
data_dir = "python_scripts/db/"
grades_df = pd.read_csv(os.path.join(data_dir, "grades.csv"))
attendances_df = pd.read_csv(os.path.join(data_dir, "attendances.csv"))
courses_df = pd.read_csv(os.path.join(data_dir, "courses.csv"))
users_df = pd.read_csv(os.path.join(data_dir, "users.csv"))

# Function to calculate attendance rate
def get_attendance_rate(student_id, course_id):
    records = attendances_df[
        (attendances_df["student_id"] == student_id)
        & (attendances_df["course_id"] == course_id)
    ]
    if records.empty:
        return 0.0
    present = records[records["status"] == "present"].shape[0]
    late = records[records["status"] == "late"].shape[0]
    total = records.shape[0]
    return (present + 0.5 * late) / total if total > 0 else 0.0

# Function to calculate assignment score
def calculate_assignment_score(row):
    max_score = 30
    return row["assignments"] / max_score if pd.notna(row["assignments"]) else 0.0

# Function to calculate performance score
def calculate_performance_score(row):
    max_total = 170
    if pd.isna(row["total"]):
        return 0.0
    return (row["total"] / max_total + row["assignment_score"]) / 2

# Main recommendation function for a single student
def recommend_courses(student_id):
    # Verify student exists and has student role
    if student_id not in users_df[users_df["role"] == "student"]["id"].values:
        return {
            "student_id": student_id,
            "error": "Student ID not found or not a student",
            "recommendations": []
        }

    student_grades = grades_df[grades_df["student_id"] == student_id].copy()
    
    if student_grades.empty:
        return {
            "student_id": student_id,
            "recommendations": [],
            "attendance_rate": 0.0
        }

    # Calculate scores
    student_grades["assignment_score"] = student_grades.apply(calculate_assignment_score, axis=1)
    student_grades["performance_score"] = student_grades.apply(calculate_performance_score, axis=1)

    # Filter high-performing courses (performance score >= 0.5)
    high_performing = student_grades[student_grades["performance_score"] >= 0.5]

    if high_performing.empty:
        return {
            "student_id": student_id,
            "recommendations": [],
            "attendance_rate": 0.0
        }

    # Get difficulties of high-performing courses
    high_difficulties = (
        high_performing["course_id"]
        .map(lambda x: courses_df[courses_df["id"] == x]["difficulty"].values[0])
        .unique()
    )

    # Difficulty mapping
    difficulty_level = {"easy": 1, "medium": 2, "hard": 3}

    # Exclude 'hard' from max difficulty calculation
    allowed_difficulties = [diff for diff in high_difficulties if diff != "hard"]
    max_allowed_difficulty = max(
        [difficulty_level.get(diff, 1) for diff in allowed_difficulties]
    ) if allowed_difficulties else 1

    # Get departments
    departments = (
        student_grades["course_id"]
        .map(lambda x: courses_df[courses_df["id"] == x]["department"].values[0])
        .unique()
    )

    # Initialize recommendations list
    recommended_courses = []

    # Check departments for electives
    for department in departments:
        elective_courses = courses_df[
            (courses_df["type"] == "elective")
            & (courses_df["department"] == department)
        ].copy()

        if not elective_courses.empty:
            elective_courses["difficulty_level"] = elective_courses["difficulty"].map(difficulty_level)
            filtered_courses = elective_courses[
                elective_courses["difficulty_level"].le(max_allowed_difficulty)
            ]
            taken_course_ids = student_grades["course_id"].values
            recommended = filtered_courses[
                ~filtered_courses["id"].isin(taken_course_ids)
            ]

            if not recommended.empty:
                recommended_courses.append(recommended)

    # Combine recommendations
    if not recommended_courses:
        return {
            "student_id": student_id,
            "recommendations": [],
            "attendance_rate": 0.0
        }

    recommended_courses = pd.concat(recommended_courses)
    recommended_courses = recommended_courses.drop(columns=["difficulty_level"])

    # Get professor names
    professor_names = {}
    for prof_id in recommended_courses["professor_id"].unique():
        prof = users_df[users_df["id"] == prof_id]
        if not prof.empty:
            professor_names[prof_id] = prof["name"].values[0]

    # Prepare output data
    recommendations = []
    for _, row in recommended_courses.iterrows():
        recommendations.append({
            "course_id": int(row["id"]),
            "course_name": row["name"],
            "course_code": row["code"],
            "description": row["description"],
            "credit_hours": row["credit_hours"],
            "professor_name": professor_names.get(row["professor_id"], ""),
            "department": row["department"],
            "difficulty": row["difficulty"]
        })

    # Get attendance rate for the first course (if available)
    first_course_id = student_grades["course_id"].iloc[0] if not student_grades.empty else None
    attendance_rate = get_attendance_rate(student_id, first_course_id) if first_course_id else 0.0

    return {
        "student_id": student_id,
        "recommendations": recommendations,
        "attendance_rate": round(attendance_rate * 100, 2)
    }

# Example usage
if __name__ == "__main__":
    example_student_id = users_df[users_df["role"] == "student"]["id"].iloc[0]
    result = recommend_courses(example_student_id)
    print(result)