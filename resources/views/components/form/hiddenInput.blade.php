<input
  type="hidden"
  id="{{ Str::kebab($id ?? $name) }}"
  name="{{ $name }}"
  value="{{ $value }}" />
