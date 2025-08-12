<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة جديدة من {{ $name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1E2A38;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #000000, #333333);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .message-box {
            background-color: rgba(0, 0, 0, 0.05);
            border-right: 4px solid #000000;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.8;
        }
        .sender-info {
            background-color: rgba(255, 255, 255, 0.75);
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .sender-info p {
            margin: 8px 0;
            color: #2C3E50;
        }
        .sender-info strong {
            color: #000000;
            margin-left: 10px;
        }
        .divider {
            height: 1px;
            background-color: rgba(0, 0, 0, 0.25);
            margin: 15px 0;
        }
        .footer {
            background-color: rgba(0, 0, 0, 0.05);
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #2C3E50;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>رسالة جديدة من {{ $name }}</h1>
        </div>

        <div class="email-body">
            @if($subject)
                <div class="message-box">
                    <strong>الموضوع:</strong> {{ $subject }}
                </div>
            @endif

            <div class="message-box">
                {{ $userMessage }}
            </div>

            <div class="divider"></div>

            <div class="sender-info">
                <p><strong>المرسل:</strong> {{ $name }}</p>
                <p><strong>البريد الإلكتروني:</strong> {{ $email }}</p>
                @if($phone)
                    <p><strong>رقم الهاتف:</strong> {{ $phone }}</p>
                @endif
                @if($subject)
                    <p><strong>الموضوع:</strong> {{ $subject }}</p>
                @endif
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} RISEN - جميع الحقوق محفوظة
        </div>
    </div>
</body>
</html>
