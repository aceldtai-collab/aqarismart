# 🎨 دليل استخدام الألوان في الموقع

## 📍 وين الألوان بتنعكس؟

### 1. **Primary Color (--brand)**
اللون الأساسي بيستخدم في:

#### في `app.blade.php`:
- ✅ الأزرار الأساسية (`.btn-primary`)
- ✅ الـ gradient backgrounds
- ✅ الـ hover states للأزرار
- ✅ الـ borders عند الـ focus
- ✅ الـ `.brand-bg` class
- ✅ الـ `.brand-text` class
- ✅ الـ `.brand-border` class

#### في `tenant/home.blade.php`:
- ✅ الـ Hero Section background (gradient)
- ✅ الـ Search Promotion banner background (gradient)

#### في `public-layout.blade.php`:
- ✅ الـ `.btn-brand` buttons
- ✅ الـ `.brand-bg` backgrounds
- ✅ الـ `.brand-text` text colors

---

### 2. **Accent Color (--accent)**
اللون الثانوي بيستخدم في:

#### في `app.blade.php`:
- ✅ الـ `.accent-bg` class
- ✅ الـ `.accent-text` class
- ✅ الـ `.accent-border` class

#### في `public-layout.blade.php`:
- ✅ الـ `.btn-accent` buttons
- ✅ الـ `.accent-bg` backgrounds
- ✅ الـ `.accent-text` text colors

---

### 3. **Font Color (--font-color)**
لون النص بيستخدم في:

#### في `public-layout.blade.php`:
- ✅ الـ `body` tag style: `color: var(--font-color)`

---

## 🔧 كيف تتأكد إنه الألوان شغالة؟

### الطريقة 1: فحص الـ Source Code
1. افتح الموقع
2. اضغط `F12` (Developer Tools)
3. اضغط `Ctrl+U` (View Source)
4. دور على `:root{` في الـ `<style>` tag
5. لازم تلاقي:
```css
:root{ 
    --brand: #YOUR_COLOR_HERE; 
    --accent: #YOUR_COLOR_HERE; 
    --font-color: #YOUR_COLOR_HERE;
}
```

### الطريقة 2: فحص الـ Computed Styles
1. افتح الموقع
2. اضغط `F12`
3. اختار أي عنصر بيستخدم اللون (مثلاً زر)
4. شوف الـ Computed tab
5. دور على `background-color` أو `color`
6. لازم يكون نفس اللون اللي حطيته

### الطريقة 3: فحص قاعدة البيانات
شغل الملف `check-settings.php` من المتصفح:
```
http://your-domain.test/check-settings.php
```

---

## ⚠️ مشاكل شائعة وحلولها

### المشكلة 1: الألوان ما بتتغير
**السبب:** Browser Cache
**الحل:**
1. اضغط `Ctrl+Shift+R` (Hard Refresh)
2. أو امسح الـ cache من الـ browser
3. أو افتح الموقع في Incognito Mode

### المشكلة 2: الألوان بتظهر بس مش في كل مكان
**السبب:** بعض العناصر بتستخدم Tailwind classes مباشرة
**الحل:** استخدم الـ utility classes:
- `.brand-bg` بدل `bg-blue-600`
- `.brand-text` بدل `text-blue-600`
- `.accent-bg` بدل `bg-cyan-600`

### المشكلة 3: الألوان مش محفوظة في قاعدة البيانات
**السبب:** مشكلة في الـ validation أو الـ save
**الحل:** 
1. شوف الـ `storage/logs/laravel.log`
2. تأكد إنه ما في errors
3. شغل `check-settings.php` للتأكد

---

## 🎯 أمثلة عملية

### مثال 1: تغيير لون الأزرار
```html
<!-- قبل -->
<button class="bg-blue-600 text-white">Click Me</button>

<!-- بعد (بيستخدم اللون من الإعدادات) -->
<button class="brand-bg text-white">Click Me</button>
```

### مثال 2: تغيير لون النص
```html
<!-- قبل -->
<h1 class="text-blue-600">Title</h1>

<!-- بعد -->
<h1 class="brand-text">Title</h1>
```

### مثال 3: استخدام CSS Variables مباشرة
```html
<div style="background: var(--brand); color: white;">
    Custom Element
</div>
```

---

## 📊 ملخص الاستخدام

| اللون | CSS Variable | Utility Classes | الاستخدام |
|-------|--------------|-----------------|-----------|
| Primary | `--brand` | `.brand-bg`, `.brand-text`, `.brand-border` | الأزرار، الـ gradients، الـ hero sections |
| Accent | `--accent` | `.accent-bg`, `.accent-text`, `.accent-border` | الأزرار الثانوية، الـ highlights |
| Font | `--font-color` | - | لون النص الأساسي في الـ body |

---

## 🚀 نصائح للتطوير

1. **استخدم الـ utility classes** بدل الألوان المباشرة
2. **امسح الـ cache** بعد كل تعديل
3. **استخدم Incognito Mode** للتجربة
4. **فحص الـ Source Code** للتأكد من تطبيق الألوان
5. **استخدم الـ Developer Tools** لفحص الـ CSS Variables

---

## 📝 ملاحظات مهمة

- الألوان بتنحفظ في `tenants.settings` (JSON column)
- الألوان بتتطبق عبر CSS Variables في الـ `<style>` tag
- لازم تعمل Hard Refresh بعد التعديل
- بعض الصفحات بتستخدم ألوان ثابتة (sales-flow, sales-story)
