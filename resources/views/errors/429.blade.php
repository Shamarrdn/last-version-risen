<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم تجاوز عدد المحاولات المسموحة</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            background-color: #f7fafc;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="text-red-500 text-5xl font-bold mb-4">429</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">تم تجاوز عدد المحاولات المسموحة</h1>
            <p class="text-gray-600 mb-6">{{ $exception->getMessage() ?: 'لقد تجاوزت الحد المسموح من المحاولات. الرجاء المحاولة لاحقاً.' }}</p>
            <div class="mt-6">
                <a href="/" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all">العودة للصفحة الرئيسية</a>
            </div>
        </div>
    </div>
</body>

</html>