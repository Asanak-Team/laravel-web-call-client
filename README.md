# 📦 Laravel WebCall Client

یک پکیج مدرن Laravel برای مدیریت تماس ها از طریق یک سرویس RESTful.

---

## 🔧 نصب پکیج

ابتدا از طریق Composer نصب کنید:

```bash
composer require farzad-forouzanfar/WebCall-client
```

سپس فایل پیکربندی را publish نمایید:

```bash
php artisan vendor:publish --tag=asanak-config
```

و فایل `.env` پروژه را با مقادیر زیر تکمیل کنید:

```env
ASANAK_WEB_CALL_USERNAME=your-username
ASANAK_WEB_CALL_PASSWORD=your-password
ASANAK_WEB_CALL_BASE_URL=https://callapi.asanak.com
ASANAK_WEB_CALL_LOG=true
```

پکیج به صورت اتوماتیک provider و facade را به اپلیکیشن اضافه می‌کند، نیاز به تعریف دستی نیست.

---

## ✅ استفاده در پروژه لاراول

### 1. افزودن فایل صوتی جدید


```php
use Asanak\WebCall\Facade\AsanakWebCallFacade as AsanakWebCall;

try {
    $data = AsanakWebCall::uploadNewVoice('/path/file/voice.mp3');
    dd($data);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

### 2. تماس از طریق فایل صوتی


```php
use Asanak\WebCall\Facade\AsanakWebCallFacade as AsanakWebCall;

try {
    $data = AsanakWebCall::callByVoice('VOICE_FILE_ID', '09120000000');
    dd($data);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

### 3. تماس OTP


```php
use Asanak\WebCall\Facade\AsanakWebCallFacade as AsanakWebCall;

try {
    $data = AsanakWebCall::callByOtp(1234, '09120000000');
    dd($data);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

### 4. مشاهده وضعیت تماس ها

```php
use Asanak\WebCall\Facade\AsanakWebCallFacade as AsanakWebCall;

try {
    $data = AsanakWebCall::callStatus(['CALL_ID_1', 'CALL_ID_2']);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

### 5. دریافت اعتبار

```php
use Asanak\WebCall\Facade\AsanakWebCallFacade as AsanakWebCall;

try {
    $data = AsanakWebCall::getCredit();
    dd($data['credit']);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

---

## 🧰 لاگ‌گذاری و مانیتورینگ

در صورتی که مقدار `ASANAK_WEB_CALL_LOG` در `.env` برابر `true` باشد، لاگ درخواست‌ها و پاسخ‌ها در `log` لاراول ثبت می‌گردد.

---

## 📄 منابع و مستندات

- 🌐 [صفحه اصلی سرویس تماس آسا‌نک](https://callapi.asanak.com/)
- 🧾 [مستندات آنلاین کامل](https://callapi.asanak.com/docs/v1)
- 🚀 [مستندات آنلاین Postman](https://documenter.getpostman.com/view/21876448/2sB2qcDM5m)
- ⬇️ [دانلود فایل کالکشن Postman](https://callapi.asanak.com/docs/v1/Asanak_Call_API_Service.postman_collection.json)

---


## 🙋‍♂️ پشتیبانی

📞 تماس: [۰۲١۶۴۰۶۳۱۸۰](https://asanak.com/call_to_asanak)
📨 ایمیل: [info@asanak.ir](mailto:info@asanak.ir)
