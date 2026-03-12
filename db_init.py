import sqlite3

DB_NAME = "courses.db"

def init_db():
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("""
    CREATE TABLE IF NOT EXISTS courses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        credit_hours INTEGER NOT NULL
    )
    """)
    conn.commit()
    conn.close()

init_db()
print("Courses database initialized.")
