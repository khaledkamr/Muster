import mysql.connector
from datetime import datetime, timedelta
import random
from typing import List, Dict, Any


class AttendanceSeeder:
    def __init__(self):
        # Database connection configuration
        self.db_config = {
            "host": "localhost",
            "user": "root",
            "password": "",
            "database": "musterdb",
        }
        self.conn = None
        self.cursor = None

    def connect(self):
        """Establish database connection"""
        try:
            self.conn = mysql.connector.connect(**self.db_config)
            self.cursor = self.conn.cursor(dictionary=True)
            print("Successfully connected to the database")
        except mysql.connector.Error as err:
            print(f"Error connecting to database: {err}")
            raise

    def close(self):
        """Close database connection"""
        if self.cursor:
            self.cursor.close()
        if self.conn:
            self.conn.close()
        print("Database connection closed")

    def get_students(self) -> List[Dict[str, Any]]:
        """Get all students from the database"""
        query = "SELECT * FROM users WHERE role = 'student'"
        self.cursor.execute(query)
        return self.cursor.fetchall()

    def get_enrollments(self, student_id: int) -> List[Dict[str, Any]]:
        """Get all enrollments for a student with course information"""
        query = """
            SELECT e.*, c.* 
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.student_id = %s
        """
        self.cursor.execute(query, (student_id,))
        return self.cursor.fetchall()

    def create_attendance(
        self, student_id: int, course_id: int, date: str, type: str, status: str
    ):
        """Create or update attendance record"""
        query = """
            INSERT INTO attendances (student_id, course_id, date, type, status)
            VALUES (%s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE status = %s
        """
        values = (student_id, course_id, date, type, status, status)
        self.cursor.execute(query, values)
        self.conn.commit()

    def run(self):
        """Main method to seed attendance data"""
        try:
            self.connect()
            students = self.get_students()

            for student in students:
                enrollments = self.get_enrollments(student["id"])

                for enrollment in enrollments:
                    course = enrollment
                    enrollment_year = enrollment["enrolled_at"].year
                    semester_start = (
                        f"{enrollment_year}-01-01"
                        if course["semester"] == "first"
                        else f"{enrollment_year}-08-01"
                    )

                    start_date = datetime.strptime(semester_start, "%Y-%m-%d")
                    start_date += timedelta(days=random.randint(0, 5))

                    for attendance_type in ["lecture", "lab"]:
                        current_date = start_date
                        for _ in range(16):
                            random_num = random.randint(1, 100)
                            status = (
                                "present"
                                if random_num <= 70
                                else ("late" if random_num <= 75 else "absent")
                            )

                            self.create_attendance(
                                student_id=student["id"],
                                course_id=course["id"],
                                date=current_date.strftime("%Y-%m-%d"),
                                type=attendance_type,
                                status=status,
                            )

                            current_date += timedelta(days=7)

            print("Attendance seeding completed successfully!")

        except Exception as e:
            print(f"An error occurred: {e}")
        finally:
            self.close()


if __name__ == "__main__":
    seeder = AttendanceSeeder()
    seeder.run()
