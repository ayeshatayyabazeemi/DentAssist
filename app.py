from flask import Flask, jsonify, request
import sqlite3

app = Flask(__name__)
DB_NAME = "courses.db"

# ---------------- GET ALL COURSES ----------------
@app.get("/api/courses")
def get_courses():
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("SELECT id, title, credit_hours FROM courses")
    rows = cur.fetchall()
    conn.close()

    courses = [{"id": r[0], "title": r[1], "credit_hours": r[2]} for r in rows]
    return jsonify(courses), 200


# ---------------- GET COURSE BY ID ----------------
@app.get("/api/courses/<int:course_id>")
def get_course(course_id):
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("SELECT id, title, credit_hours FROM courses WHERE id = ?", (course_id,))
    row = cur.fetchone()
    conn.close()

    if row is None:
        return jsonify({"error": "Course not found"}), 404

    course = {"id": row[0], "title": row[1], "credit_hours": row[2]}
    return jsonify(course), 200


# ---------------- CREATE COURSE ----------------
@app.post("/api/courses")
def create_course():
    data = request.get_json(silent=True)

    if not data or "title" not in data or "credit_hours" not in data:
        return jsonify({"error": "Missing required fields: title, credit_hours"}), 400
    if not title or title.strip() == "":
        return jsonify({"error": "Title cannot be empty"}), 400

    if not isinstance(credit_hours, int) or credit_hours <= 0:
       return jsonify({"error": "credit_hours must be numeric and greater than 0"}), 400
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute(
        "INSERT INTO courses (title, credit_hours) VALUES (?, ?)",
        (data["title"], data["credit_hours"])
    )
    conn.commit()
    new_id = cur.lastrowid
    conn.close()

    return jsonify({
        "message": "Course created",
        "course": {
            "id": new_id,
            "title": data["title"],
            "credit_hours": data["credit_hours"]
        }
    }), 201


# ---------------- UPDATE COURSE ----------------
@app.put("/api/courses/<int:course_id>")
def update_course(course_id):
    data = request.get_json(silent=True)

    if not data or "title" not in data or "credit_hours" not in data:
        return jsonify({"error": "Missing required fields: title, credit_hours"}), 400

    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute(
        "UPDATE courses SET title = ?, credit_hours = ? WHERE id = ?",
        (data["title"], data["credit_hours"], course_id)
    )
    conn.commit()
    updated = cur.rowcount
    conn.close()

    if updated == 0:
        return jsonify({"error": "Course not found"}), 404

    return jsonify({
        "message": "Course updated",
        "course": {
            "id": course_id,
            "title": data["title"],
            "credit_hours": data["credit_hours"]
        }
    }), 200


# ---------------- DELETE COURSE ----------------
@app.delete("/api/courses/<int:course_id>")
def delete_course(course_id):
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("DELETE FROM courses WHERE id = ?", (course_id,))
    conn.commit()
    deleted = cur.rowcount
    conn.close()

    if deleted == 0:
        return jsonify({"error": "Course not found"}), 404

    return jsonify({"message": "Course deleted"}), 200


if __name__ == "__main__":
    app.run(debug=True)
