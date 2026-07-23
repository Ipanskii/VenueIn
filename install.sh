#!/bin/bash
# ============================================================
#  INSTALL.SH — Setup otomatis project VenueIn
#  Jalankan file ini di dalam folder project (setelah git clone)
#  Cara pakai: chmod +x install.sh && ./install.sh
# ============================================================

set -e  # hentikan script kalau ada perintah yang gagal

# --- UBAH SESUAI KEBUTUHAN ---
DB_NAME="venuein"
DB_USER="root"
DB_PASS=""
SQL_FILE="database_export.sql"

echo ""
echo "[1/8] Install dependency PHP (Composer)..."
composer install

echo ""
echo "[2/8] Install dependency JS (NPM)..."
npm install

echo ""
echo "[3/8] Build asset frontend..."
npm run build

echo ""
echo "[4/8] Menyalin .env.example menjadi .env..."
if [ ! -f .env ]; then
    cp .env.example .env
else
    echo "File .env sudah ada, dilewati."
fi

echo ""
echo "[5/8] Generate application key..."
php artisan key:generate

echo ""
echo "[6/8] Membuat database \"$DB_NAME\" jika belum ada..."
if [ -z "$DB_PASS" ]; then
    mysql -u"$DB_USER" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME"
else
    mysql -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME"
fi

echo ""
echo "[7/8] Import file database dari \"$SQL_FILE\"..."
if [ -f "$SQL_FILE" ]; then
    if [ -z "$DB_PASS" ]; then
        mysql -u"$DB_USER" "$DB_NAME" < "$SQL_FILE"
    else
        mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"
    fi
else
    echo "File $SQL_FILE tidak ditemukan, langkah import dilewati."
    echo "Jalankan 'php artisan migrate' secara manual jika perlu."
fi

echo ""
echo "[8/8] Membuat storage link..."
php artisan storage:link

echo ""
echo "============================================================"
echo " SELESAI! Jalankan 'php artisan serve' untuk memulai server."
echo "============================================================"
