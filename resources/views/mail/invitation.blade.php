<p>{{__('Hallo')}}</p>

<p>{{__('t.mails.invitation.you_have_been_invited', ['courseName' => $invitation->course->name, 'inviterName' => auth()->user()->name])}}</p>

@php
$invitationLink = new App\Util\HtmlString;
$invitationLink->s('<a href="' . route('invitation.view', ['token' => $invitation->token]) . '">');
$invitationLink->__('t.mails.invitation.here');
$invitationLink->s('</a>');
@endphp
<p>{{__('t.mails.invitation.accept', ['here' => $invitationLink])}}</p>

<p>{{__('t.mails.invitation.greeting')}}</p>
