# api.py (Updated for 2026+)

from flask import Flask, jsonify, request
import joblib
import pandas as pd
from datetime import date, timedelta, datetime
import os
import json

try:
    from zoneinfo import ZoneInfo  # Python 3.9+
except ImportError:
    ZoneInfo = None  # fallback if not available

app = Flask(__name__)

# ---------------------------
# Configuration
# ---------------------------
MODEL_PATH = os.getenv("MODEL_PATH", "booking_forecast_model.pkl")

# Keep this list EXACTLY the same as what you used in training (names + order).
FEATURE_COLUMNS = ["day_of_week", "month", "day_of_month", "is_weekend", "is_holiday"]

# Timezone (Malaysia)
APP_TZ = os.getenv("APP_TIMEZONE", "Asia/Kuala_Lumpur")

# Optional: supply a custom holiday file (recommended) in JSON format:
# {
#   "2026": ["2026-01-01", "2026-02-..."],
#   "2025": ["2025-01-01", ...]
# }
HOLIDAYS_JSON_PATH = os.getenv("HOLIDAYS_JSON_PATH", "")  # e.g. "malaysia_holidays.json"

# ---------------------------
# Model state
# ---------------------------
model = None
last_modified = 0

# ---------------------------
# Holidays handling
# ---------------------------
DEFAULT_HOLIDAYS_BY_YEAR = {
    # Your original 2025 list (kept, but note: not complete for all states)
    "2025": [
        "2025-01-01", "2025-01-29", "2025-03-30", "2025-03-31", "2025-05-01",
        "2025-05-12", "2025-06-02", "2025-06-06", "2025-08-31", "2025-09-16",
        "2025-10-20", "2025-12-25",
    ],

    # 2026: placeholder minimal set (federal-ish / commonly observed).
    # IMPORTANT: Replace with the accurate list you want to support (or use HOLIDAYS_JSON_PATH).
    # If you leave this minimal, holiday impact will be limited but the API will still work correctly.
    "2026": [
        "2026-01-01",  # New Year
        "2026-05-01",  # Labour Day
        "2026-08-31",  # Merdeka
        "2026-09-16",  # Malaysia Day
        "2026-12-25",  # Christmas
    ],
}

def _load_holidays_by_year():
    """Load holiday dates by year from JSON file if provided; otherwise use defaults."""
    if HOLIDAYS_JSON_PATH and os.path.exists(HOLIDAYS_JSON_PATH):
        try:
            with open(HOLIDAYS_JSON_PATH, "r", encoding="utf-8") as f:
                data = json.load(f)
            # Ensure values are list of ISO strings
            cleaned = {}
            for year, dates_list in data.items():
                if isinstance(dates_list, list):
                    cleaned[str(year)] = [str(d) for d in dates_list]
            return cleaned
        except Exception as e:
            print(f"WARNING: Failed to load holidays JSON '{HOLIDAYS_JSON_PATH}': {e}")
            return DEFAULT_HOLIDAYS_BY_YEAR
    return DEFAULT_HOLIDAYS_BY_YEAR

HOLIDAYS_BY_YEAR = _load_holidays_by_year()

def _holiday_set_for_year(year: int):
    """Return a set of datetime.date objects for the given year."""
    iso_list = HOLIDAYS_BY_YEAR.get(str(year), [])
    out = set()
    for d in iso_list:
        try:
            out.add(date.fromisoformat(d))
        except ValueError:
            # Skip bad entries
            pass
    return out

def _today_in_malaysia() -> date:
    """Get today's date in Malaysia timezone (avoids server timezone mismatch)."""
    if ZoneInfo is None:
        # Fallback: server local date
        return date.today()
    now_my = datetime.now(ZoneInfo(APP_TZ))
    return now_my.date()

# ---------------------------
# Smart Model Loading
# ---------------------------
def load_model_if_updated():
    global model, last_modified
    try:
        current_modified = os.path.getmtime(MODEL_PATH)
        if model is None or current_modified > last_modified:
            model = joblib.load(MODEL_PATH)
            last_modified = current_modified
            print(
                f"✓ Model loaded/reloaded at "
                f"{datetime.fromtimestamp(current_modified).strftime('%Y-%m-%d %H:%M:%S')}"
            )
        return model
    except FileNotFoundError:
        print(f"ERROR: Model file '{MODEL_PATH}' not found.")
        return None
    except Exception as e:
        print(f"ERROR: Failed to load model '{MODEL_PATH}': {e}")
        return None

# ---------------------------
# API Endpoints
# ---------------------------
@app.route("/predict", methods=["GET"])
def predict():
    current_model = load_model_if_updated()
    if not current_model:
        return jsonify({"error": "Model not loaded.", "model_path": MODEL_PATH}), 500

    # Optional query params
    # /predict?days=7
    # /predict?days=14&start=2026-02-01
    days = request.args.get("days", default="7")
    start_str = request.args.get("start", default="")

    try:
        days = int(days)
        if days < 1 or days > 60:
            return jsonify({"error": "Parameter 'days' must be between 1 and 60."}), 400
    except ValueError:
        return jsonify({"error": "Parameter 'days' must be an integer."}), 400

    if start_str:
        try:
            start_date = date.fromisoformat(start_str)
        except ValueError:
            return jsonify({"error": "Parameter 'start' must be ISO date YYYY-MM-DD."}), 400
    else:
        start_date = _today_in_malaysia()

    holiday_set = _holiday_set_for_year(start_date.year)

    print(f"[{datetime.now().strftime('%H:%M:%S')}] Forecast request: start={start_date}, days={days}")

    # 1) Prepare future feature rows
    future_rows = []
    for i in range(days):
        d = start_date + timedelta(days=i)
        # if forecast crosses year boundary, refresh holiday set
        if i == 0 or d.year != (start_date.year):
            holiday_set = _holiday_set_for_year(d.year)

        future_rows.append({
            "date_iso": d.isoformat(),
            "label": d.strftime("%a, %b %d"),
            "day_of_week": d.weekday(),
            "month": d.month,
            "day_of_month": d.day,
            "is_weekend": 1 if d.weekday() >= 5 else 0,
            "is_holiday": 1 if d in holiday_set else 0,
        })

    future_df = pd.DataFrame(future_rows)

    # 2) Select model features in a fixed order
    try:
        X_future = future_df[FEATURE_COLUMNS]
    except KeyError as e:
        return jsonify({
            "error": "Feature columns mismatch. Update FEATURE_COLUMNS to match training.",
            "missing_or_wrong": str(e),
            "expected": FEATURE_COLUMNS,
            "available": list(future_df.columns),
        }), 500

    # 3) Predict
    try:
        preds = current_model.predict(X_future)
        preds = [max(0, int(round(float(p)))) for p in preds]
    except Exception as e:
        return jsonify({"error": f"Prediction failed: {e}"}), 500

    # 4) Response format (dashboard-friendly)
    response = {
        "start_date": start_date.isoformat(),
        "days": days,
        "labels": future_df["label"].tolist(),
        "dates": future_df["date_iso"].tolist(),
        "data": preds,
        "features_used": FEATURE_COLUMNS,
    }
    return jsonify(response), 200

@app.route("/health", methods=["GET"])
def health_check():
    # Basic health + model status (useful for deployment checks)
    exists = os.path.exists(MODEL_PATH)
    loaded = model is not None
    return jsonify({
        "status": "ok",
        "time": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        "timezone": APP_TZ,
        "model_path": MODEL_PATH,
        "model_file_exists": exists,
        "model_loaded": loaded,
        "model_last_modified_epoch": last_modified if loaded else None,
    }), 200

# ---------------------------
# Run the Server
# ---------------------------
if __name__ == "__main__":
    print("=" * 60)
    print("🚀 Starting Football Booking Forecast API (Updated for 2026+)")
    print("=" * 60)
    app.run(host="0.0.0.0", port=5000, debug=True)
