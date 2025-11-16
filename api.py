# api.py (Upgraded Version)

from flask import Flask, jsonify
import joblib
import pandas as pd
from datetime import date, timedelta, datetime
import os

app = Flask(__name__)

# --- Model Configuration ---
MODEL_PATH = 'booking_forecast_model.pkl'
model = None
last_modified = 0

# Define Malaysian Public Holidays for 2025
public_holidays = [
    '2025-01-01', '2025-01-29', '2025-03-30', '2025-03-31', '2025-05-01',
    '2025-05-12', '2025-06-02', '2025-06-06', '2025-08-31', '2025-09-16',
    '2025-10-20', '2025-12-25',
]
holidays_date = [date.fromisoformat(d) for d in public_holidays]

# --- Smart Model Loading Function (Your excellent code, unchanged) ---
def load_model_if_updated():
    global model, last_modified
    try:
        current_modified = os.path.getmtime(MODEL_PATH)
        if model is None or current_modified > last_modified:
            model = joblib.load(MODEL_PATH)
            last_modified = current_modified
            print(f"✓ Model loaded/reloaded successfully at {datetime.fromtimestamp(current_modified).strftime('%Y-%m-%d %H:%M:%S')}")
        return model
    except FileNotFoundError:
        print(f"ERROR: '{MODEL_PATH}' not found.")
        return None

# --- API Endpoints ---
@app.route('/predict', methods=['GET'])
def predict():
    current_model = load_model_if_updated()
    if not current_model:
        return jsonify({'error': 'Model not loaded.'}), 500

    print(f"[{datetime.now().strftime('%H:%M:%S')}] Received forecast request.")
    
    # 1. Prepare data for the next 7 days, including the holiday feature
    future_dates = []
    today = date.today()
    for i in range(7):
        next_date = today + timedelta(days=i)
        future_dates.append({
            'date': next_date.strftime('%a, %b %d'),
            'day_of_week': next_date.weekday(),
            'month': next_date.month,
            'day_of_month': next_date.day,
            'is_weekend': (next_date.weekday() >= 5),
            'is_holiday': (next_date in holidays_date) # New holiday feature
        })

    future_df = pd.DataFrame(future_dates)
    # Select all the features the new model expects
    X_future = future_df[['day_of_week', 'month', 'day_of_month', 'is_weekend', 'is_holiday']]

    # 2. Get predictions
    predictions = current_model.predict(X_future)
    predictions = [max(0, round(p)) for p in predictions]
    print(f"Generated predictions: {predictions}")

    # 3. Format the response
    response = {'labels': list(future_df['date']), 'data': predictions}
    return jsonify(response)

@app.route('/health', methods=['GET'])
def health_check():
    # ... (Your health check code is perfect and unchanged)
    pass

# --- Run the Server ---
if __name__ == '__main__':
    print("=" * 60)
    print("🚀 Starting Upgraded Football Booking Forecast API")
    print("=" * 60)
    app.run(host='0.0.0.0', port=5000, debug=True)