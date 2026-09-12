# PortSys Full Project Update Report

## التعديلات المنجزة

### 1) استبدال الشعار الرئيسي
تم اعتماد الشعار المرفق داخل المشروع كاملًا، وإضافة نسخ مناسبة منه:

- `app/Resources/portsys/portsys-logo.png`
- `app/Resources/portsys/portsys-wordmark.png`
- `app/Resources/portsys/portsys-mark.png`
- `app/Resources/portsys/portsys-icon-192.png`
- `app/Resources/portsys/portsys-icon-512.png`
- `app/Resources/portsys/apple-touch-icon.png`
- `app/Resources/portsys/favicon.ico`
- `app/Resources/portsys/favicon.svg`
- `public/assets/images/logo.png`
- `public/favicon.ico`

وتم ربط الشعار الجديد في:

- الواجهة العامة.
- الفوتر.
- صفحات الدخول والتسجيل.
- لوحة التحكم.
- أيقونة المتصفح والـ manifest.

### 2) إصلاح مشكلة ظهور صورة الشعار بدل تسجيل الدخول أو تنفيذ الإجراءات
تم إصلاح السبب من:

- `app/Config/App.php`
- `app/Controllers/PortsysAssets.php`
- `app/Config/Routes.php`

كان هناك احتمال في بيئات XAMPP/FastCGI أن يدخل مسار مثل:

```text
index.php/portsys-assets/portsys-mark.svg
```
داخل `baseURL`، فيتم إرسال بعض النماذج والأزرار إلى مسار الصورة، فيعرض المتصفح الشعار بدل تنفيذ الطلب.

تم تعديل اكتشاف `baseURL` ليقطع المسار عند أول `/index.php` بشكل صحيح، وإضافة حماية تمنع التعامل مع مسار الأصول كصفحة تطبيق.

### 3) تعديل صفحة about
في صفحة:

```text
index.php/about
```
تم تعديل فريق العمل ليكون:

- محمد الخاوندي — 193347 — مطور البرمجيات.
- لانا الحاتم — 205886 — مصممة الواجهات.

مع تحسين عرض البطاقات وإظهار الرقم ضمن Badge واضح.

### 4) تحسينات إضافية
- تحديث رقم نسخة الشعار لكسر كاش المتصفح القديم.
- إضافة نسخة wordmark للشعار حتى يظهر بشكل مناسب في الهيدر واللوحة بدل تصغير الشعار الكامل بشكل غير مقروء.
- تنظيف الربط البصري للشعار في اللوحة والواجهة العامة وصفحات المصادقة.
- إضافة ملاحظات داخل المشروع: `app/PORTSYS_V4_FULL_PROJECT_NOTES.md`.

## نتائج الفحص الثابت

- فحص PHP syntax:
  - عدد الملفات المفحوصة: 153.
  - الأخطاء: 0.
- فحص routes مقابل Controllers/Methods:
  - عدد المراجع: 75.
  - المفقود: 0.
- فحص Views المستخدمة:
  - Views مفقودة: 0.
- فحص الملفات المستدعاة عبر `portsys_asset`:
  - Assets مفقودة: 0.
- فحص ملفات اللغة:
  - العربية: 888 مفتاح.
  - الإنجليزية: 888 مفتاح.
  - الفرق بين الملفات: 0.

## ملاحظة تشغيل مهمة

لم يتم تشغيل المشروع داخل متصفح فعلي في هذه البيئة لأن PHP الموجود في sandbox لا يحتوي امتداد `mbstring`، وCodeIgniter4 يحتاجه. لذلك تم إجراء فحص ثابت شامل للملفات والروابط والمسارات.

على جهازك في XAMPP تأكد أن امتداد `mbstring` مفعّل من `php.ini`، ثم أعد تشغيل Apache.

## بعد التركيب

1. استبدل المشروع بالنسخة المرفقة.
2. نفذ عند الحاجة:

```bash
php spark migrate
```

3. افتح المتصفح واعمل:

```text
Ctrl + F5
```

حتى يتم تحميل الشعار الجديد بدل الكاش القديم.
