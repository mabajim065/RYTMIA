<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Gimnasta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
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
    body { font-family: 'DM Sans', sans-serif; background-color: var(--off-white); color: var(--text); display: flex; min-height: 100vh; }
    .view { display: none; }
    .view.active { display: block; }
    /* === SIDEBAR === */
    .sidebar { width: 280px; background-color: var(--white); border-right: 1px solid var(--blush); display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 10; }
    .brand { padding: 2rem; font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 600; color: var(--burgundy); text-align: center; border-bottom: 1px solid var(--blush); }
    .nav-links { flex: 1; padding: 2rem 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .nav-link { padding: 1rem 1.5rem; border-radius: var(--radius-md); color: var(--muted); text-decoration: none; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 1rem; cursor: pointer; }
    .nav-link:hover, .nav-link.active { background-color: var(--cream); color: var(--burgundy); }
    .user-profile { padding: 2rem; border-top: 1px solid var(--blush); display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 40px; height: 40px; background: linear-gradient(135deg, var(--rose), var(--blush)); color: var(--burgundy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; }
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
      background: linear-gradient(135deg, var(--rose) 0%, var(--blush) 100%);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      color: var(--burgundy);
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
    }
    .welcome-card::after { content: '🤸‍♀️'; position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); font-size: 5rem; opacity: 0.2; }
    .welcome-title { font-family: 'Cormorant Garamond', serif; font-size: 2rem; margin-bottom: 0.5rem; }
    .welcome-sub { opacity: 0.8; font-size: 0.95rem; }
    /* === STATS === */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--white); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .stat-num { font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: var(--burgundy); }
    .stat-desc { color: var(--muted); font-size: 0.85rem; margin-top: 0.25rem; }
    /* === PERFIL === */
    .perfil-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .perfil-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.5rem; margin-bottom: 1.5rem; }
    .perfil-row { display: flex; gap: 0.75rem; align-items: baseline; padding: 0.75rem 0; border-bottom: 1px solid var(--blush); }
    .perfil-row:last-child { border-bottom: none; }
    .perfil-label { color: var(--muted); font-size: 0.85rem; min-width: 160px; }
    .perfil-value { color: var(--text); font-weight: 500; }
    .badge { display: inline-block; padding: 0.2rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-activa { background-color: #e8f5e9; color: #2e7d32; }
    .badge-inactiva { background-color: #fff8e1; color: #f57f17; }
    .badge-baja { background-color: #ffebee; color: #c62828; }
    @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 1.5rem; } }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="brand">Rytmia.</div>
    <nav class="nav-links">
      <a class="nav-link active" id="nav-panel" onclick="showView('panel')">🏠 Mi Panel</a>
      <a class="nav-link" id="nav-calendario" onclick="showView('calendario')">📅 Calendario</a>
    </nav>
    <div class="user-profile">
      <div class="avatar" id="sidebarAvatar">G</div>
      <div class="user-info">
        <div class="user-name" id="sidebarName">Gimnasta</div>
        <div class="user-role">Gimnasta</div>
      </div>
      <button class="logout-btn" onclick="logout()" title="Cerrar sesión">🚪</button>
    </div>
  </aside>

  <main class="main-content">
    <div id="view-panel" class="view active">
      <header class="header">
        <h1 class="page-title">Mi Panel</h1>
        <p class="page-subtitle">Bienvenida, <span id="nombreBienvenida">gimnasta</span></p>
      </header>

      <div class="welcome-card">
        <div class="welcome-title">¡Hola, <span id="welcomeName">gimnasta</span>! 🎀</div>
        <div class="welcome-sub">Consulta aquí tu información personal y la de tu conjunto.</div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-num" id="statAnios">–</div>
          <div class="stat-desc">Años en el club</div>
        </div>
        <div class="stat-card">
          <div class="stat-num" id="statCategoria">–</div>
          <div class="stat-desc">Categoría</div>
        </div>
      </div>

      <div class="perfil-card">
        <div class="perfil-title">Mi Ficha</div>
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
          <span class="perfil-label">Teléfono usuario</span>
          <span class="perfil-value" id="perfilTelefono">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Teléfono contacto</span>
          <span class="perfil-value" id="perfilContacto">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Nº de licencia</span>
          <span class="perfil-value" id="perfilLicencia">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Fecha de nacimiento</span>
          <span class="perfil-value" id="perfilFecha">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Conjunto</span>
          <span class="perfil-value" id="perfilConjunto">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Club</span>
          <span class="perfil-value" id="perfilClub">–</span>
        </div>
        <div class="perfil-row">
          <span class="perfil-label">Estado</span>
          <span class="perfil-value" id="perfilEstado">–</span>
        </div>
      </div>
    </div>
    
    <div id="view-calendario" class="view">
      <header class="header">
        <h1 class="page-title">Calendario</h1>
        <p class="page-subtitle">Competiciones de mi conjunto</p>
      </header>
      <div class="perfil-card" style="padding: 1rem;">
        <div id="calendar"></div>
      </div>
    </div>
  </main>

<script>
  const API   = '/api';
  const token = localStorage.getItem('rytmia_token');
  const user  = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

  // Redirigir si no hay sesión o no es gimnasta
  if (!token || user.rol !== 'gimnasta') {
    window.location.href = '/';
  }

  // UI inmediata
  const nombreCompleto = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('sidebarName').textContent      = nombreCompleto;
  document.getElementById('sidebarAvatar').textContent    = (user.nombre?.[0] ?? 'G').toUpperCase();
  document.getElementById('welcomeName').textContent       = user.nombre ?? 'gimnasta';
  document.getElementById('nombreBienvenida').textContent  = user.nombre ?? 'gimnasta';
  document.getElementById('perfilNombre').textContent      = nombreCompleto;
  document.getElementById('perfilDni').textContent         = user.dni ?? '–';
  document.getElementById('perfilEmail').textContent       = user.email ?? '–';
  document.getElementById('perfilTelefono').textContent    = user.telefono ?? '–';

  // Perfil completo desde API
  fetch(`${API}/me`, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    const g = data.gimnasta;
    if (g) {
      document.getElementById('statAnios').textContent     = g.anios_en_club ?? 0;
      document.getElementById('statCategoria').textContent = g.categoria?.nombre ?? '–';
      document.getElementById('perfilLicencia').textContent= g.numero_licencia ?? '–';
      document.getElementById('perfilFecha').textContent   = g.fecha_nacimiento
        ? new Date(g.fecha_nacimiento).toLocaleDateString('es-ES') : '–';
      document.getElementById('perfilContacto').textContent= g.telefono_contacto ?? '–';
      document.getElementById('perfilConjunto').textContent= g.conjunto?.nombre ?? '–';
      document.getElementById('perfilClub').textContent    = g.club?.nombre ?? '–';

      const estadoEl = document.getElementById('perfilEstado');
      estadoEl.innerHTML = `<span class="badge badge-${g.estado ?? 'activa'}">${g.estado ?? '–'}</span>`;
    }
  })
  .catch(() => {});
  
  function showView(name) {
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById('view-' + name).classList.add('active');
    document.getElementById('nav-' + name).classList.add('active');
    
    if (name === 'calendario') initCalendar();
  }

  let calendarInstance = null;
  function initCalendar() {
    if (calendarInstance) return;
    const calendarEl = document.getElementById('calendar');
    fetch(`${API}/competiciones`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
      const events = data.map(c => ({
        title: c.nombre + ' (' + c.tipo + ')',
        start: c.fecha,
        color: c.estado === 'pendiente' ? 'var(--muted)' : 'var(--burgundy)'
      }));
      calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: events
      });
      calendarInstance.render();
    }).catch(() => {
      calendarEl.innerHTML = '<p style="color:red">Error cargando calendario.</p>';
    });
  }

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
