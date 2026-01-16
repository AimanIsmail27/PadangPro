# train_model.py (Updated for 2026+)
# - Supports holidays by year (via JSON file or defaults)
# - Normalizes dates so holiday matching is reliable
# - Keeps feature names + order consistent with api.py
# - Trains and saves booking_forecast_model.pkl

import os
import json
import pandas as pd
import joblib

from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error


print("Starting AI model training process (Updated for 2026+)...")

# ---------------------------
# Configuration
# ---------------------------
TRAINING_CSV = os.getenv("TRAINING_CSV", "training_data.csv")
MODEL_OUT = os.getenv("MODEL_OUT", "booking_forecast_model.pkl")

# Keep EXACTLY the same as api.py
FEATURE_COLUMNS = ["day_of_week", "month", "day_of_month", "is_weekend", "is_holiday"]

# Optional holidays json (same format as api.py)
# {
#   "2026": ["2026-01-01", "2026-09-16", ...],
#   "2025": ["2025-01-01", ...]
# }
HOLIDAYS_JSON_PATH = os.getenv("HOLIDAYS_JSON_PATH", "")  # e.g. "malaysia_holidays.json"

# Defaults (you can extend)
DEFAULT_HOLIDAYS_BY_YEAR = {
    "2025": [
        "2025-01-01", "2025-01-29", "2025-03-30", "2025-03-31", "2025-05-01",
        "2025-05-12", "2025-06-02", "2025-06-06", "2025-08-31", "2025-09-16",
        "2025-10-20", "2025-12-25",
    ],
    # Minimal placeholders for 2026 (replace with accurate list or use HOLIDAYS_JSON_PATH)
    "2026": [
        "2026-01-01",  # New Year
        "2026-05-01",  # Labour Day
        "2026-08-31",  # Merdeka
        "2026-09-16",  # Malaysia Day
        "2026-12-25",  # Christmas
    ],
}


def load_holidays_by_year() -> dict:
    """Load holiday dates by year from JSON file if provided; otherwise use defaults."""
    if HOLIDAYS_JSON_PATH and os.path.exists(HOLIDAYS_JSON_PATH):
        try:
            with open(HOLIDAYS_JSON_PATH, "r", encoding="utf-8") as f:
                data = json.load(f)

            cleaned = {}
            for year, dates_list in data.items():
                if isinstance(dates_list, list):
                    cleaned[str(year)] = [str(d) for d in dates_list]
            print(f"Loaded holidays from: {HOLIDAYS_JSON_PATH}")
            return cleaned
        except Exception as e:
            print(f"WARNING: Failed to load holidays JSON '{HOLIDAYS_JSON_PATH}': {e}")
            print("Falling back to DEFAULT_HOLIDAYS_BY_YEAR.")
            return DEFAULT_HOLIDAYS_BY_YEAR

    print("Using DEFAULT_HOLIDAYS_BY_YEAR (no HOLIDAYS_JSON_PATH provided).")
    return DEFAULT_HOLIDAYS_BY_YEAR


def build_holiday_set(holidays_by_year: dict) -> set:
    """
    Convert year->list(ISO date strings) into a single set of normalized pandas Timestamps.
    We keep them normalized (00:00:00) to match normalized booking dates.
    """
    all_dates = []
    for year, iso_list in holidays_by_year.items():
        for d in iso_list:
            try:
                all_dates.append(pd.to_datetime(d))
            except Exception:
                pass
    if not all_dates:
        return set()

    # Normalize to midnight to avoid time mismatch
    holiday_ts = pd.to_datetime(all_dates).normalize()
    return set(holiday_ts)


# ---------------------------
# 1) Load Data
# ---------------------------
try:
    df = pd.read_csv(TRAINING_CSV)
    print(f"Successfully loaded {TRAINING_CSV}")
except FileNotFoundError:
    print(f"Error: {TRAINING_CSV} not found. Please run 'php artisan export:training-data' first.")
    raise SystemExit(1)

if "slot_Date" not in df.columns:
    print("Error: training CSV must contain a 'slot_Date' column.")
    raise SystemExit(1)

print(f"Found {len(df)} paid bookings to learn from.")

# ---------------------------
# 2) Feature Engineering
# ---------------------------
# Parse slot_Date and normalize (strip time) so holiday matching works reliably
df["slot_Date"] = pd.to_datetime(df["slot_Date"], errors="coerce")

# Drop rows with invalid dates
before = len(df)
df = df.dropna(subset=["slot_Date"]).copy()
after = len(df)
if after < before:
    print(f"Dropped {before - after} rows due to invalid slot_Date values.")

# Normalize to date boundary (00:00:00)
df["slot_Date"] = df["slot_Date"].dt.normalize()

# Aggregate bookings per day
daily_bookings = df.groupby("slot_Date").size().reset_index(name="total_bookings")

# Load holidays + build set
holidays_by_year = load_holidays_by_year()
holiday_set = build_holiday_set(holidays_by_year)

# Create features
daily_bookings["day_of_week"] = daily_bookings["slot_Date"].dt.dayofweek
daily_bookings["month"] = daily_bookings["slot_Date"].dt.month
daily_bookings["day_of_month"] = daily_bookings["slot_Date"].dt.day
daily_bookings["is_weekend"] = (daily_bookings["day_of_week"] >= 5).astype(int)
daily_bookings["is_holiday"] = daily_bookings["slot_Date"].isin(holiday_set).astype(int)

print("Feature engineering complete. Features used:", FEATURE_COLUMNS)

# ---------------------------
# 3) Train Model
# ---------------------------
X = daily_bookings[FEATURE_COLUMNS]
y = daily_bookings["total_bookings"]

# Basic guard: if too few days, training/test split can fail
if len(daily_bookings) < 10:
    print("WARNING: Very few days of data. Model training may be unreliable.")
    # Still proceed, but use all data for training
    X_train, X_test, y_train, y_test = X, X, y, y
else:
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

model = RandomForestRegressor(
    n_estimators=200,
    random_state=42,
    min_samples_leaf=1,
)

model.fit(X_train, y_train)
print("Model training complete (RandomForestRegressor).")

# ---------------------------
# 4) Evaluate Accuracy
# ---------------------------
try:
    preds = model.predict(X_test)
    mae = mean_absolute_error(y_test, preds)
    print(f"Model Accuracy (MAE): On average, prediction is off by {mae:.2f} bookings.")
except Exception as e:
    print(f"WARNING: Could not evaluate MAE: {e}")

# ---------------------------
# 5) Save Model
# ---------------------------
joblib.dump(model, MODEL_OUT)
print(f"\nSUCCESS: Model trained and saved as '{MODEL_OUT}'")

# Helpful notes for debugging consistency
print("\nNotes:")
print(f"- Training file: {TRAINING_CSV}")
print(f"- Features: {FEATURE_COLUMNS}")
print(f"- Holidays source: {HOLIDAYS_JSON_PATH if HOLIDAYS_JSON_PATH else 'DEFAULT_HOLIDAYS_BY_YEAR'}")
