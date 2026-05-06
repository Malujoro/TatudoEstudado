<form method="POST" action="/reset-password">
    @csrf

    <input type="hidden" name="token" value="{{ request('token') }}">
    <input type="hidden" name="email" value="{{ request('email') }}">

    <input type="password" name="password" placeholder="Nova senha" required>
    <input type="password" name="password_confirmation" placeholder="Confirmar senha" required>

    <button type="submit">Redefinir senha</button>
</form>