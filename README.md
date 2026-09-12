# PortSys

نظام ويب لإدارة الموانئ والسفن والرحلات البحرية، مبني باستخدام PHP وCodeIgniter 4. يوفر النظام واجهة ثنائية اللغة بالعربية والإنجليزية، مع صلاحيات منفصلة للمستخدمين والمديرين.

## المزايا

- تسجيل دخول وإنشاء حسابات وإدارة الملف الشخصي وكلمة المرور.
- لوحة تحكم للمستخدمين والمديرين مع صلاحيات مبنية على الأدوار.
- إدارة الموانئ والمواقع والسفن والرحلات من خلال عمليات الإضافة والتعديل والحذف.
- إنشاء طلبات الرحلات ومراجعتها وتحديث حالتها من قبل المدير.
- دليل عام للموانئ والسفن، وبحث داخل النظام.
- كتالوج مبدئي للموانئ العالمية مع الإحداثيات والمنطقة الزمنية.
- استيراد المواقع الافتراضية ومزامنتها مع بيانات الموانئ دون تكرار.
- سجل نشاطات ومركز رسائل وإشعارات.
- تصدير رحلات المستخدم إلى ملف CSV.
- دعم العربية والإنجليزية مع حفظ اللغة واتجاه الواجهة.

> بيانات الإحداثيات الموجودة في الكتالوج مخصصة للاستخدام التجريبي والعروض، وليست بيانات ملاحة بحرية معتمدة.

## التقنيات

- PHP 8.2 أو أحدث
- CodeIgniter 4.7
- MySQL أو MariaDB
- Composer
- PHPUnit للاختبارات
- Bootstrap وLeaflet وOpenStreetMap لواجهة النظام والخرائط

## متطلبات التشغيل

تأكد من تفعيل امتدادات PHP التالية:

- `intl`
- `mbstring`
- `json`
- `mysqli` أو برنامج قاعدة البيانات المستخدم
- `curl`

يُفضّل استخدام XAMPP على Windows، مع تشغيل Apache وMySQL.

## التثبيت المحلي

### 1. تنزيل المشروع

```bash
git clone https://github.com/USERNAME/portsys.git
cd portsys
composer install
```

استبدل `USERNAME/portsys` برابط مستودع GitHub الحقيقي.

### 2. إعداد ملف البيئة

انسخ ملف البيئة إلى `.env`:

```bash
copy env .env
```

ثم عدّل الإعدادات بما يناسب جهازك:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/project1/'
app.indexPage = ''
app.appTimezone = 'Asia/Damascus'

database.default.hostname = localhost
database.default.database = portsys
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

أنشئ قاعدة بيانات باسم `portsys` من phpMyAdmin أو باستخدام MySQL قبل تشغيل الترحيلات.

### 3. تشغيل الترحيلات

من جذر المشروع شغّل:

```bash
php spark migrate
```

### 4. إضافة الحسابات التجريبية

```bash
php spark db:seed UserSeeder
```

الحسابات الافتراضية:

| الدور | البريد الإلكتروني | كلمة المرور |
| --- | --- | --- |
| مدير | `admin@example.com` | `PortSys@2026` |
| مستخدم | `user@example.com` | `User@2026` |

غيّر هذه القيم في `.env` قبل تشغيل Seeder على أي بيئة مشتركة أو إنتاجية:

```ini
PORTSYS_ADMIN_EMAIL = 'admin@example.com'
PORTSYS_ADMIN_PASSWORD = 'ضع-كلمة-مرور-قوية'
PORTSYS_USER_EMAIL = 'user@example.com'
PORTSYS_USER_PASSWORD = 'ضع-كلمة-مرور-قوية'
```

### 5. تشغيل المشروع

يمكن تشغيله بإحدى الطرق التالية:

```bash
php spark serve
```

ثم افتح [http://localhost:8080](http://localhost:8080).

أو مع XAMPP، اجعل مجلد `public` هو مجلد الويب العام وافتح:

```text
http://localhost/project1/public/
```

لتحسين الأمان في الإنتاج، يجب توجيه Apache إلى مجلد `public` بدل جذر المشروع حتى لا تكون ملفات `app` و`writable` مكشوفة.

## الاختبارات

لتشغيل اختبارات PHPUnit:

```bash
composer test
```

أو:

```bash
vendor/bin/phpunit
```

## بنية المشروع

```text
app/                منطق التطبيق: Controllers وModels وViews وConfig
app/Database/       Migrations وSeeds الخاصة بقاعدة البيانات
app/Language/       ملفات الترجمة العربية والإنجليزية
app/Libraries/      الخدمات والمزامنة والكتالوجات المساعدة
public/             نقطة الدخول والملفات العامة
tests/              اختبارات المشروع
writable/           الكاش والجلسات والملفات المؤقتة
spark               أداة أوامر CodeIgniter
```

## روابط مهمة داخل النظام

- `/` الصفحة الرئيسية
- `/login` تسجيل الدخول
- `/register` إنشاء حساب
- `/dashboard` لوحة التحكم بعد تسجيل الدخول
- `/ports-directory` دليل الموانئ العام
- `/ships-directory` دليل السفن العام
- `/places` إدارة المواقع للمدير
- `/trips` إدارة الرحلات للمستخدمين والمديرين

## ملاحظات أمنية

- لا ترفع ملف `.env` إلى GitHub؛ يحتوي على إعدادات قاعدة البيانات والأسرار.
- غيّر كلمات المرور التجريبية قبل النشر.
- استخدم `CI_ENVIRONMENT = production` وHTTPS في بيئة الإنتاج.
- تأكد من أن مجلد `writable` قابل للكتابة، لكنه غير مكشوف مباشرة للزوار.
- لا تعتمد على بيانات الكتالوج لأغراض الملاحة أو التشغيل البحري الحقيقي.

## الترخيص

هذا المشروع مرخّص وفقًا للترخيص الموجود في ملف [LICENSE](LICENSE).
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
