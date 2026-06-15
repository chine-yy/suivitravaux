<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #FF6B35 0%, #e85a2a 100%);
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .email-header .subtitle {
            color: #ffffff;
            opacity: 0.9;
            font-size: 14px;
            margin-top: 5px;
        }
        .email-body {
            padding: 30px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #FF6B35;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-item:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #333333;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            color: #555555;
        }
        .message-box {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .message-label {
            font-weight: bold;
            color: #333333;
            margin-bottom: 10px;
            display: block;
        }
        .message-content {
            color: #555555;
            white-space: pre-wrap;
            line-height: 1.8;
        }
        .email-footer {
            background-color: #1a1a1a;
            padding: 20px;
            text-align: center;
        }
        .email-footer p {
            color: #999999;
            font-size: 12px;
            margin: 0;
        }
        .email-footer .brand {
            color: #FF6B35;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>📬 Nouveau message</h1>
                <p class="subtitle">Formulaire de contact - {{ config('app.name') }}</p>
            </div>
            
            <div class="email-body">
                <div class="info-card">
                    <div class="info-item">
                        <span class="info-label">👤 Nom :</span>
                        <span class="info-value">{{ $name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">✉️ Email :</span>
                        <span class="info-value">{{ $email }}</span>
                    </div>
                </div>
                
                <div class="message-box">
                    <span class="message-label">💬 Message :</span>
                    <div class="message-content">{!! nl2br(e($message)) !!}</div>
                </div>
            </div>
            
            <div class="email-footer">
                <p class="brand">{{ config('app.name') }} - Solution de gestion de chantier BTP</p>
                <p>Cet email a été envoyé depuis le formulaire de contact du site</p>
            </div>
        </div>
    </div>
</body>
</html>

