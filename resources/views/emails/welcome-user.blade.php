<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur SchoolSoft</title>
</head>
<body>
    <h2>Bienvenue sur SchoolSoft, {{ $user->firstname }} !</h2>

    <p>Votre compte utilisateur a été créé avec succès.</p>

    <p>Voici vos informations de connexion :</p>

    <ul>
        <li><strong>Email :</strong> {{ $user->email }}</li>
        <li><strong>Mot de passe temporaire :</strong>
            {{ $temporaryPassword }}
        </li>
    </ul>

    <p>
        Pour des raisons de sécurité, nous vous recommandons de modifier
        votre mot de passe après votre première connexion.
    </p>

    <p>L'équipe SchoolSoft</p>
</body>
</html>