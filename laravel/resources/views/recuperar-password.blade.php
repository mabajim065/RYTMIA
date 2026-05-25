<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Restablecer contraseña</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    :root {
      --burgundy:   #6B1A3A;
      --rose:       #C45C7E;
      --blush:      #F2D5DF;
      --cream:      #FDF6F0;
      --text:       #2A1520;
      --muted:      #9B7080;
      --error:      #D94F4F;
      --success:    #2e7d32;
      --radius:     14px;
      --shadow:     0 8px 40px rgba(107,26,58,.13);
      --card-bg:    #ffffff;
      --input-bg:   var(--cream);
      --input-focus:#ffffff;
    }

    @media (prefers-color-scheme: dark) {
      :root {
        --burgundy:   #EFA6C0;
        --rose:       #D87D9C;
        --blush:      #4A2B38;
        --cream:      #1F1318;
        --text:       #FDF6F0;
        --muted:      #A88894;
        --error:      #EF6E6E;
        --shadow:     0 8px 40px rgba(0,0,0,0.5);
        --card-bg:    #1E1216;
        --input-bg:   #140C10;
        --input-focus:#2A1A21;
      }
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--cream);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      background-image:
        radial-gradient(ellipse 80% 60% at 70% 10%, rgba(196,92,126,.12) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 10% 90%, rgba(107,26,58,.08) 0%, transparent 55%);
    }

    .card {
      background: var(--card-bg);
      border-radius: 24px;
      box-shadow: var(--shadow);
      width: 100%;
      max-width: 420px;
      padding: 2.8rem 2.5rem 2.5rem;
      animation: fadeUp .5s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .logo-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: .4rem;
      margin-bottom: 2rem;
    }

    .logo-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--burgundy);
      letter-spacing: -.01em;
    }

    .logo-sub {
      font-size: .78rem;
      color: var(--muted);
      letter-spacing: .04em;
      text-transform: uppercase;
      text-align: center;
    }

    .field {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      font-size: .8rem;
      font-weight: 500;
      color: var(--text);
      margin-bottom: .45rem;
      letter-spacing: .01em;
    }

    input {
      width: 100%;
      padding: .75rem 1rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius);
      font-family: 'DM Sans', sans-serif;
      font-size: .95rem;
      color: var(--text);
      background: var(--input-bg);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }

    input:focus {
      border-color: var(--rose);
      background: var(--input-focus);
      box-shadow: 0 0 0 3px rgba(196,92,126,.15);
    }

    input.error-input {
      border-color: var(--error);
      box-shadow: 0 0 0 3px rgba(217,79,79,.12);
    }

    .error-msg {
      display: none;
      font-size: .8rem;
      color: var(--error);
      margin-top: .35rem;
    }

    .error-msg.visible { display: block; }

    .alert {
      display: none;
      background: #FEF0F0;
      border: 1px solid rgba(217,79,79,.3);
      border-radius: 10px;
      padding: .75rem 1rem;
      font-size: .85rem;
      color: var(--error);
      margin-bottom: 1.2rem;
      animation: shake .35s ease;
    }

    .alert-success {
      background: #e8f5e9;
      color: var(--success);
      border: 1px solid #c8e6c9;
    }

    .alert.visible { display: block; }

    @keyframes shake {
      0%,100% { transform: translateX(0); }
      25%      { transform: translateX(-6px); }
      75%      { transform: translateX(6px); }
    }

    .btn-login {
      width: 100%;
      padding: .85rem;
      background: linear-gradient(135deg, var(--burgundy) 0%, var(--rose) 100%);
      color: #fff;
      font-family: 'DM Sans', sans-serif;
      font-size: .95rem;
      font-weight: 500;
      letter-spacing: .02em;
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: opacity .2s, transform .15s;
      margin-top: .4rem;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-login:hover  { opacity: .9; transform: translateY(-1px); }
    .btn-login:active { transform: translateY(0); }
    .btn-login:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    .spinner {
      display: none;
      width: 18px; height: 18px;
      border: 2.5px solid rgba(255,255,255,.4);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .7s linear infinite;
      position: absolute;
      right: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
    }

    .btn-login.loading .spinner { display: block; }
    .btn-login.loading .btn-text { opacity: 0; }

    @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

    .card-footer {
      text-align: center;
      margin-top: 1.6rem;
      font-size: .8rem;
      color: var(--muted);
    }

    .card-footer a {
      color: var(--burgundy);
      text-decoration: none;
      font-weight: 500;
    }
    .card-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="card" role="main">

  <!-- Logo -->
  <div class="logo-wrap">
    <img src="/logo.jpg" alt="Rytmia Logo" class="logo-img" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; box-shadow: 0 4px 12px rgba(107,26,58,0.15); margin-bottom: 0.5rem;">
    <span class="logo-name">Rytmia</span>
    <span class="logo-sub">Establece tu nueva contraseña</span>
  </div>

  <div id="alert" class="alert" role="alert" aria-live="polite"></div>

  <!-- Formulario de cambio -->
  <form id="resetForm" novalidate>
    <input type="hidden" id="token" value="{{ $token }}" />
    <input type="hidden" id="email" value="{{ $email }}" />

    <!-- Nueva Contraseña -->
    <div class="field">
      <label for="password">Nueva contraseña</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Mín. 8 caracteres"
        required
      />
      <span class="error-msg" id="password-error">La contraseña debe tener al menos 8 caracteres.</span>
    </div>

    <!-- Confirmar Nueva Contraseña -->
    <div class="field">
      <label for="password_confirmation">Confirmar nueva contraseña</label>
      <input
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        placeholder="Repite la contraseña"
        required
      />
      <span class="error-msg" id="password_confirmation-error">Las contraseñas no coinciden.</span>
    </div>

    <!-- Botón -->
    <button type="submit" class="btn-login" id="btnSubmit">
      <span class="btn-text">Guardar contraseña</span>
      <span class="spinner" aria-hidden="true"></span>
    </button>
  </form>

  <div class="card-footer">
    <a href="/">Volver a Iniciar Sesión</a>
  </div>

</div>

<script>
  const $ = id => document.getElementById(id);

  function setError(inputEl, msgEl, show) {
    inputEl.classList.toggle('error-input', show);
    msgEl.classList.toggle('visible', show);
  }

  function showAlert(msg, type = 'error') {
    const el = $('alert');
    el.textContent = msg;
    el.className = 'alert alert-' + type + ' visible';
  }

  function hideAlert() {
    $('alert').className = 'alert';
    $('alert').classList.remove('visible');
  }

  $('resetForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    const token = $('token').value;
    const email = $('email').value;
    const password = $('password').value;
    const passwordConfirm = $('password_confirmation').value;

    let valid = true;
    if (!password || password.length < 8) {
      setError($('password'), $('password-error'), true);
      valid = false;
    } else {
      setError($('password'), $('password-error'), false);
    }

    if (password !== passwordConfirm) {
      setError($('password_confirmation'), $('password_confirmation-error'), true);
      valid = false;
    } else {
      setError($('password_confirmation'), $('password_confirmation-error'), false);
    }

    if (!valid) return;

    const btn = $('btnSubmit');
    btn.disabled = true;
    btn.classList.add('loading');

    try {
      const res = await fetch('/api/password/reset', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          token,
          email,
          password,
          password_confirmation: passwordConfirm
        })
      });

      const data = await res.json();

      if (!res.ok) {
        const msg = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message ?? 'No se pudo restablecer la contraseña.');
        showAlert(msg, 'error');
        return;
      }

      showAlert(data.message ?? 'Contraseña actualizada correctamente.', 'success');
      $('resetForm').style.display = 'none';

    } catch (err) {
      showAlert('Error de conexión con el servidor. Inténtalo más tarde.', 'error');
    } finally {
      btn.disabled = false;
      btn.classList.remove('loading');
    }
  });
</script>

</body>
</html>
