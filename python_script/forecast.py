import pandas as pd
from prophet import Prophet
from sklearn.metrics import mean_absolute_error, mean_squared_error
import numpy as np
import json

print("📌 Starting Real-World Forecast System...")

# ===============================
# Load Data
# ===============================
df = pd.read_csv("monthly_preprocessed.csv")
df['month'] = pd.to_datetime(df['month'])

df = df.rename(columns={
    "month": "ds",
    "total_revenue": "y"
})

df = df.dropna()
output={}
# ===============================
# SPLIT DATA
# ===============================

train = df[df['ds'] < '2025-01-01']     # 2016–2024
validate = df[df['ds'] >= '2025-01-01'] # 2025 actual

print("📊 Train Size:", len(train))
print("📊 Validation Size:", len(validate))

# ===============================
# Train Prophet
# ===============================
model = Prophet(
    yearly_seasonality=True,
    weekly_seasonality=False,
    daily_seasonality=False,
    changepoint_prior_scale=0.05
)

model.fit(train)

# ===============================
# VALIDATION PREDICTION (2025)
# ===============================
future_validate = model.make_future_dataframe(
    periods=len(validate),
    freq='MS'
)

forecast_validate = model.predict(future_validate)

# Keep only 2025 prediction
forecast_2025 = forecast_validate[
    forecast_validate['ds'].isin(validate['ds'])
]

# ===============================
# Accuracy Metrics
# ===============================
mae = mean_absolute_error(
    validate['y'],
    forecast_2025['yhat']
)

rmse = np.sqrt(mean_squared_error(
    validate['y'],
    forecast_2025['yhat']
))

print("📉 MAE:", round(mae, 2))
print("📉 RMSE:", round(rmse, 2))

# ===============================
# FORECAST FUTURE (2026+)
# ===============================
future_forecast = model.make_future_dataframe(
    periods=24,   # forecast 2 years ahead
    freq='MS'
)

forecast_full = model.predict(future_forecast)

forecast_future = forecast_full[
    forecast_full['ds'] >= '2026-01-01'
]


import calendar

# ===============================
# ADVANCED SEASONAL ANALYSIS
# ===============================

seasonality = forecast_full[['ds','yearly']].copy()
seasonality['month'] = seasonality['ds'].dt.month

monthly_season_avg = seasonality.groupby('month')['yearly'].mean()

best_month = monthly_season_avg.idxmax()
worst_month = monthly_season_avg.idxmin()

best_month_name = calendar.month_name[best_month]
worst_month_name = calendar.month_name[worst_month]

recommendations = []

# ===============================
# SEASONAL BUSINESS INSIGHTS
# ===============================

recommendations.append(
    f"Highest seasonal demand expected in {best_month_name}. Increase appointment slots and staffing to capture higher patient flow."
)

recommendations.append(
    f"Lower revenue trend typically occurs in {worst_month_name}. Consider running promotional dental packages or preventive care campaigns."
)

# ===============================
# TREND ANALYSIS
# ===============================

future_trend_start = forecast_future['yhat'].iloc[0]
future_trend_end = forecast_future['yhat'].iloc[-1]

if future_trend_end > future_trend_start:
    recommendations.append(
        "Revenue trend for upcoming years shows growth. Expanding services or adding specialists could increase clinic profitability."
    )
else:
    recommendations.append(
        "Forecast indicates potential revenue slowdown. Focus on patient retention and marketing strategies."
    )

# ===============================
# RISK DETECTION
# ===============================

volatility = forecast_full['yhat'].std()

if volatility > forecast_full['yhat'].mean() * 0.25:
    recommendations.append(
        "Revenue volatility detected. Stabilizing treatment scheduling and improving payment cycles may improve financial predictability."
    )



# ===============================
# SAVE OUTPUT FOR DASHBOARD
# ===============================

output = {
    "metrics": {
        "MAE": mae,
        "RMSE": rmse
    },
    "validation_2025": forecast_2025[
        ['ds', 'yhat', 'yhat_lower', 'yhat_upper']
    ].to_dict(orient="records"),
    "future_forecast": forecast_future[
        ['ds', 'yhat', 'yhat_lower', 'yhat_upper']
    ].to_dict(orient="records")
}
# ===============================
# SAVE RECOMMENDATIONS
# ===============================

output["recommendations"] = recommendations

with open("forecast_finance.json", "w") as f:
    json.dump(output, f, default=str)
print("✅ Real-World Forecast Completed")
print("💾 forecast_finance.json Saved")
# ===============================
# SEASONAL RECOMMENDATIONS
# ===============================

seasonality = forecast_full[['ds', 'yearly']].copy()
seasonality['month'] = seasonality['ds'].dt.month

monthly_season_avg = seasonality.groupby('month')['yearly'].mean()

best_month = monthly_season_avg.idxmax()
worst_month = monthly_season_avg.idxmin()

recommendations = []

recommendations.append(
    f"Peak revenue season detected in month {best_month}. Increase marketing and staffing."
)

recommendations.append(
    f"Low revenue season detected in month {worst_month}. Offer promotions or discount packages."
)

output["recommendations"] = recommendations

# trend_last = forecast_future['yhat'].iloc[-1]
# trend_first = forecast_future['yhat'].iloc[0]

# if trend_last > trend_first:
#     recommendations.append(
#         "Overall upward revenue trend detected for 2026–2027. Consider expanding services."
#     )
# else:
#     recommendations.append(
#         "Revenue slowdown predicted. Focus on patient retention and marketing."
#     )