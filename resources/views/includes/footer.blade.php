<footer class="page-footer text-center">
    <p class="footer-company-name">{{__('t.footer.slogan')}}
        @if(config('app.contact.link') !== null && config('app.contact.link') !== "")
        - <a href="{{config('app.contact.link')}}">{{config('app.contact.text') ?? __('t.footer.contact_text')}}</a>
        @endif
    </p>
</footer>
