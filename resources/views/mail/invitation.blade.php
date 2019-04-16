<p>{{__('Hallo')}}</p>

<p>{{__('Du wurdest in den Kurs ":kursname" eingeladen.', ['kursname' => $einladung->kurs->name])}}</p>

<a href="{{ route('invitation.view', ['token' => $einladung->token]) }}">{{__('Klicke hier')}}</a> {{__('um die Einladung anzunehmen.')}}
