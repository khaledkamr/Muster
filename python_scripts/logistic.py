# Import libraries
import pandas as pd
import numpy as np
import os
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.ensemble import RandomForestClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    roc_curve,
    auc,
    confusion_matrix,
    ConfusionMatrixDisplay,
)
import matplotlib.pyplot as plt
import seaborn as sns
import plotly.express as px
import ipywidgets as widgets
from IPython.display import display, HTML
from imblearn.over_sampling import SMOTE
from joblib import dump, load
import json

# Set plotting style
plt.style.use("ggplot")
sns.set_palette("Blues")

# Print working directory and files
print("Current working directory:", os.getcwd())
print("Files in directory:", os.listdir("."))

# Data Preprocessing and Feature Engineering
# Load data
grades = pd.read_csv("python_scripts/db/grades.csv")
attendances = pd.read_csv("python_scripts/db/attendances.csv", low_memory=False)
courses = pd.read_csv("python_scripts/db/courses.csv")

# Print initial diagnostics
print("Initial grades.csv shape:", grades.shape)
print(
    "Unique student_id-course_id pairs in grades.csv:",
    grades[["student_id", "course_id"]].drop_duplicates().shape[0],
)
print("Target distribution in grades.csv:")
print(grades["status"].value_counts(normalize=True))

# Calculate attendance statistics only for available data
present_count = (
    attendances[attendances["status"].isin(["present", "late"])]
    .groupby(["student_id", "course_id"])
    .size()
    .reset_index(name="present_count")
)
total_sessions = (
    attendances.groupby(["student_id", "course_id"])
    .size()
    .reset_index(name="total_sessions")
)

# Merge with grades, retaining all grades data
data = grades.merge(
    present_count, on=["student_id", "course_id"], how="left", suffixes=("", "_attend")
)
data = data.merge(
    total_sessions, on=["student_id", "course_id"], how="left", suffixes=("", "_total")
)
data = data.merge(
    courses[["id", "difficulty", "department"]],
    left_on="course_id",
    right_on="id",
    how="left",
)

# Handle missing attendance values
data["present_count"] = data["present_count"].fillna(0)
data["total_sessions"] = data["total_sessions"].fillna(
    0
)  # No sessions for unmatched pairs
data["attendance_percentage"] = np.where(
    data["total_sessions"] > 0,
    (data["present_count"] / data["total_sessions"]) * 100,
    0,
)
data["attendance_percentage"] = data["attendance_percentage"].clip(0, 100)

# Map difficulty and department, handling NaN
data["difficulty"] = (
    data["difficulty"].map({"easy": 0, "medium": 1, "hard": 2}).fillna(1)
)
data["department"] = (
    data["department"].astype("category").cat.codes.fillna(-1)
)  # Use -1 for missing departments

# Impute missing scores with medians, checking for NaN
score_columns = ["quiz1", "quiz2", "midterm", "assignments", "final"]
for col in score_columns:
    print(f"NaN count in {col} before imputation:", data[col].isna().sum())
    data[col] = pd.to_numeric(data[col], errors="coerce").fillna(data[col].median())

# Calculate weighted total score
max_scores = {"quiz1": 10, "quiz2": 10, "midterm": 30, "assignments": 30, "final": 60}
weights = {
    "quiz1": 0.05,
    "quiz2": 0.05,
    "midterm": 0.20,
    "assignments": 0.10,
    "final": 0.40,
}
data["total_score"] = (
    data["quiz1"] / max_scores["quiz1"] * weights["quiz1"]
    + data["quiz2"] / max_scores["quiz2"] * weights["quiz2"]
    + data["midterm"] / max_scores["midterm"] * weights["midterm"]
    + data["assignments"] / max_scores["assignments"] * weights["assignments"]
    + data["final"] / max_scores["final"] * weights["final"]
) * 100

include_nan_status = False
if include_nan_status:
    data["status"] = data["status"].fillna("pending")
    valid_statuses = ["pass", "fail", "pending"]
else:
    valid_statuses = ["pass", "fail"]

initial_rows = data.shape[0]
data = data[data["status"].isin(valid_statuses)]
filtered_rows = data.shape[0]
print(f"Rows dropped due to status filtering: {initial_rows - filtered_rows}")

print("Data shape:", data.shape)
print("Target distribution after merging:")
print(data["status"].value_counts(normalize=True))
print("Attendance percentage summary:")
print(data["attendance_percentage"].describe())
print("Weighted total score summary:")
print(data["total_score"].describe())

# Feature selection
feature_names = [
    "attendance_percentage",
    "assignments",
    "midterm",
    "quiz1",
    "quiz2",
    "total_score",
    "difficulty",
    "department",
]
X = data[feature_names]
y = data["status"].map(
    {"pass": 1, "fail": 0, "pending": 2}
    if include_nan_status
    else {"pass": 1, "fail": 0}
)

# Model Training and Evaluation
if X is not None and y is not None:
    print("Target distribution in data:")
    print(y.value_counts(normalize=True))

    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    # Train-test split with stratification
    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled, y, test_size=0.2, random_state=42, stratify=y
    )
    print("Training set target distribution:")
    print(pd.Series(y_train).value_counts(normalize=True))

    # Apply SMOTE
    smote = SMOTE(sampling_strategy=0.5, random_state=42)
    X_train_balanced, y_train_balanced = smote.fit_resample(X_train, y_train)
    print(
        "Balanced training set distribution:",
        pd.Series(y_train_balanced).value_counts(normalize=True),
    )

    # Train Logistic Regression
    param_grid_lr = {
        "C": [0.01, 0.1, 1],
        "solver": ["lbfgs"],
        "class_weight": ["balanced"],
    }
    grid_lr = GridSearchCV(
        LogisticRegression(max_iter=5000),
        param_grid_lr,
        cv=5,
        scoring="f1_macro",
        n_jobs=-1,
    )
    grid_lr.fit(X_train_balanced, y_train_balanced)
    model = grid_lr.best_estimator_
    print("Logistic Regression - Best parameters:", grid_lr.best_params_)
    print("Logistic Regression - Best cross-validation F1-score:", grid_lr.best_score_)
    y_prob_lr = model.predict_proba(X_test)[:, 1]
    y_pred_lr = (y_prob_lr >= 0.5).astype(int)
    print(
        "\nLogistic Regression - Test Accuracy (threshold=0.5):",
        accuracy_score(y_test, y_pred_lr),
    )
    print("Logistic Regression - Classification Report (threshold=0.5):")
    print(classification_report(y_test, y_pred_lr, target_names=["Fail", "Pass"]))

    dump(model, "model.joblib")
    dump(scaler, "scaler.joblib")
    print("Model and scaler saved as model.joblib and scaler.joblib.")
else:
    print(
        "Error: X or y is not defined. Ensure data preprocessing runs successfully first."
    )

# To see what to search for
valid_pairs = (
    data[["student_id", "course_id"]]
    .drop_duplicates()
    .sort_values(["student_id", "course_id"])
)
print(f"Total unique pairs: {len(valid_pairs)}")

html = valid_pairs.to_html(classes="compact-table", index=False)
html = f"""
<style>
.compact-table {{
    font-size: 10px;
    line-height: 1;
    max-height: 400px;
    overflow-y: auto;
    display: block;
}}
.compact-table th, .compact-table td {{
    padding: 2px;
    border: 1px solid #ddd;
}}
</style>
{html}
"""
display(HTML(html))

# Interactive Prediction Interface
pd.set_option("display.max_columns", None)
pd.set_option("future.no_silent_downcasting", True)


def search_data(student_id, course_id):
    if data is None or data.empty:
        print("Error: Data is not available. Ensure data preprocessing is run first.")
        return pd.DataFrame()
    result = data[
        (data["student_id"] == student_id) & (data["course_id"] == course_id)
    ].copy()
    columns = [
        "student_id",
        "course_id",
        "attendance_percentage",
        "assignments",
        "midterm",
        "quiz1",
        "quiz2",
        "status",
        "total_score",
        "difficulty",
        "department",
    ]
    if not result.empty:
        for col in columns:
            if col not in result.columns:
                result[col] = pd.NA
        result = result[columns].infer_objects(copy=False)
    else:
        print(
            f"No matching records found for student_id={student_id}, course_id={course_id}"
        )
        result = pd.DataFrame(
            [[student_id, course_id, pd.NA] + [pd.NA] * (len(columns) - 2)],
            columns=columns,
        )
    return result


feature_names = [
    "attendance_percentage",
    "assignments",
    "midterm",
    "quiz1",
    "quiz2",
    "total_score",
    "difficulty",
    "department",
]
try:
    model = load("model.joblib")
    scaler = load("scaler.joblib")
except FileNotFoundError:
    print(
        "Error: model.joblib or scaler.joblib not found. Run model training to save them first."
    )
    model = None
    scaler = None


def predict_with_model(result):
    if model is not None and scaler is not None:
        X_search = pd.DataFrame(
            index=result.index, columns=feature_names, dtype="float64"
        )
        for col in feature_names:
            X_search[col] = pd.to_numeric(result[col], errors="coerce").fillna(
                data[feature_names].median()
            )
        X_search_scaled = scaler.transform(X_search)
        result["Predicted_Probability"] = model.predict_proba(X_search_scaled)[:, 1]
        result["Predicted_Status"] = result["Predicted_Probability"].apply(
            lambda x: "Pass" if x >= 0.5 else "Fail"
        )
    return result


student_id_input = widgets.IntText(description="Student ID:", value=2)
course_id_input = widgets.IntIntText(description="Course ID:", value=1)
button = widgets.Button(description="Search")
output = widgets.Output(layout={"width": "100%", "height": "auto"})


def on_button_clicked(b):
    with output:
        output.clear_output()
        student_id = student_id_input.value
        course_id = course_id_input.value
        result = search_data(student_id, course_id)
        if not result.empty:
            result = predict_with_model(result)
            result.to_csv("predictions.csv", index=False)
            json_data = result.to_json(orient="records", lines=True)
            with open("predictions.json", "w") as json_file:
                json_file.write(json_data)
            display(
                result.style.set_properties(**{"text-align": "left"}).set_table_styles(
                    [{"selector": "th", "props": [("text-align", "left")]}]
                )
            )


button.on_click(on_button_clicked)
display(student_id_input, course_id_input, button, output)
