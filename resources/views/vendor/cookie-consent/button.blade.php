<form action="{!! $url !!}" {!! $attributes !!}>
    @csrf
    <button type="submit" class="{!! $basename !!}__link" style="background: black;border:none;">
        <span class="{!! $basename !!}__label">{{ $label }}</span>
    </button>
</form>
