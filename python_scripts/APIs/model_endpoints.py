from flask import Flask, request, jsonify
from flask_cors import CORS
import pickle
import os
import sys
from http import HTTPStatus
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
import time

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../scripts')))
from performance_model import StudentPerformanceModel
from gpa_model import predict_student_gpa, build_and_train_model, prepare_training_data
# from course_recommendation import recommend_courses
import course_recommendation

app = Flask(__name__)
CORS(app)

performance_model = None
gpa_model = None
scaler_X = None
scaler_y = None

def load_models():
    global performance_model, gpa_model, scaler_X, scaler_y
    
    performance_model_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/performance_model.pkl'))
    try:
        if not os.path.exists(performance_model_path):
            raise FileNotFoundError(f"Performance model file not found at {performance_model_path}")
        performance_model = StudentPerformanceModel.load(performance_model_path)
        app.logger.info("Performance model loaded successfully")
    except Exception as e:
        app.logger.error(f"Failed to load performance model: {str(e)}")
        performance_model = None

    gpa_model_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/lstm_cgpa_model.keras'))
    scaler_X_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/scaler_X.npy'))
    scaler_y_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '../models/scaler_y.npy'))
    
    try:
        if not os.path.exists(gpa_model_path):
            raise FileNotFoundError(f"GPA model file not found at {gpa_model_path}")

        gpa_model = load_model(gpa_model_path, custom_objects={'gpa_accuracy': gpa_accuracy})

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
    if performance_model is None:
        return jsonify({"error": "Performance model not loaded"}), HTTPStatus.INTERNAL_SERVER_ERROR

    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), HTTPStatus.BAD_REQUEST

        course_id = data.get("course_id")
        student_ids = data.get("student_ids", [])

        if not course_id:
            return jsonify({"error": "Missing course_id"}), HTTPStatus.BAD_REQUEST

        if isinstance(student_ids, (int, str)) or not student_ids:
            student_ids = [data.get("student_id")] if "student_id" in data else []

        if not student_ids:
            return jsonify({"error": "No student_ids provided"}), HTTPStatus.BAD_REQUEST

        try:
            course_id = int(course_id)
        except (ValueError, TypeError):
            return jsonify({"error": "course_id must be an integer"}), HTTPStatus.BAD_REQUEST

        try:
            student_ids = [int(sid) for sid in student_ids]
        except (ValueError, TypeError):
            return jsonify({"error": "All student_ids must be integers"}), HTTPStatus.BAD_REQUEST

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
    if gpa_model is None or scaler_X is None or scaler_y is None:
        return jsonify({"error": "GPA model or scalers not loaded"}), HTTPStatus.INTERNAL_SERVER_ERROR

    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), HTTPStatus.BAD_REQUEST

        student_id = data.get("student_id")
        if not student_id:
            return jsonify({"error": "Missing student_id"}), HTTPStatus.BAD_REQUEST

        try:
            student_id = int(student_id)
        except (ValueError, TypeError):
            return jsonify({"error": "student_id must be an integer"}), HTTPStatus.BAD_REQUEST

        result = predict_student_gpa(student_id, gpa_model, scaler_X, scaler_y)
        return jsonify(result), HTTPStatus.OK

    except Exception as e:
        app.logger.error(f"Error processing GPA prediction request: {str(e)}")
        return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/gpa_retrain", methods=["POST"])
def retrain():
    try:
        X_train, _, y_train, _, _, _ = prepare_training_data()
        start_time = time.time()
        build_and_train_model(X_train, y_train)
        end_time = time.time()
        execution_time = end_time - start_time
        return jsonify({"message": "model retrained successfully", "time": f"{execution_time}"}), HTTPStatus.OK
    
    except Exception as e:
        return jsonify({"error": "Internal server error"}), HTTPStatus.INTERNAL_SERVER_ERROR

@app.route("/recommend_courses", methods=["POST"])
def recommend_courses_endpoint():
    """Recommend elective courses for a single student"""
    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), HTTPStatus.BAD_REQUEST

        student_id = data.get("student_id")
        if not student_id:
            return jsonify({"error": "Missing student_id"}), HTTPStatus.BAD_REQUEST

        try:
            student_id = int(student_id)
        except (ValueError, TypeError):
            return jsonify({"error": "student_id must be an integer"}), HTTPStatus.BAD_REQUEST

        result = recommend_courses(student_id)
        return jsonify(result), HTTPStatus.OK

    except Exception as e:
        app.logger.error(f"Error processing course recommendation request: {str(e)}")
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
    if not load_models():
        print("Failed to load one or both models. Exiting...")
        exit(1)
    
    app.run(host="0.0.0.0", port=5000, debug=False)