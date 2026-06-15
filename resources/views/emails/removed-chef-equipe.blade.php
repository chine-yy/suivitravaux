<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Changement de rôle</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #f8f9fc; padding: 20px; border-radius: 8px; border-left: 4px solid #f6c23e;">
        <h2 style="color: #f6c23e; margin-top: 0;">Bonjour {{ $user->name }},</h2>
        
        <p>Nous vous informons qu'il y a eu un changement de direction pour l'équipe <strong>{{ $equipe->nom }}</strong>.</p>
        
        <p>Vous n'êtes désormais plus le chef de cette équipe. Un nouveau chef a été assigné.</p>
        
        <p>Nous vous remercions pour votre gestion passée.</p>
        
        <div style="margin-top: 30px; font-size: 13px; color: #858796;">
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
        </div>
    </div>
    
</body>
</html>
