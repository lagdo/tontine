Bonjour,
<br/><br/>
Vous avez reçu un feedback @if ($user !== null)de l'utilisateur {{ $user->name }}@endif sur l'application Tontine.
<br/><br/>
Type: {{ $feedback->type }}
<br/><br/>
Message:
<br/><br/>
{{ $feedback->message }}
