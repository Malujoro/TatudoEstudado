@props(['url'])
<tr>
<td class="header" style="text-align: center;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none; text-align: center;">

<!-- Imagem da Logo -->
<!-- <img src="{{ asset('assets/logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo" style="height: auto; width: 80px; max-width: 100%; display: block; margin: 0 auto 10px auto;"> -->

<img src="{{ asset('https://raw.githubusercontent.com/Malujoro/TatudoEstudado/refs/heads/main/public/assets/logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo" style="height: auto; width: 80px; max-width: 100%; display: block; margin: 0 auto 10px auto;">
<!-- Nome do App (Texto abaixo da logo) -->
    <span style="color: #9C7DC0; font-size: 22px; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
        {!! $slot !!}
    </span>

</a>
</td>
</tr>
