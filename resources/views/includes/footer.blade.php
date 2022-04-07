<footer class="page-footer text-center">
    <p class="footer-company-name">{{__('t.footer.slogan')}}
        @if(env('APP_CONTACT_LINK') !== null && env('APP_CONTACT_LINK') !== "")
        - <a href="{{env('APP_CONTACT_LINK')}}">{{env('APP_CONTACT_TEXT', __('t.footer.contact_text'))}}</a>
        @endif
    </p>
</footer>
