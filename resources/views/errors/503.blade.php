<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance en cours</title>
    <style>
        :root {
            --bg-1: #7c2d12;
            --bg-2: #007a35;
            --text-main: #e8f5e9;
            --text-soft: #c8e6c9;
            --accent: #009A44;
            --card-border: rgba(255, 237, 213, 0.3);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-main);
            background: radial-gradient(circle at 18% 20%, #fb923c 0%, transparent 42%),
                        radial-gradient(circle at 82% 10%, #009A44 0%, transparent 40%),
                        linear-gradient(135deg, var(--bg-1), var(--bg-2));
            padding: 24px;
        }

        .maintenance-card {
            width: min(920px, 100%);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            background: rgba(124, 45, 18, 0.52);
            backdrop-filter: blur(8px);
            padding: clamp(28px, 4vw, 54px);
            box-shadow: 0 24px 70px rgba(67, 20, 7, 0.38);
        }

        .maintenance-badge {
            display: inline-block;
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            color: #7c2d12;
            background: #c8e6c9;
            border: 1px solid #a5d6a7;
            border-radius: 999px;
            padding: 8px 14px;
            margin-bottom: 16px;
        }

        h1 {
            margin: 0 0 14px;
            font-size: clamp(1.9rem, 4.4vw, 3rem);
            line-height: 1.18;
            font-weight: 800;
        }

        .message {
            margin: 0;
            font-size: clamp(1.05rem, 2vw, 1.35rem);
            line-height: 1.65;
            color: var(--text-soft);
            max-width: 64ch;
        }

        .accent {
            color: var(--accent);
            font-weight: 700;
        }

        .btn-home {
            display: inline-block;
            margin-top: 28px;
            padding: 14px 32px;
            background: #c8e6c9;
            color: #7c2d12;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 10px 25px rgba(67, 20, 7, 0.2);
            border: 1px solid #a5d6a7;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            background: #fff;
            box-shadow: 0 15px 35px rgba(67, 20, 7, 0.3);
        }
    </style>
</head>
<body>
    <main class="maintenance-card" role="main" aria-live="polite">
        <span class="maintenance-badge">Maintenance</span>
        <h1>Service temporairement indisponible</h1>
        <p class="message">
            Service non disponible pour cause de <span class="accent">maintenance de l'application</span>.
            Merci de patienter quelques instants.
        </p>
        <a href="/" class="btn-home">Retour à l'accueil</a>
    </main>
</body>
</html>
