import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import train_test_split
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import EarlyStopping
from tensorflow.keras import saving
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score
import os
import json
import time

data_dir = "python_scripts/db/"
grades_df = pd.read_csv(os.path.join(data_dir, "grades.csv"))
attendances_df = pd.read_csv(os.path.join(data_dir, "attendances.csv"))
courses_df = pd.read_csv(os.path.join(data_dir, "courses.csv"))
users_df = pd.read_csv(os.path.join(data_dir, "users.csv"))
enrollments_df = pd.read_csv(os.path.join(data_dir, "enrollments.csv"))

features_complete = ['quiz1', 'quiz2', 'midterm', 'assignments', 'project', 'final']
features_partial = ['quiz1', 'midterm', 'assignments']
target = 'total'

@tf.keras.saving.register_keras_serializable()
def gpa_accuracy(y_true, y_pred):
    accuracy = tf.reduce_mean(tf.cast(tf.abs(y_true - y_pred) <= 0.1, tf.float32))
    return accuracy

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

def calculate_cgpa(student_id, student_grades):
    student_data = student_grades[
        (student_grades['student_id'] == student_id) & 
        student_grades[features_complete + [target]].notnull().all(axis=1)
    ]
    if student_data.empty:
        return 0.0
    total_gpa = student_data[target].apply(total_to_gpa).mean()
    return total_gpa

def prepare_training_data():
    train_data = grades_df[grades_df[features_complete + [target]].notnull().all(axis=1)].copy()
    train_data['cgpa'] = train_data.groupby('student_id')[target].transform(lambda x: x.apply(total_to_gpa).mean())
    
    X = train_data[features_partial + ['cgpa']].values
    y = train_data[target].values
    
    scaler_X = MinMaxScaler()
    scaler_y = MinMaxScaler()
    X_scaled = scaler_X.fit_transform(X)
    y_scaled = scaler_y.fit_transform(y.reshape(-1, 1))
    
    X_reshaped = X_scaled.reshape((X_scaled.shape[0], 1, X_scaled.shape[1]))
    X_train, X_test, y_train, y_test = train_test_split(X_reshaped, y_scaled, test_size=0.2, random_state=42)
    
    return X_train, X_test, y_train, y_test, scaler_X, scaler_y

def build_and_train_model(X_train, y_train, X_test,y_test):
    model = Sequential([
        LSTM(64, input_shape=(X_train.shape[1], X_train.shape[2]), return_sequences=True),
        Dropout(0.2),
        LSTM(32),
        Dropout(0.2),
        Dense(16, activation='relu'),
        Dense(1)
    ])
    
    model.compile(
        Adam(learning_rate=0.001), 
        loss='mse',
        metrics=['mae']
    )

    early_stopping = EarlyStopping(
        monitor='val_loss',
        patience=10,
        restore_best_weights=True
    )

    start_time = time.time()
    history = model.fit(
        X_train, y_train, 
        epochs=10, 
        batch_size=32, 
        validation_split=0.2, 
        verbose=1
    )
    end_time = time.time()
    execution_time = end_time - start_time

    y_pred = model.predict(X_test)

    mse = mean_squared_error(y_test, y_pred)
    rmse = np.sqrt(mse)
    mae = mean_absolute_error(y_test, y_pred)
    r2 = r2_score(y_test, y_pred)

    print(f"Test Mean Squared Error: {mse:.4f}")
    print(f"Test Root Mean Squared Error: {rmse:.4f}")
    print(f"Test Mean Absolute Error: {mae:.4f}")
    print(f"Test R² Score: {r2:.4f}")

    results_dir = 'python_scripts/results'
    os.makedirs(results_dir, exist_ok=True)
    # metrics = {
    #     'accuracy': float(history.history['gpa_accuracy'][-1]),
    #     'loss': float(history.history['loss'][-1] * 100),
    #     'epochs': len(history.epoch),
    #     'execution_time': execution_time,
    #     'accuracy_over_epochs': [float(l) for l in history.history['gpa_accuracy']],
    #     'loss_over_epochs': [float(l * 100) for l in history.history['loss']],
    # }

    with open(os.path.join(results_dir, 'lstm_training_metrics.json'), 'w') as f:
        json.dump(history.history, f, indent=4)

    return model

def predict_student_gpa(student_id, model, scaler_X, scaler_y):
    if student_id not in users_df[users_df['role'] == 'student']['id'].values:
        return {
            'student_id': student_id,
            'error': 'Student ID not found or not a student'
        }
    
    historical_cgpa = calculate_cgpa(student_id, grades_df)
    
    partial_data = grades_df[
        (grades_df['student_id'] == student_id) &
        grades_df[features_partial].notnull().all(axis=1) &
        grades_df[['quiz2', 'project', 'final', target]].isnull().any(axis=1)
    ]
    
    if partial_data.empty:
        return {
            'student_id': student_id,
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
    completed_courses = grades_df[
        (grades_df['student_id'] == student_id) & 
        grades_df[features_complete + [target]].notnull().all(axis=1)
    ]
    n_completed = len(completed_courses)
    n_uncompleted = len(partial_data)
    
    if n_completed == 0:
        new_cgpa = semester_gpa
    else:
        new_cgpa = (historical_cgpa * n_completed + semester_gpa * n_uncompleted) / (n_completed + n_uncompleted)
    
    return {
        'student_id': student_id,
        'historical_cgpa': round(historical_cgpa, 2),
        'predicted_semester_gpa': round(semester_gpa, 2) if semester_gpa else None,
        'predicted_new_cgpa': round(new_cgpa, 2)
    }

if __name__ == "__main__":
    X_train, X_test, y_train, y_test, scaler_X, scaler_y = prepare_training_data()
    model = build_and_train_model(X_train, y_train, X_test, y_test)
    
    model.save('python_scripts/models/lstm_cgpa_model.keras')
    np.save('python_scripts/models/scaler_X.npy', scaler_X)
    np.save('python_scripts/models/scaler_y.npy', scaler_y)

    example_student_id = 2
    result = predict_student_gpa(example_student_id, model, scaler_X, scaler_y)
    print(result)