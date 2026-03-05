import pandas as pd
import numpy as np

print("📌 Starting Advanced Seasonal Preprocessing...")

# ===============================
# Load Raw Data
# ===============================
df = pd.read_csv("monthly_raw.csv")

df['month'] = pd.to_datetime(df['month'])
df['total_revenue'] = df['total_revenue'].astype(float)

# Sort to avoid ordering issues
df = df.sort_values("month")

# ===============================
# Handle Duplicate Months (Real-world safety)
# ===============================
df = df.groupby("month", as_index=False)["total_revenue"].sum()

# ===============================
# Create Complete Monthly Timeline
# ===============================
all_months = pd.date_range(
    start=df['month'].min(),
    end=df['month'].max(),
    freq='MS'
)

df = df.set_index("month").reindex(all_months)
df.index.name = "month"

# ===============================
# Helper Columns
# ===============================
df['year'] = df.index.year
df['month_num'] = df.index.month

# ===============================
# Season-Based Missing Value Filling
# ===============================

def seasonal_fill(row, full_df):

    if pd.notna(row['total_revenue']):
        return row['total_revenue']

    month = row.name.month
    year = row.name.year

    # Use same month historical average
    same_month_history = full_df[
        (full_df.index.month == month) &
        (full_df.index.year < year)
    ]['total_revenue'].dropna()

    if len(same_month_history) > 0:
        return same_month_history.mean()

    return np.nan

df['total_revenue'] = df.apply(
    lambda row: seasonal_fill(row, df),
    axis=1
)

# ===============================
# Backup Linear Interpolation
# ===============================
df['total_revenue'] = df['total_revenue'].interpolate(method='linear')

# ===============================
# OUTLIER SMOOTHING
# ===============================

rolling_mean = df['total_revenue'].rolling(
    window=3,
    center=True,
    min_periods=1
).mean()

rolling_std = df['total_revenue'].rolling(
    window=3,
    center=True,
    min_periods=1
).std()

upper_limit = rolling_mean + (2 * rolling_std)
lower_limit = rolling_mean - (2 * rolling_std)

df['total_revenue'] = df['total_revenue'].clip(
    lower=lower_limit,
    upper=upper_limit
)

# ===============================
# Optional: Light Smoothing
# (helps Prophet detect trend)
# ===============================
df['total_revenue'] = df['total_revenue'].rolling(
    window=2,
    min_periods=1
).mean()

# ===============================
# Clean Helper Columns
# ===============================
df.drop(columns=['year','month_num'], inplace=True)

# Reset index for ML model
df = df.reset_index()

# ===============================
# Save ML Dataset
# ===============================
df.to_csv("monthly_preprocessed.csv", index=False)

print("📊 Final Dataset Size:", len(df))
print("✅ Seasonal Preprocessing Completed")
print("💾 Saved monthly_preprocessed.csv")