<!DOCTYPE html>
<html>
<head>
    <title>Invitation de colocation</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Bonjour !</h2>
    <p>Vous avez été invité(e) à rejoindre la colocation <strong>{{ $invitation->colocation->name }}</strong>.</p>
    
    <p>Pour voir et accepter l'invitation, cliquez sur le lien ci-dessous :</p>
    
    <a href="{{ route('invitations.show', $invitation->token) }}" style="display: inline-block; padding: 10px 20px; background-color: #2D5A4C; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
        Voir l'invitation
    </a>
</body>
</html>