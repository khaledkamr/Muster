from flask import Flask, request, jsonify
from flask_cors import CORS
import pickle
import os
import sys
from http import HTTPStatus
import numpy as np
from tensorflow.keras.models import load_model

# Add parent directory to sys.path to import models
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../scripts')))
from performance_model import StudentPerformanceModel
from gpa_model import predict_student_gpa

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Global variables to store the models and scalers
performance_model = None
gpa_model = None
scaler_X = None
scaler_y = None

def load_models():
    """Load both the performance and GPA prediction models from disk"""
    global performance_model, gpa_model, scaler_X, scaler_y
    
    # Load performance model
    performance_model_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/performance_model.pkl'))
    try:
        if not os.path.exists(performance_model_path):
            raise FileNotFoundError(f"Performance model file not found at {performance_model_path}")
        performance_model = StudentPerformanceModel.load(performance_model_path)
        app.logger.info("Performance model loaded successfully")
    except Exception as e:
        app.logger.error(f"Failed to load performance model: {str(e)}")
        performance_model = None

    # Load GPA model and scalers
    gpa_model_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/lstm_cgpa_model.keras'))
    scaler_X_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/scaler_X.npy'))
    scaler_y_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/scaler_y.npy'))
    
    try:
        if not os.path.exists(gpa_model_path):
            raise FileNotFoundError(f"GPA model file not found at {gpa_model_path}")
        if not os.path.exists(scaler_X_path):
            raise FileNotFoundError(f"Scaler X file not found at {scaler_X_path}")
        if not os.path.exists(scaler_y_path):
            raise FileNotFoundError(f"Scaler y file not found at {scaler_y_path}")
        
        gpa_model = load_model(gpa_model_path)
        scaler_X = np.load(scaler_X_path, allow_pickle=True).item()
        scaler_y = np.load(scaler_y_path, allow_pickle=True).item()
        app.logger.info("GPA model and scalers loaded successfully")
    except Exception as e:
        app.logger.error(f"Failed to load GPA model or scalers: {str(e)}")
        gpa_model = None
        scaler_X = None
        scaler_y = None

    return performance_model is not None and gpa_model is not None

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
    if performance_model is None:
        return jsonify({"error": "Performance model not loaded"}), HTTPStatus.INTERNAL_SERVER_ERROR

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
        results = performance_model.predict(student_ids, course_id)
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
        app.logger.error(f"Error processing performance prediction request: {str(e)}")
        return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/gpa", methods=["POST"])
def predict_gpa():
    """Predict GPA and CGPA for a single student"""
    if gpa_model is None or scaler_X is None or scaler_y is None:
        return jsonify({"error": "GPA model or scalers not loaded"}), HTTPStatus.INTERNAL_SERVER_ERROR

    try:
        # Get JSON data from request
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), HTTPStatus.BAD_REQUEST

        # Validate required field
        student_id = data.get("student_id")
        if not student_id:
            return jsonify({"error": "Missing student_id"}), HTTPStatus.BAD_REQUEST

        # Ensure student_id is integer
        try:
            student_id = int(student_id)
        except (ValueError, TypeError):
            return jsonify({"error": "student_id must be an integer"}), HTTPStatus.BAD_REQUEST

        # Make GPA prediction
        result = predict_student_gpa(student_id, gpa_model, scaler_X, scaler_y)
        return jsonify(result), HTTPStatus.OK

    except Exception as e:
        app.logger.error(f"Error processing GPA prediction request: {str(e)}")
        return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/health", methods=["GET"])
def health_check():
    """Health check endpoint"""
    return jsonify({
        "status": "healthy",
        "performance_model_loaded": performance_model is not None,
        "gpa_model_loaded": gpa_model is not None and scaler_X is not None and scaler_y is not None
    }), HTTPStatus.OK

if __name__ == "__main__":
    # Load models before starting the server
    if not load_models():
        print("Failed to load one or both models. Exiting...")
        exit(1)
    
    # Run Flask app
    app.run(host="0.0.0.0", port=5000, debug=False)