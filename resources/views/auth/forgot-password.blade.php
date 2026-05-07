<form method="POST" action="/forgot-password">
    @csrf

    <input type="email" name="email" placeholder="Digite seu email" required>

    <button type="submit">Enviar link</button>
</form>