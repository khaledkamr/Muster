import pandas as pd
import json

# Load the data
grades_df = pd.read_csv("python_scripts/db/grades.csv")
attendances_df = pd.read_csv("python_scripts/db/attendances.csv")
courses_df = pd.read_csv("python_scripts/db/courses.csv")
users_df = pd.read_csv("python_scripts/db/users.csv")


# Function to calculate attendance rate
def get_attendance_rate(student_id, course_id):
    records = attendances_df[
        (attendances_df["student_id"] == student_id)
        & (attendances_df["course_id"] == course_id)
    ]
    if records.empty:
        return 0
    present = records[records["status"] == "present"].shape[0]
    late = records[records["status"] == "late"].shape[0]
    total = records.shape[0]
    return (present + 0.5 * late) / total if total > 0 else 0


# Function to calculate assignment score
def calculate_assignment_score(row):
    max_score = 30
    return row["assignments"] / max_score


# Function to calculate performance score
def calculate_performance_score(row):
    max_total = 170
    if pd.isna(row["total"]):
        return 0
    return (row["total"] / max_total + row["assignment_score"]) / 2


# Main recommendation function
def recommend_courses(student_id):
    student_grades = grades_df[grades_df["student_id"] == student_id].copy()

    if student_grades.empty:
        print("No data for this student.")
        return pd.DataFrame()  # Return empty DataFrame for CSV

    # Calculate scores
    student_grades["assignment_score"] = student_grades.apply(
        calculate_assignment_score, axis=1
    )
    student_grades["performance_score"] = student_grades.apply(
        calculate_performance_score, axis=1
    )

    # Filter high-performing courses (performance score >= 0.5)
    high_performing = student_grades[student_grades["performance_score"] >= 0.5]

    if high_performing.empty:
        print("No high-performing courses.")
        return pd.DataFrame(
            [
                {
                    "student_id": student_id,
                    "course_id": None,
                    "course_name": None,
                    "course_code": None,
                    "description": None,
                    "professor_name": None,
                    "department": None,
                    "difficulty": None,
                }
            ]
        )

    # Get difficulties of high-performing courses
    high_difficulties = (
        high_performing["course_id"]
        .map(lambda x: courses_df[courses_df["id"] == x]["difficulty"].values[0])
        .unique()
    )
    print(f"High-performing difficulties: {high_difficulties}")

    # Difficulty mapping
    difficulty_level = {"easy": 1, "medium": 2, "hard": 3}

    # Exclude 'hard' from max difficulty calculation
    allowed_difficulties = [diff for diff in high_difficulties if diff != "hard"]
    if not allowed_difficulties:
        max_allowed_difficulty = 1  # Fallback to 'easy' if only 'hard' exists
    else:
        max_allowed_difficulty = max(
            [difficulty_level.get(diff, 1) for diff in allowed_difficulties]
        )
    print(f"Max allowed difficulty level: {max_allowed_difficulty}")

    # Get departments
    departments = (
        student_grades["course_id"]
        .map(lambda x: courses_df[courses_df["id"] == x]["department"].values[0])
        .unique()
    )
    print(f"Student departments: {departments}")

    # Initialize recommendations list
    recommended_courses = []

    # Check departments for electives
    for department in departments:
        print(f"\nChecking department: {department}")
        elective_courses = courses_df[
            (courses_df["type"] == "elective")
            & (courses_df["department"] == department)
        ].copy()
        print(f"Available electives: {len(elective_courses)}")

        if not elective_courses.empty:
            elective_courses["difficulty_level"] = elective_courses["difficulty"].map(
                difficulty_level
            )
            filtered_courses = elective_courses[
                elective_courses["difficulty_level"].le(max_allowed_difficulty)
            ]
            print(f"Eligible courses: {len(filtered_courses)}")

            taken_course_ids = student_grades["course_id"].values
            print(f"Taken course IDs: {taken_course_ids}")
            recommended = filtered_courses[
                ~filtered_courses["id"].isin(taken_course_ids)
            ]

            if not recommended.empty:
                print(f"Found recommendations in {department}")
                recommended_courses.append(recommended)

    # Combine recommendations
    if not recommended_courses:
        print("No recommendations available.")
        return pd.DataFrame(
            [
                {
                    "student_id": student_id,
                    "course_id": None,
                    "course_name": None,
                    "course_code": None,
                    "description": None,
                    "professor_name": None,
                    "department": None,
                    "difficulty": None,
                }
            ]
        )

    recommended_courses = pd.concat(recommended_courses)
    recommended_courses = recommended_courses.drop(columns=["difficulty_level"])

    # Output recommendations
    print(f"\nRecommended elective courses for student:")
    print(
        recommended_courses[
            [
                "id",
                "name",
                "code",
                "description",
                "professor_id",
                "department",
                "difficulty",
            ]
        ]
    )

    # Attendance rate
    first_course_id = student_grades["course_id"].values[0]
    print(
        f"\nAttendance rate for student: {get_attendance_rate(student_id, first_course_id) * 100:.2f}%"
    )

    # Prepare CSV data with additional fields
    csv_data = recommended_courses[
        [
            "id",
            "name",
            "code",
            "description",
            "professor_id",
            "department",
            "difficulty",
        ]
    ].copy()
    csv_data["student_id"] = student_id

    # Get professor names
    professor_names = {}
    for prof_id in csv_data["professor_id"].unique():
        prof = users_df[users_df["id"] == prof_id]
        if not prof.empty:
            professor_names[prof_id] = prof["name"].values[0]

    csv_data["professor_name"] = csv_data["professor_id"].map(professor_names)

    csv_data = csv_data.rename(
        columns={"id": "course_id", "name": "course_name", "code": "course_code"}
    )
    csv_data = csv_data[
        [
            "student_id",
            "course_id",
            "course_name",
            "course_code",
            "description",
            "professor_name",
            "department",
            "difficulty",
        ]
    ]

    return csv_data


# Function to update recommendations JSON for all students
def update_all_recommendations_json():
    # Filter users to only include students
    students_df = users_df[users_df["role"] == "student"]

    # Initialize dictionary to store all recommendations
    all_recommendations = {}

    # Loop through all students
    for _, student in students_df.iterrows():
        student_id = student["id"]
        print(f"\nProcessing recommendations for student {student_id}")

        recommendations = recommend_courses(student_id)
        if not recommendations.empty:
            # Add recommendations for this student with additional fields
            all_recommendations[str(student_id)] = {
                str(row["course_id"]): {
                    "course_name": row["course_name"],
                    "course_code": row["course_code"],
                    "description": row["description"],
                    "professor_name": row["professor_name"],
                    "department": row["department"],
                    "difficulty": row["difficulty"],
                }
                for _, row in recommendations.iterrows()
            }
        else:
            # Add empty recommendations for this student
            all_recommendations[str(student_id)] = {}

    # Save all recommendations to JSON file
    with open("python_scripts/results/recommendations.json", "w") as f:
        json.dump(all_recommendations, f, indent=4)
    print(f"\nUpdated recommendations.json with recommendations for all students")


# Example usage
update_all_recommendations_json()
