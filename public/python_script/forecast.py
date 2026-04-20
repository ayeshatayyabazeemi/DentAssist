# import pandas as pd
# from prophet import Prophet
# from sklearn.metrics import mean_absolute_error, mean_squared_error
# import numpy as np
# import json

# print("📌 Starting Real-World Forecast System...")

# # ===============================
# # Load Data
# # ===============================
# df = pd.read_csv("monthly_preprocessed.csv")
# df['month'] = pd.to_datetime(df['month'])

# df = df.rename(columns={
#     "month": "ds",
#     "total_revenue": "y"
# })

# df = df.dropna()
# output={}
# # ===============================
# # SPLIT DATA
# # ===============================

# train = df[df['ds'] < '2025-01-01']     # 2016–2024
# validate = df[df['ds'] >= '2025-01-01'] # 2025 actual

# print("📊 Train Size:", len(train))
# print("📊 Validation Size:", len(validate))

# # ===============================
# # Train Prophet
# # ===============================
# model = Prophet(
#     yearly_seasonality=True,
#     weekly_seasonality=False,
#     daily_seasonality=False,
#     changepoint_prior_scale=0.05
# )

# model.fit(train)

# # ===============================
# # VALIDATION PREDICTION (2025)
# # ===============================
# future_validate = model.make_future_dataframe(
#     periods=len(validate),
#     freq='MS'
# )

# forecast_validate = model.predict(future_validate)

# # Keep only 2025 prediction
# forecast_2025 = forecast_validate[
#     forecast_validate['ds'].isin(validate['ds'])
# ]

# # ===============================
# # Accuracy Metrics
# # ===============================
# mae = mean_absolute_error(
#     validate['y'],
#     forecast_2025['yhat']
# )

# rmse = np.sqrt(mean_squared_error(
#     validate['y'],
#     forecast_2025['yhat']
# ))

# print("📉 MAE:", round(mae, 2))
# print("📉 RMSE:", round(rmse, 2))

# # ===============================
# # FORECAST FUTURE (2026+)
# # ===============================
# future_forecast = model.make_future_dataframe(
#     periods=24,   # forecast 2 years ahead
#     freq='MS'
# )

# forecast_full = model.predict(future_forecast)

# forecast_future = forecast_full[
#     forecast_full['ds'] >= '2026-01-01'
# ]


# import calendar

# # ===============================
# # ADVANCED SEASONAL ANALYSIS
# # ===============================

# seasonality = forecast_full[['ds','yearly']].copy()
# seasonality['month'] = seasonality['ds'].dt.month

# monthly_season_avg = seasonality.groupby('month')['yearly'].mean()

# best_month = monthly_season_avg.idxmax()
# worst_month = monthly_season_avg.idxmin()

# best_month_name = calendar.month_name[best_month]
# worst_month_name = calendar.month_name[worst_month]

# recommendations = []

# # ===============================
# # SEASONAL BUSINESS INSIGHTS
# # ===============================

# recommendations.append(
#     f"Highest seasonal demand expected in {best_month_name}. Increase appointment slots and staffing to capture higher patient flow."
# )

# recommendations.append(
#     f"Lower revenue trend typically occurs in {worst_month_name}. Consider running promotional dental packages or preventive care campaigns."
# )

# # ===============================
# # TREND ANALYSIS
# # ===============================

# future_trend_start = forecast_future['yhat'].iloc[0]
# future_trend_end = forecast_future['yhat'].iloc[-1]

# if future_trend_end > future_trend_start:
#     recommendations.append(
#         "Revenue trend for upcoming years shows growth. Expanding services or adding specialists could increase clinic profitability."
#     )
# else:
#     recommendations.append(
#         "Forecast indicates potential revenue slowdown. Focus on patient retention and marketing strategies."
#     )

# # ===============================
# # RISK DETECTION
# # ===============================

# volatility = forecast_full['yhat'].std()

# if volatility > forecast_full['yhat'].mean() * 0.25:
#     recommendations.append(
#         "Revenue volatility detected. Stabilizing treatment scheduling and improving payment cycles may improve financial predictability."
#     )



# # ===============================
# # SAVE OUTPUT FOR DASHBOARD
# # ===============================

# output = {
#     "metrics": {
#         "MAE": mae,
#         "RMSE": rmse
#     },
#     "validation_2025": forecast_2025[
#         ['ds', 'yhat', 'yhat_lower', 'yhat_upper']
#     ].to_dict(orient="records"),
#     "future_forecast": forecast_future[
#         ['ds', 'yhat', 'yhat_lower', 'yhat_upper']
#     ].to_dict(orient="records")
# }
# # ===============================
# # SAVE RECOMMENDATIONS
# # ===============================

# output["recommendations"] = recommendations

# with open("forecast_finance.json", "w") as f:
#     json.dump(output, f, default=str)
# print("✅ Real-World Forecast Completed")
# print("💾 forecast_finance.json Saved")
# # ===============================
# # SEASONAL RECOMMENDATIONS
# # ===============================

# seasonality = forecast_full[['ds', 'yearly']].copy()
# seasonality['month'] = seasonality['ds'].dt.month

# monthly_season_avg = seasonality.groupby('month')['yearly'].mean()

# best_month = monthly_season_avg.idxmax()
# worst_month = monthly_season_avg.idxmin()

# recommendations = []

# recommendations.append(
#     f"Peak revenue season detected in month {best_month}. Increase marketing and staffing."
# )

# recommendations.append(
#     f"Low revenue season detected in month {worst_month}. Offer promotions or discount packages."
# )

# output["recommendations"] = recommendations
import pandas as pd
from prophet import Prophet
from sklearn.metrics import mean_absolute_error, mean_squared_error
import numpy as np
import json
import calendar

print("📌 Starting Advanced AI Forecast System...")

# ===============================
# LOAD DATA
# ===============================
df = pd.read_csv("monthly_preprocessed.csv")

df['month'] = pd.to_datetime(df['month'])
df = df.rename(columns={"month": "ds", "total_revenue": "y"})
df = df.dropna()

# Log transform (improves forecasting stability)
df['y'] = np.log1p(df['y'])

# ===============================
# HOLIDAY EFFECTS
# ===============================
holidays = pd.DataFrame({
    'holiday': [
        'eid', 'eid', 'eid',
        'ramadan', 'ramadan',
        'new_year'
    ],
    'ds': pd.to_datetime([
        '2022-05-02',
        '2023-04-21',
        '2024-04-10',
        '2023-03-23',
        '2024-03-11',
        '2024-01-01'
    ]),
    'lower_window': -3,
    'upper_window': 3
})

# ===============================
# SPLIT DATA (VALIDATION)
# ===============================
train = df[df['ds'] < '2025-01-01']
validate = df[df['ds'] >= '2025-01-01']

print("📊 Train Size:", len(train))
print("📊 Validation Size:", len(validate))

# ===============================
# TRAIN MODEL (VALIDATION)
# ===============================
model = Prophet(
    yearly_seasonality=True,
    weekly_seasonality=False,
    daily_seasonality=False,
    holidays=holidays,
    changepoint_prior_scale=0.15,
    seasonality_prior_scale=10
)

# Custom monthly seasonality
model.add_seasonality(
    name='monthly',
    period=30.5,
    fourier_order=5
)

model.fit(train)

# ===============================
# VALIDATION FORECAST
# ===============================
future_validate = model.make_future_dataframe(
    periods=len(validate),
    freq='MS'
)

forecast_validate = model.predict(future_validate)

forecast_2025 = forecast_validate[
    forecast_validate['ds'].isin(validate['ds'])
]

# Reverse log transform
validate_actual = np.expm1(validate['y'])
predicted = np.expm1(forecast_2025['yhat'])

# ===============================
# ACCURACY METRICS
# ===============================
mae = mean_absolute_error(validate_actual, predicted)
rmse = np.sqrt(mean_squared_error(validate_actual, predicted))

print("📉 MAE:", round(mae,2))
print("📉 RMSE:", round(rmse,2))

# ===============================
# FINAL MODEL (FULL DATA)
# ===============================
final_model = Prophet(
    yearly_seasonality=True,
    weekly_seasonality=False,
    daily_seasonality=False,
    holidays=holidays,
    changepoint_prior_scale=0.15,
    seasonality_prior_scale=10
)

final_model.add_seasonality(
    name='monthly',
    period=30.5,
    fourier_order=5
)

final_model.fit(df)

# ===============================
# FUTURE FORECAST
# ===============================
future = final_model.make_future_dataframe(
    periods=3,
    freq='MS'
)

forecast_future = final_model.predict(future)

forecast_future = forecast_future[
    forecast_future['ds'] > df['ds'].max()
]

# Reverse log transform
forecast_future['yhat'] = np.expm1(forecast_future['yhat'])
forecast_future['yhat_lower'] = np.expm1(forecast_future['yhat_lower'])
forecast_future['yhat_upper'] = np.expm1(forecast_future['yhat_upper'])


# ===============================
# DEMAND WINDOW ANALYSIS
# ===============================

forecast_future['month'] = forecast_future['ds'].dt.month
forecast_future['month_name'] = forecast_future['ds'].dt.strftime('%B')

avg_revenue = forecast_future['yhat'].mean()

# classify demand level
def classify_demand(val):
    if val > avg_revenue * 1.10:
        return "High"
    elif val < avg_revenue * 0.90:
        return "Low"
    else:
        return "Normal"

forecast_future['demand'] = forecast_future['yhat'].apply(classify_demand)

# detect consecutive demand windows
windows = []
current_window = None

for i, row in forecast_future.iterrows():

    if current_window is None:
        current_window = {
            "type": row['demand'],
            "start": row['month_name'],
            "end": row['month_name']
        }

    elif row['demand'] == current_window["type"]:
        current_window["end"] = row['month_name']

    else:
        windows.append(current_window)
        current_window = {
            "type": row['demand'],
            "start": row['month_name'],
            "end": row['month_name']
        }

windows.append(current_window)

# ===============================
# SEASONAL ANALYSIS
# ===============================
forecast_full = pd.concat(
    [forecast_validate, forecast_future],
    ignore_index=True
)

seasonality = forecast_full[['ds','yearly']].copy()
seasonality['month'] = seasonality['ds'].dt.month

monthly_avg = seasonality.groupby('month')['yearly'].mean()

best_month = monthly_avg.idxmax()
worst_month = monthly_avg.idxmin()

best_month_name = calendar.month_name[best_month]
worst_month_name = calendar.month_name[worst_month]

# ===============================
# SMART BUSINESS RECOMMENDATIONS
# ===============================

recommendations = []

# 1️⃣ Merge consecutive windows with same demand type
merged = []
for w in windows:
    if not merged or merged[-1]["type"] != w["type"]:
        merged.append(w.copy())
    else:
        merged[-1]["end"] = w["end"]

# 2️⃣ Generate demand recommendations
priority = {"High":3, "Low":2,"Normal": 1, "Stable":1}

for w in merged:

    start = w["start"]
    end = w["end"]

    period = start if start == end else f"{start} to {end}"

    if w["type"] == "High":
        msg = f"High patient demand expected from {period}. Increase dentist availability and hygiene slots."
    elif w["type"] == "Low":
        msg = f"Lower patient visits predicted from {period}. Consider promotions or preventive care campaigns."

    elif w["type"] in ["Normal", "Stable"]:
        msg = f"Stable demand expected from {period}. Maintain regular staffing and focus on patient retention."

    recommendations.append((priority[w["type"]], msg))

# 3️⃣ Revenue trend insight
if len(forecast_future) > 0:

    first_val = forecast_future['yhat'].iloc[0]
    last_val = forecast_future['yhat'].iloc[-1]

    change = ((last_val - first_val) / max(first_val,1)) * 100

    if change > 10:
        msg = f"Revenue forecast indicates strong growth of {change:.1f}%. Consider expanding services or clinic capacity."
        score = 4
    elif change < -10:
        msg = f"Revenue forecast indicates a decline of {abs(change):.1f}%. Focus on retention and targeted marketing."
        score = 4
    else:
        msg = f"Revenue expected to remain stable ({change:.1f}%). Maintain operational efficiency."
        score = 2

    recommendations.append((score, msg))

# 4️⃣ Rank by importance and remove duplicates
recommendations = sorted(set(recommendations), reverse=True)

# 5️⃣ Return only top insights
recommendations = [r[1] for r in recommendations[:4]]
# ===============================
# VOLATILITY DETECTION
# ===============================
# volatility = forecast_future['yhat'].std()
# mean_val = forecast_future['yhat'].mean()


# if volatility > mean_val * 0.25:
#     recommendations.append(
#         "High revenue variability detected. Stabilize cash flow by increasing preventive care packages and recall appointments."
#     )

# ===============================
# MERGE VALIDATION RESULTS
# ===============================
validation_output = []

for i in range(len(validate)):

    actual = float(np.expm1(validate.iloc[i]['y']))
    pred = float(np.expm1(forecast_2025.iloc[i]['yhat']))

    validation_output.append({

        "ds": str(validate.iloc[i]['ds']),
        "actual": actual,
        "predicted": pred,
        "yhat_lower": float(np.expm1(forecast_2025.iloc[i]['yhat_lower'])),
        "yhat_upper": float(np.expm1(forecast_2025.iloc[i]['yhat_upper'])),
        "anomaly": abs(pred - actual) > pred * 0.15

    })


# ===============================
# SAVE JSON
# ===============================
output = {

    "metrics": {
        "MAE": float(mae),
        "RMSE": float(rmse)
    },

    "validation_2025": validation_output,

    "future_forecast": forecast_future[
        ['ds','yhat','yhat_lower','yhat_upper']
    ].to_dict(orient="records"),

    "recommendations": recommendations

}

with open("forecast_finance.json", "w") as f:
    json.dump(output, f, default=str)

print("✅ Advanced Forecast Completed")
print("💾 forecast_finance.json Saved")




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