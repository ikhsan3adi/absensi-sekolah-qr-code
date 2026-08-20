# source: https://raw.githubusercontent.com/alimochtar78/absensi-wajah-laravel/refs/heads/main/face_recognition_api.py
# modified by: mdestafadilah

from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import base64
import cv2
import os

app = Flask(__name__)

# Folder yang berisi wajah siswa
KNOWN_FACES_DIR = os.getenv("KNOWN_FACES_DIR", "writable/faces")
ALLOWED_EXT = (".jpg", ".jpeg", ".png", ".bmp", ".webp")
known_face_encodings = []
known_face_names = []

# Load semua wajah yang sudah didaftarkan.
# File non-gambar (mis. .gitkeep) dan file rusak dilewati agar service tetap jalan.
os.makedirs(KNOWN_FACES_DIR, exist_ok=True)
for filename in sorted(os.listdir(KNOWN_FACES_DIR)):
    if not filename.lower().endswith(ALLOWED_EXT):
        continue
    try:
        image = face_recognition.load_image_file(f"{KNOWN_FACES_DIR}/{filename}")
        encoding = face_recognition.face_encodings(image)
    except Exception as e:
        print(f"[face-api] Gagal memuat {filename}: {e}")
        continue
    if encoding:
        known_face_encodings.append(encoding[0])
        known_face_names.append(filename.split(".")[0])  # Nama file = Nama siswa

print(f"[face-api] {len(known_face_names)} wajah terdaftar dimuat dari {KNOWN_FACES_DIR}")

@app.route("/verify-face", methods=["POST"])
def verify_face():
    try:
        # Ambil data dari request
        data = request.json
        img_data = base64.b64decode(data["face_encoding"].split(",")[1])
        img_array = np.frombuffer(img_data, dtype=np.uint8)
        img = cv2.imdecode(img_array, cv2.IMREAD_COLOR)

        if img is None:
            return jsonify({"status": "error", "message": "Gambar tidak dapat diproses!"}), 400

        # Konversi BGR (OpenCV) → RGB (face_recognition)
        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

        # Cari wajah dalam gambar
        face_locations = face_recognition.face_locations(img)
        face_encodings = face_recognition.face_encodings(img, face_locations)

        if not face_encodings:
            return jsonify({"status": "error", "message": "Wajah tidak terdeteksi!"}), 400

        # Bandingkan wajah yang dikirim dengan yang sudah terdaftar
        for face_encoding in face_encodings:
            matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
            if True in matches:
                matched_name = known_face_names[matches.index(True)]
                return jsonify({"status": "success", "name": matched_name})

        return jsonify({"status": "error", "message": "Wajah tidak dikenali!"}), 400

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)