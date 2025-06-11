import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.optimizers import Adam
import json
import os

# Load the dataset
data_dir = "python_scripts/db/"
grades_df = pd.read_csv(os.path.join(data_dir, "grades.csv"))
attendances_df = pd.read_csv(os.path.join(data_dir, "attendances.csv"))
courses_df = pd.read_csv(os.path.join(data_dir, "courses.csv"))
users_df = pd.read_csv(os.path.join(data_dir, "users.csv"))
enrollments_df = pd.read_csv(os.path.join(data_dir, "enrollments.csv"))

# Define features and target
features_complete = ['quiz1', 'quiz2', 'midterm', 'assignments', 'project', 'final']
features_partial = ['quiz1', 'midterm', 'assignments']
target = 'total'

# Function to convert total score to GPA
def total_to_gpa(total):
    if total > 161.5:
        return 4.00
    elif total > 153:
        return 4.00
    elif total > 144.5:
        return 3.70
    elif total > 136:
        return 3.30
    elif total > 127.5:
        return 3.00
    elif total > 122.4:
        return 2.70
    elif total > 119:
        return 2.30
    elif total > 110.5:
        return 2.00
    elif total > 107.1:
        return 1.70
    elif total > 102:
        return 1.30
    elif total > 93.5:
        return 1.00
    elif total > 85:
        return 0.70
    else:
        return 0.00

# Calculate CGPA for completed courses
def calculate_cgpa(student_id, student_grades):
    student_data = student_grades[(student_grades['student_id'] == student_id) & student_grades[features_complete + [target]].notnull().all(axis=1)]
    if student_data.empty:
        return None
    total_gpa = student_data[target].apply(total_to_gpa).mean()
    return total_gpa

# Prepare training data
train_data = grades_df[grades_df[features_complete + [target]].notnull().all(axis=1)].copy()
train_data['cgpa'] = train_data.groupby('student_id')[target].transform(lambda x: x.apply(total_to_gpa).mean())

# Features for training: partial grades + historical CGPA
X = train_data[features_partial + ['cgpa']].values
y = train_data[target].values

# Normalize features and target
scaler_X = MinMaxScaler()
scaler_y = MinMaxScaler()
X_scaled = scaler_X.fit_transform(X)
y_scaled = scaler_y.fit_transform(y.reshape(-1, 1))

# Reshape for LSTM: [samples, time_steps, features]
X_reshaped = X_scaled.reshape((X_scaled.shape[0], 1, X_scaled.shape[1]))

# Split data
X_train, X_test, y_train, y_test = train_test_split(X_reshaped, y_scaled, test_size=0.2, random_state=42)

# Build LSTM model
model = Sequential([
    LSTM(64, input_shape=(X_train.shape[1], X_train.shape[2]), return_sequences=True),
    Dropout(0.2),
    LSTM(32),
    Dropout(0.2),
    Dense(16, activation='relu'),
    Dense(1, activation='linear')
])

# Compile model
model.compile(optimizer=Adam(learning_rate=0.001), loss='mse')

# Train model
model.fit(X_train, y_train, epochs=50, batch_size=32, validation_split=0.2, verbose=0)
model.save('lstm_cgpa_model.h5')

# Function to predict CGPA for a student
def predict_cgpa(student_id, student_grades):
    historical_cgpa = calculate_cgpa(student_id, student_grades)
    if historical_cgpa is None:
        historical_cgpa = 0.0

    partial_data = student_grades[
        (student_grades['student_id'] == student_id) &
        student_grades[features_partial].notnull().all(axis=1) &
        student_grades[['quiz2', 'project', 'final', target]].isnull().any(axis=1)
    ]
    
    if partial_data.empty:
        return {
            'historical_cgpa': historical_cgpa,
            'predicted_semester_gpa': None,
            'predicted_new_cgpa': historical_cgpa
        }

    predicted_gpas = []
    for _, course in partial_data.iterrows():
        input_data = np.append(course[features_partial], historical_cgpa)
        input_scaled = scaler_X.transform([input_data])
        input_reshaped = input_scaled.reshape((1, 1, len(features_partial) + 1))
        
        pred_scaled = model.predict(input_reshaped, verbose=0)
        pred_total = scaler_y.inverse_transform(pred_scaled)[0][0]
        pred_gpa = total_to_gpa(pred_total)
        predicted_gpas.append(pred_gpa)
    
    semester_gpa = np.mean(predicted_gpas) if predicted_gpas else 0.0
    completed_courses = student_grades[(student_grades['student_id'] == student_id) & student_grades[features_complete + [target]].notnull().all(axis=1)]
    n_completed = len(completed_courses)
    n_uncompleted = len(partial_data)
    
    if n_completed == 0:
        new_cgpa = semester_gpa
    else:
        new_cgpa = (historical_cgpa * n_completed + semester_gpa * n_uncompleted) / (n_completed + n_uncompleted)
    
    return {
        'historical_cgpa': historical_cgpa,
        'predicted_semester_gpa': semester_gpa,
        'predicted_new_cgpa': new_cgpa
    }

# Get all student IDs
student_ids = users_df[users_df['role'] == 'student']['id'].unique()

# Predict GPA for all students
results = {}
for student_id in student_ids:
    results[str(student_id)] = predict_cgpa(student_id, grades_df)

# Save results to JSON
output_dir = "python_scripts/results/"
if not os.path.exists(output_dir):
    os.makedirs(output_dir)
with open(os.path.join(output_dir, "gpa_predictions.json"), "w") as f:
    json.dump(results, f, indent=4)