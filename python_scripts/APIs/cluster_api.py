from flask import Flask, request, jsonify
from flask_cors import CORS
import pickle
import os
import sys
from http import HTTPStatus

# Add parent directory to sys.path to import StudentPerformanceModel
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../scripts')))
from performance_model import StudentPerformanceModel

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Global variable to store the model
model = None

def load_model():
    """Load the trained model from disk"""
    global model
    model_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/performance_model.pkl'))
    
    try:
        if not os.path.exists(model_path):
            raise FileNotFoundError(f"Model file not found at {model_path}")
        
        model = StudentPerformanceModel.load(model_path)
        app.logger.info("Model loaded successfully")
        return True
    except Exception as e:
        app.logger.error(f"Failed to load model: {str(e)}")
        return False

@app.errorhandler(400)
def bad_request(error):
    """Handle 400 Bad Request errors"""
    return jsonify({"error": str(error.description)}), HTTPStatus.BAD_REQUEST

@app.errorhandler(500)
def internal_error(error):
    """Handle 500 Internal Server errors"""
    return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/cluster", methods=["POST"])
def predict_performance():
    """Predict performance group for one or more students in a specific course"""
    if model is None:
        return jsonify({"error": "Model not loaded"}), HTTPStatus.INTERNAL_SERVER_ERROR

    try:
        # Get JSON data from request
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), HTTPStatus.BAD_REQUEST

        # Validate required fields
        course_id = data.get("course_id")
        student_ids = data.get("student_ids", [])

        if not course_id:
            return jsonify({"error": "Missing course_id"}), HTTPStatus.BAD_REQUEST

        # If a single student_id is provided, convert to list for backward compatibility
        if isinstance(student_ids, (int, str)) or not student_ids:
            student_ids = [data.get("student_id")] if "student_id" in data else []

        if not student_ids:
            return jsonify({"error": "No student_ids provided"}), HTTPStatus.BAD_REQUEST

        # Ensure course_id is integer
        try:
            course_id = int(course_id)
        except (ValueError, TypeError):
            return jsonify({"error": "course_id must be an integer"}), HTTPStatus.BAD_REQUEST

        # Ensure all student_ids are integers
        try:
            student_ids = [int(sid) for sid in student_ids]
        except (ValueError, TypeError):
            return jsonify({"error": "All student_ids must be integers"}), HTTPStatus.BAD_REQUEST

        # Make predictions for all students in one call
        results = model.predict(student_ids, course_id)
        data = results[0]
        high_performers_count = results[1]
        average_performers_count = results[2]
        at_risk_students_count = results[3]

        response = {
            "high_performers_count": high_performers_count,
            "average_performers_count": average_performers_count,
            "at_risk_students_count": at_risk_students_count,
            "data": data,
        }

        return jsonify(response), HTTPStatus.OK

    except Exception as e:
        app.logger.error(f"Error processing request: {str(e)}")
        return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/health", methods=["GET"])
def health_check():
    """Health check endpoint"""
    return jsonify({
        "status": "healthy",
        "model_loaded": model is not None
    }), HTTPStatus.OK

if __name__ == "__main__":
    # Load model before starting the server
    if not load_model():
        print("Failed to load model. Exiting...")
        exit(1)
    
    # Run Flask app
    app.run(host="0.0.0.0", port=5000, debug=False)