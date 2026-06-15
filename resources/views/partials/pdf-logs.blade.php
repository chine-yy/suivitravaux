<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logs Système - CNRST</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.2;
            font-size: 9px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #009A44;
            padding-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #009A44;
            margin-bottom: 3px;
        }
        .title {
            font-size: 14px;
            text-transform: uppercase;
            color: #009A44;
            margin-bottom: 5px;
        }
        .meta {
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
        }
        pre {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Courier New', Courier, monospace;
            color: #1e293b;
        }
        .log-entry {
            margin-bottom: 2px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div class="title">Logs Système - Laravel</div>
        <div class="meta">Généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name }}</div>
    </div>

    <div class="content">
        <pre>{{ $logs }}</pre>
    </div>

    <div class="footer">
        Logs Système - CNRST SUIVI TRAVAUX - &copy; {{ date('Y') }} CNRST - Document confidentiel
    </div>
</body>
</html>
