FROM php:8.2-apache

# تفعيل مود إعادة الكتابة لـ Apache (مهم للـ .htaccess)
RUN a2enmod rewrite

# نسخ ملفات موقعك إلى مجلد السيرفر واختيار الصلاحيات المناسبة
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
