#!/bin/bash

echo "Bắt đầu init.sh" | tee -a /tmp/init.log

# Kiểm tra và tạo file .env nếu chưa tồn tại
if [ ! -f /app/.env ]; then
    echo "Tạo .env từ .env.example" | tee -a /tmp/init.log
    cp /app/.env.example /app/.env
    if [ $? -eq 0 ]; then
        echo "Tạo file .env thành công" | tee -a /tmp/init.log
    else
        echo "Tạo file .env thất bại" | tee -a /tmp/init.log
        exit 1
    fi
else
    echo "File .env đã tồn tại" | tee -a /tmp/init.log
fi

# Đảm bảo quyền truy cập file .env
chmod 644 /app/.env
chown www-data:www-data /app/.env

# Kiểm tra nội dung .env trước khi tạo APP_KEY
echo "Nội dung APP_KEY trước khi tạo:" | tee -a /tmp/init.log
grep "^APP_KEY=" /app/.env | tee -a /tmp/init.log || echo "Không tìm thấy APP_KEY" | tee -a /tmp/init.log

# Kiểm tra APP_KEY
if ! grep -q "^APP_KEY=.*" /app/.env || [ -z "$(grep '^APP_KEY=' /app/.env | cut -d'=' -f2)" ]; then
    echo "Đang tạo APP_KEY" | tee -a /tmp/init.log
    su www-data -s /bin/bash -c "php /app/artisan key:generate --verbose" 2>&1 | tee -a /tmp/init.log
    if [ $? -eq 0 ]; then
        echo "Tạo khóa thành công" | tee -a /tmp/init.log
        echo "Nội dung APP_KEY sau khi tạo:" | tee -a /tmp/init.log
        grep "^APP_KEY=" /app/.env | tee -a /tmp/init.log || echo "Không tìm thấy APP_KEY" | tee -a /tmp/init.log
    else
        echo "Tạo khóa thất bại" | tee -a /tmp/init.log
        exit 1
    fi
else
    echo "APP_KEY đã được thiết lập, bỏ qua tạo khóa" | tee -a /tmp/init.log
fi

LOG="/app/storage/logs/deploy/$(date +"%Y/%m")/$(date +"%Y-%m-%d").log"
echo "Tạo thư mục log deploy" | tee -a /tmp/init.log
install -d $(dirname "$LOG")

echo "Chạy composer deploy-docker" | tee -a /tmp/init.log
COMPOSER_ALLOW_SUPERUSER=1 /app/composer deploy-docker >> "$LOG" 2>&1
if [ $? -eq 0 ]; then
    echo "Composer deploy-docker thành công" | tee -a /tmp/init.log
else
    echo "Composer deploy-docker thất bại" | tee -a /tmp/init.log
fi

echo "Khởi động cron" | tee -a /tmp/init.log
crontab /etc/cron.d/crontab
cron

echo "Khởi động php artisan serve" | tee -a /tmp/init.log
while true; do
    LOG="/app/storage/logs/serve/$(date +"%Y/%m")/$(date +"%Y-%m-%d").log"
    install -d $(dirname "$LOG")
    su www-data -s /bin/bash -c "php /app/artisan serve --host=0.0.0.0 --port=80 --no-reload" >> "$LOG" 2>&1
done