# train_model.py (Upgraded Version)

import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error
import joblib

print("Starting AI model training process...")

# --- 1. Load Data ---
try:
    df = pd.read_csv('training_data.csv')
    print("Successfully loaded training_data.csv")
except FileNotFoundError:
    print("Error: training_data.csv not found. Please run 'php artisan export:training-data' first.")
    exit()

print(f"Found {len(df)} paid bookings to learn from.")

# --- 2. Feature Engineering ---
df['slot_Date'] = pd.to_datetime(df['slot_Date'])
daily_bookings = df.groupby('slot_Date').size().reset_index(name='total_bookings')

# Define Malaysian Public Holidays for 2025 (add more years as needed)
public_holidays = [
    '2025-01-01', '2025-01-29', '2025-03-30', '2025-03-31', '2025-05-01',
    '2025-05-12', '2025-06-02', '2025-06-06', '2025-08-31', '2025-09-16',
    '2025-10-20', '2025-12-25',
]
holidays_date = pd.to_datetime(public_holidays)

# Create all features for the model
daily_bookings['day_of_week'] = daily_bookings['slot_Date'].dt.dayofweek
daily_bookings['month'] = daily_bookings['slot_Date'].dt.month
daily_bookings['day_of_month'] = daily_bookings['slot_Date'].dt.day
daily_bookings['is_weekend'] = (daily_bookings['day_of_week'] >= 5).astype(int)
daily_bookings['is_holiday'] = daily_bookings['slot_Date'].isin(holidays_date).astype(int) # New holiday feature

print("Feature engineering complete. 'is_holiday' feature added.")

# --- 3. Train the Upgraded Model ---
# The features list now includes is_holiday
features = ['day_of_week', 'month', 'day_of_month', 'is_weekend', 'is_holiday']
target = 'total_bookings'

X = daily_bookings[features]
y = daily_bookings[target]

# Split data for training and accuracy testing
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Use the more powerful RandomForestRegressor
model = RandomForestRegressor(n_estimators=100, random_state=42, min_samples_leaf=1)
model.fit(X_train, y_train)

print("Model training with RandomForest complete.")

# --- 4. Measure Model Accuracy ---
predictions = model.predict(X_test)
mae = mean_absolute_error(y_test, predictions)
print(f"Model Accuracy (Mean Absolute Error): On average, the prediction is off by {mae:.2f} bookings.")

# --- 5. Save the Upgraded Model ---
joblib.dump(model, 'booking_forecast_model.pkl')
print("\nSUCCESS: Upgraded AI model has been trained and saved as 'booking_forecast_model.pkl'")