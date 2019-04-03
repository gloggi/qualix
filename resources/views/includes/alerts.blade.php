@if(Session::has('alert-primary'))
    @component('components.alert', ['type' => 'primary', 'message' => Session::get('alert-primary')])@endcomponent
@endif
@if(Session::has('alert-success'))
    @component('components.alert', ['type' => 'success', 'message' => Session::get('alert-success')])@endcomponent
@endif
@if(Session::has('alert-danger'))
    @component('components.alert', ['type' => 'danger', 'message' => Session::get('alert-danger')])@endcomponent
@endif
@if(Session::has('alert-warning'))
    @component('components.alert', ['type' => 'warning', 'message' => Session::get('alert-warning')])@endcomponent
@endif
