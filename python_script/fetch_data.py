import requests
import pandas as pd

print("📌 Fetching Data From API...")

response = requests.get("http://localhost:8080/invoice/data")
data = response.json()

df = pd.DataFrame(data)

df['month'] = pd.to_datetime(df['month'])
df['total_revenue'] = df['total_revenue'].astype(float)

# Keep from 2016 onward
df = df[df['month'] >= '2016-01-01']
df = df.sort_values('month')

print("📊 Rows Received:", len(df))

df.to_csv("monthly_raw.csv", index=False)

print("💾 Saved monthly_raw.csv")
print("✅ fetch_data.py Completed")