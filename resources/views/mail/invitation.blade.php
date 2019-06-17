<p>{{__('Hallo')}}</p>

<p>{{__('Du wurdest in den Kurs ":coursename" eingeladen.', ['coursename' => $invitation->course->name])}}</p>

<a href="{{ route('invitation.view', ['token' => $invitation->token]) }}">{{__('Klicke hier')}}</a> {{__('um die Einladung anzunehmen.')}}
