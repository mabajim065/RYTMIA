<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Entrenadora</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --burgundy: #6B1A3A;
      --rose:     #C45C7E;
      --blush:    #F2D5DF;
      --cream:    #FDF6F0;
      --off-white: #FAF6F1;
      --text:     #2A1520;
      --muted:    #9B7080;
      --white:    #ffffff;
      --shadow-soft: 0 10px 30px rgba(107,26,58,.08);
      --radius-lg: 20px;
      --radius-md: 14px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'DM Sans', sans-serif;
      background-color: var(--off-white);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }
    /* === SIDEBAR === */
    .sidebar {
      width: 280px;
      background-color: var(--white);
      border-right: 1px solid var(--blush);
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
      z-index: 10;
    }
    .brand { padding: 2rem; font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 600; color: var(--burgundy); text-align: center; border-bottom: 1px solid var(--blush); }
    .nav-links { flex: 1; padding: 2rem 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .nav-link { padding: 1rem 1.5rem; border-radius: var(--radius-md); color: var(--muted); text-decoration: none; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 1rem; cursor: pointer; }
    .nav-link:hover, .nav-link.active { background-color: var(--cream); color: var(--burgundy); }
    .user-profile { padding: 2rem; border-top: 1px solid var(--blush); display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 40px; height: 40px; background: linear-gradient(135deg, var(--burgundy), var(--rose)); color: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; }
    .user-info { flex: 1; }
    .user-name { font-weight: 600; font-size: 0.9rem; }
    .user-role { font-size: 0.8rem; color: var(--muted); }
    .logout-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 1.2rem; transition: color 0.3s; }
    .logout-btn:hover { color: #D94F4F; }
    /* === MAIN === */
    .main-content { flex: 1; margin-left: 280px; padding: 3rem; }
    .header { margin-bottom: 3rem; }
    .page-title { font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: var(--burgundy); }
    .page-subtitle { color: var(--muted); margin-top: 0.5rem; }
    /* === WELCOME CARD === */
    .welcome-card {
      background: linear-gradient(135deg, var(--burgundy) 0%, var(--rose) 100%);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      color: #fff;
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
    }
    .welcome-card::after {
      content: '🎀';
      position: absolute;
      right: 2rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 5rem;
      opacity: 0.15;
    }
    .welcome-title { font-family: 'Cormorant Garamond', serif; font-size: 2rem; margin-bottom: 0.5rem; }
    .welcome-sub { opacity: 0.85; font-size: 0.95rem; }
    /* === STATS === */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--white); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .stat-num { font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: var(--burgundy); }
    .stat-desc { color: var(--muted); font-size: 0.85rem; margin-top: 0.25rem; }
    /* === INFO PERFIL === */
    .perfil-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .perfil-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.5rem; margin-bottom: 1.5rem; }
    .perfil-row { display: flex; gap: 0.75rem; align-items: baseline; padding: 0.75rem 0; border-bottom: 1px solid var(--blush); }
    .perfil-row:last-child { border-bottom: none; }
    .perfil-label { color: var(--muted); font-size: 0.85rem; min-width: 160px; }
    .perfil-value { color: var(--text); font-weight: 500; }
    /* === ACCESS DENIED === */
    .access-denied { text-align: center; padding: 5rem 2rem; }
    .access-denied h2 { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 2rem; margin-bottom: 1rem; }
    .access-denied p { color: var(--muted); }
    /* === RESPONSIVE === */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .main-content { margin-left: 0; padding: 1.5rem; }
    }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="brand">Rytmia.</div>
    <nav class="nav-links">
      <a class="nav-link active">🏠 Mi Panel</a>
      <a class="nav-link" href="/dashboard/admin">👥 Equipo Técnico</a>
    </nav>
    <div class="user-profile">
      <div class="avatar" id="sidebarAvatar">E</div>
      <div class="user-info">
        <div class="user-name" id="sidebarName">Entrenadora</div>
        <div class="user-role">Entrenadora</div>
      </div>
      <button class="logout-btn" onclick="logout()" title="Cerrar sesión">🚪</button>
    </div>
  </aside>

  <main class="main-content">
    <header class="header">
      <h1 class="page-title">Mi Panel</h1>
      <p class="page-subtitle">Bienvenida de vuelta, <span id="nombreBienvenida">entrenadora</span></p>
    </header>

    <div class="welcome-card">
      <div class="welcome-title">¡Hola, <span id="welcomeName">entrenadora</span>! 👋</div>
      <div class="welcome-sub">Aquí puedes consultar tu información de perfil y acceder a los recursos del club.</div>
    </div>

    <div class="stats-grid" id="statsGrid">
      <div class="stat-card">
        <div class="stat-num" id="statAnios">–</div>
        <div class="stat-desc">Años de experiencia</div>
      </div>
      <div class="stat-card">
        <div class="stat-num" id="statHoras">–</div>
        <div class="stat-desc">Horas semanales</div>
      </div>
    </div>

    <div class="perfil-card">
      <div class="perfil-title">Mi Perfil</div>
      <div class="perfil-row">
        <span class="perfil-label">Nombre completo</span>
        <span class="perfil-value" id="perfilNombre">–</span>
      </div>
      <div class="perfil-row">
        <span class="perfil-label">DNI</span>
        <span class="perfil-value" id="perfilDni">–</span>
      </div>
      <div class="perfil-row">
        <span class="perfil-label">Email</span>
        <span class="perfil-value" id="perfilEmail">–</span>
      </div>
      <div class="perfil-row">
        <span class="perfil-label">Teléfono</span>
        <span class="perfil-value" id="perfilTelefono">–</span>
      </div>
      <div class="perfil-row">
        <span class="perfil-label">Titulación</span>
        <span class="perfil-value" id="perfilTitulacion">–</span>
      </div>
      <div class="perfil-row">
        <span class="perfil-label">Biografía</span>
        <span class="perfil-value" id="perfilBiografia" style="font-style:italic; color: var(--muted);">Sin información</span>
      </div>
    </div>
  </main>

<script>
  const API = '/api';
  const token = localStorage.getItem('rytmia_token');
  const user  = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

  // Redirigir si no hay sesión o no es entrenadora
  if (!token || user.rol !== 'entrenadora') {
    window.location.href = '/';
  }

  // UI con datos de localStorage (inmediato)
  document.getElementById('sidebarName').textContent = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('sidebarAvatar').textContent = (user.nombre?.[0] ?? 'E').toUpperCase();
  document.getElementById('welcomeName').textContent = user.nombre ?? 'entrenadora';
  document.getElementById('nombreBienvenida').textContent = user.nombre ?? 'entrenadora';
  document.getElementById('perfilNombre').textContent = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('perfilDni').textContent    = user.dni ?? '–';
  document.getElementById('perfilEmail').textContent  = user.email ?? '–';
  document.getElementById('perfilTelefono').textContent = user.telefono ?? '–';

  // Obtener perfil completo desde API
  fetch(`${API}/me`, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.entrenador) {
      document.getElementById('statAnios').textContent  = data.entrenador.anios_experiencia ?? 0;
      document.getElementById('statHoras').textContent  = data.entrenador.horas_semanales ?? 0;
      document.getElementById('perfilTitulacion').textContent = data.entrenador.titulacion ?? '–';
      if (data.entrenador.biografia) {
        document.getElementById('perfilBiografia').textContent = data.entrenador.biografia;
        document.getElementById('perfilBiografia').style.fontStyle = 'normal';
        document.getElementById('perfilBiografia').style.color = 'var(--text)';
      }
    }
  })
  .catch(() => {});

  function logout() {
    fetch(`${API}/logout`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    }).finally(() => {
      localStorage.removeItem('rytmia_token');
      localStorage.removeItem('rytmia_user');
      window.location.href = '/';
    });
  }
</script>

</body>
</html>
