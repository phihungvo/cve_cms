#!/bin/bash

echo "Bắt đầu init.sh" >> /tmp/init.log

# Kiểm tra và tạo file .env nếu chưa tồn tại
if [ ! -f .env ]; then
    echo "Tạo .env từ .env.example" >> /tmp/init.log
    cp .env.example .env
    if [ $? -eq 0 ]; then
        echo "Tạo file .env thành công" >> /tmp/init.log
    else
        echo "Tạo file .env thất bại" >> /tmp/init.log
        exit 1
    fi
else
    echo "File .env đã tồn tại" >> /tmp/init.log
fi

# Đảm bảo quyền truy cập file .env
chmod 644 .env
chown www-data:www-data .env

# Kiểm tra APP_KEY
if ! grep -q "^APP_KEY=.*" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ]; then
    echo "Đang tạo APP_KEY" >> /tmp/init.log
    php artisan key:generate --verbose >> /tmp/init.log 2>&1
    if [ $? -eq 0 ]; then
        echo "Tạo khóa thành công" >> /tmp/init.log
    else
        echo "Tạo khóa thất bại" >> /tmp/init.log
        exit 1
    fi
else
    echo "APP_KEY đã được thiết lập, bỏ qua tạo khóa" >> /tmp/init.log
fi

LOG="storage/logs/deploy/$(date +"%Y/%m")/$(date +"%Y-%m-%d").log"
echo "Tạo thư mục log deploy" >> /tmp/init.log
install -d $(dirname "$LOG")

echo "Chạy composer deploy-docker" >> /tmp/init.log
COMPOSER_ALLOW_SUPERUSER=1 ./composer deploy-docker >> "$LOG" 2>&1
if [ $? -eq 0 ]; then
    echo "Composer deploy-docker thành công" >> /tmp/init.log
else
    echo "Composer deploy-docker thất bại" >> /tmp/init.log
fi

echo "Khởi động cron" >> /tmp/init.log
crontab /etc/cron.d/crontab
cron

echo "Khởi động php artisan serve" >> /tmp/init.log
while true; do
    LOG="storage/logs/serve/$(date +"%Y/%m")/$(date +"%Y-%m-%d").log"
    install -d $(dirname "$LOG")
    php artisan serve --host=0.0.0.0 --port=80 --no-reload >> "$LOG" 2>&1
done