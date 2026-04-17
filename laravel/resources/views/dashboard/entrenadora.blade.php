<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Entrenadora</title>
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
      --error:    #D94F4F;
      --success:  #2e7d32;
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
    #app { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }
    .view { display: none; }
    .view.active { display: block; }

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
    .nav-link { padding: 1rem 1.5rem; border-radius: var(--radius-md); color: var(--muted); text-decoration: none; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 1rem; cursor: pointer; border: none; background: none; font-size: 1rem; width: 100%; text-align: left; }
    .nav-link:hover, .nav-link.active { background-color: var(--cream); color: var(--burgundy); }
    .user-profile { padding: 2rem; border-top: 1px solid var(--blush); display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 40px; height: 40px; background: linear-gradient(135deg, var(--burgundy), var(--rose)); color: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; flex-shrink: 0; }
    .user-info { flex: 1; min-width: 0; }
    .user-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-role { font-size: 0.8rem; color: var(--muted); }
    .logout-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 1.2rem; transition: color 0.3s; }
    .logout-btn:hover { color: var(--error); }

    /* === MAIN === */
    .main-content { flex: 1; margin-left: 280px; padding: 3rem; }
    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; }
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

    /* === CARDS & GRIDS === */
    .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
    .team-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); display: flex; flex-direction: column; align-items: center; text-align: center; transition: all 0.3s; }
    .team-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(107,26,58,.12); }
    .card-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, var(--cream), var(--blush)); color: var(--burgundy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; border: 2px solid var(--blush); font-weight: 600; font-family: 'Cormorant Garamond', serif; }
    .card-name { font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--text); margin-bottom: 0.3rem; }
    .card-role { font-size: 0.85rem; color: var(--rose); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; }
    .card-stats { display: flex; gap: 2rem; margin-bottom: 1.5rem; justify-content: center; }
    .stat { display: flex; flex-direction: column; align-items: center; }
    .stat-val { font-weight: 700; color: var(--burgundy); font-size: 1.3rem; font-family: 'Cormorant Garamond', serif; }
    .stat-label { font-size: 0.75rem; color: var(--muted); }
    .btn-outline { background: transparent; border: 1.5px solid var(--blush); color: var(--text); padding: 0.6rem 1.2rem; border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; width: 100%; }
    .btn-outline:hover { border-color: var(--burgundy); color: var(--burgundy); background-color: var(--cream); }

    /* === INFO PERFIL === */
    .perfil-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .perfil-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.5rem; margin-bottom: 1.5rem; }
    .perfil-row { display: flex; gap: 0.75rem; align-items: baseline; padding: 0.75rem 0; border-bottom: 1px solid var(--blush); }
    .perfil-row:last-child { border-bottom: none; }
    .perfil-label { color: var(--muted); font-size: 0.85rem; min-width: 160px; }
    .perfil-value { color: var(--text); font-weight: 500; }

    /* === TABLE === */
    .table-wrap { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--blush); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: var(--cream); }
    th { padding: 1rem 1.5rem; text-align: left; font-size: 0.8rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 1rem 1.5rem; font-size: 0.9rem; border-top: 1px solid var(--blush); }
    tr:hover td { background: var(--off-white); }

    /* === MODAL === */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(42,21,32,.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-content { background: var(--white); width: 90%; max-width: 700px; border-radius: var(--radius-lg); padding: 2.5rem; position: relative; transform: translateY(20px); transition: transform 0.3s; box-shadow: 0 20px 60px rgba(0,0,0,.15); max-height: 90vh; overflow-y: auto; }
    .modal-overlay.open .modal-content { transform: translateY(0); }
    .modal-close-btn { width: 100%; padding: 0.8rem; background: var(--off-white); border: 1px solid var(--blush); border-radius: var(--radius-md); color: var(--muted); cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif; margin-top: 1.5rem; }
    .modal-close-btn:hover { background: var(--blush); color: var(--burgundy); }

    /* === LOADING === */
    .loading-state { text-align: center; padding: 4rem 2rem; color: var(--muted); grid-column: 1 / -1; }
    .loading-spinner { width: 40px; height: 40px; border: 3px solid var(--blush); border-top-color: var(--rose); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1rem; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* === MOBILE NAV === */
    .mobile-nav { display: none; padding: 1rem 1.5rem; background: var(--white); border-bottom: 1px solid var(--blush); align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 20; }
    .mobile-nav .brand { padding: 0; border: none; font-size: 1.8rem; text-align: left; }
    .menu-btn { background: none; border: none; font-size: 1.8rem; color: var(--burgundy); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; }

    @media (max-width: 768px) {
      .mobile-nav { display: flex; }
      .sidebar { transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: var(--shadow-soft); }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; padding: 1.5rem; }
      .header { flex-direction: column; align-items: flex-start; gap: 1rem; }
      .page-title { font-size: 2rem; }
    }
  </style>
</head>
<body>

<div id="app">

  <!-- MOBILE TOP NAV -->
  <div class="mobile-nav">
    <div class="brand">Rytmia.</div>
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">Rytmia.</div>
    <nav class="nav-links">
      <button class="nav-link active" id="nav-panel"      onclick="showView('panel')">🏠 Mi Panel</button>
      <button class="nav-link"        id="nav-equipo"     onclick="showView('equipo')">👥 Equipo Técnico</button>
      <button class="nav-link"        id="nav-grupos"     onclick="showView('grupos')">🏆 Mis Grupos</button>
      <button class="nav-link"        id="nav-gimnastas"  onclick="showView('gimnastas')">🤸‍♀️ Gimnastas</button>
      <button class="nav-link"        id="nav-calendario" onclick="showView('calendario')">📅 Calendario</button>
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

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <!-- ── VISTA: MI PANEL (DASHBOARD) ─────────────────────────── -->
    <div id="view-panel" class="view active">
      <header class="header">
        <div>
          <h1 class="page-title">Mi Panel</h1>
          <p class="page-subtitle">Bienvenida de vuelta, <span id="nombreBienvenida">entrenadora</span></p>
        </div>
      </header>

      <div class="welcome-card">
        <div class="welcome-title">¡Hola, <span id="welcomeName">entrenadora</span>! 👋</div>
        <div class="welcome-sub">Aquí puedes consultar tu información de perfil y acceder a tus grupos asignados.</div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-num" id="statAnios">–</div>
          <div class="stat-desc">Años de experiencia</div>
        </div>
        <div class="stat-card">
          <div class="stat-num" id="statHoras">–</div>
          <div class="stat-desc">Horas semanales</div>
        </div>
        <div class="stat-card">
          <div class="stat-num" id="statGruposCount">0</div>
          <div class="stat-desc">Grupos asignados</div>
        </div>
      </div>

      <div class="perfil-card">
        <div class="perfil-title">Mi Perfil</div>
        <div class="perfil-row"><span class="perfil-label">Nombre completo</span><span class="perfil-value" id="perfilNombre">–</span></div>
        <div class="perfil-row"><span class="perfil-label">DNI</span><span class="perfil-value" id="perfilDni">–</span></div>
        <div class="perfil-row"><span class="perfil-label">Email</span><span class="perfil-value" id="perfilEmail">–</span></div>
        <div class="perfil-row"><span class="perfil-label">Teléfono</span><span class="perfil-value" id="perfilTelefono">–</span></div>
        <div class="perfil-row"><span class="perfil-label">Titulación</span><span class="perfil-value" id="perfilTitulacion">–</span></div>
        <div class="perfil-row">
          <span class="perfil-label">Biografía</span>
          <span class="perfil-value" id="perfilBiografia" style="font-style:italic; color: var(--muted);">Sin información</span>
        </div>
      </div>
    </div>

    <!-- ── VISTA: EQUIPO TÉCNICO ───────────────────────────────── -->
    <div id="view-equipo" class="view">
      <header class="header">
        <div>
          <h1 class="page-title">Equipo Técnico</h1>
          <p class="page-subtitle">Compañeras del club y coordinadoras</p>
        </div>
      </header>
      <div id="teamGrid" class="team-grid">
        <div class="loading-state">
          <div class="loading-spinner"></div>
          <p>Cargando equipo…</p>
        </div>
      </div>
    </div>

    <!-- ── VISTA: MIS GRUPOS ──────────────────────────────────── -->
    <div id="view-grupos" class="view">
      <header class="header">
        <div>
          <h1 class="page-title">Mis Grupos</h1>
          <p class="page-subtitle">Listado de conjuntos bajo tu supervisión</p>
        </div>
      </header>

      <div id="gruposGrid" class="team-grid">
        <div class="loading-state">
          <div class="loading-spinner"></div>
          <p>Cargando tus grupos…</p>
        </div>
      </div>
    </div>

    <!-- ── VISTA: GIMNASTAS ────────────────────────────────────── -->
    <div id="view-gimnastas" class="view">
      <header class="header">
        <div>
          <h1 class="page-title">Gimnastas</h1>
          <p class="page-subtitle">Listado de todas las gimnastas del club</p>
        </div>
      </header>
      <div class="table-toolbar" style="margin-bottom: 2rem; display: flex; gap: 1rem;">
        <input type="text" id="searchGimnastas" class="search-input" placeholder="Buscar gimnasta..." style="flex:1; padding: 0.8rem; border-radius: var(--radius-md); border: 1px solid var(--blush);" oninput="buscarGimnastas()"/>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nombre</th>
              <th>DNI</th>
              <th>Categoría</th>
              <th>Grupo / Clase</th>
              <th>Teléfono Contacto</th>
            </tr>
          </thead>
          <tbody id="tbodyGimnastasTabla">
            <!-- Cargado dinámicamente -->
          </tbody>
        </table>
      </div>
      <div class="pagination" id="pagGimnastas" style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.5rem;"></div>
    </div>

    <!-- ── VISTA: CALENDARIO ───────────────────────────────────── -->
    <div id="view-calendario" class="view">
      <header class="header">
        <div>
          <h1 class="page-title">Calendario</h1>
          <p class="page-subtitle">Competiciones de mis grupos</p>
        </div>
      </header>
      <div class="perfil-card" style="padding: 1rem;">
        <div id="calendar"></div>
      </div>
    </div>

  </main>

  <!-- ── MODAL LISTA GIMNASTAS (desde grupos) ──────────────────── -->
  <div class="modal-overlay" id="modalGimnastasG" onclick="cerrarModal('modalGimnastasG', event)">
    <div class="modal-content" onclick="event.stopPropagation()">
      <div class="header" style="margin-bottom: 2rem;">
        <div>
          <h2 class="page-title" style="font-size: 1.8rem;" id="modalGrupoNombre">Gimnastas</h2>
          <p class="page-subtitle" id="modalGrupoCategoria">Categoría</p>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Gimnasta</th>
              <th>Licencia</th>
              <th>Teléfono Contacto</th>
            </tr>
          </thead>
          <tbody id="tbodyGimnastasModal">
            <!-- Cargado dinámicamente -->
          </tbody>
        </table>
      </div>

      <button class="modal-close-btn" onclick="document.getElementById('modalGimnastasG').classList.remove('open')">
        Cerrar
      </button>
    </div>
  </div>

</div>

<script>
  const API = '/api';
  const token = localStorage.getItem('rytmia_token');
  const user  = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

  // Redirigir si no hay sesión o no es entrenadora
  if (!token || user.rol !== 'entrenadora') {
    window.location.href = '/';
  }

  // UI inicial
  document.getElementById('sidebarName').textContent = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('sidebarAvatar').textContent = (user.nombre?.[0] ?? 'E').toUpperCase();
  document.getElementById('welcomeName').textContent = user.nombre ?? 'entrenadora';
  document.getElementById('nombreBienvenida').textContent = user.nombre ?? 'entrenadora';
  document.getElementById('perfilNombre').textContent = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('perfilDni').textContent    = user.dni ?? '–';
  document.getElementById('perfilEmail').textContent  = user.email ?? '–';
  document.getElementById('perfilTelefono').textContent = user.telefono ?? '–';

  // Obtener perfil completo y grupos
  let entrenadorId = null;
  fetch(`${API}/me`, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.entrenador) {
      entrenadorId = data.entrenador.id;
      document.getElementById('statAnios').textContent  = data.entrenador.anios_experiencia ?? 0;
      document.getElementById('statHoras').textContent  = data.entrenador.horas_semanales ?? 0;
      document.getElementById('perfilTitulacion').textContent = data.entrenador.titulacion ?? '–';
      if (data.entrenador.biografia) {
        document.getElementById('perfilBiografia').textContent = data.entrenador.biografia;
        document.getElementById('perfilBiografia').style.fontStyle = 'normal';
        document.getElementById('perfilBiografia').style.color = 'var(--text)';
      }
      cargarGruposStats();
    }
  })
  .catch(() => {});

  async function cargarGruposStats() {
    if (!entrenadorId) return;
    try {
      const res = await fetch(`${API}/conjuntos?entrenador_id=${entrenadorId}`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const count = (data.data ?? data).length ?? 0;
      document.getElementById('statGruposCount').textContent = count;
    } catch (e) {}
  }

  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
  }

  function showView(name) {
    document.querySelector('.sidebar').classList.remove('open');
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById('view-' + name).classList.add('active');
    document.getElementById('nav-' + name).classList.add('active');
    
    if (name === 'equipo') cargarEquipo();
    if (name === 'grupos') cargarGrupos();
    if (name === 'gimnastas') cargarGimnastas();
    if (name === 'calendario') initCalendar();
  }

  async function cargarEquipo() {
    const grid = document.getElementById('teamGrid');
    grid.innerHTML = `<div class="loading-state"><div class="loading-spinner"></div><p>Cargando equipo…</p></div>`;
    try {
      const res = await fetch(`${API}/usuarios-por-rol/entrenadora`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const lista = data.data ?? data;

      grid.innerHTML = lista.map(u => `
        <div class="team-card">
          <div class="card-avatar">${(u.nombre?.[0] ?? '?').toUpperCase()}</div>
          <div class="card-name">${u.nombre} ${u.apellidos ?? ''}</div>
          <div class="card-role">${u.entrenador?.titulacion ?? 'Entrenadora'}</div>
          <div class="card-stats">
            <div class="stat">
              <span class="stat-val">${u.entrenador?.anios_experiencia ?? 0}</span>
              <span class="stat-label">Años exp.</span>
            </div>
          </div>
          <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 1rem;">${u.email ?? ''}</div>
          <div style="font-size: 0.85rem; color: var(--burgundy); font-weight: 600;">${u.telefono ?? ''}</div>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<p style="color:red; text-align:center">Error al cargar equipo.</p>`;
    }
  }

  async function cargarGrupos() {
    const grid = document.getElementById('gruposGrid');
    grid.innerHTML = `<div class="loading-state"><div class="loading-spinner"></div><p>Cargando tus grupos…</p></div>`;

    if (!entrenadorId) return;

    try {
      const res = await fetch(`${API}/conjuntos?entrenador_id=${entrenadorId}&per_page=50`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const lista = data.data ?? data;

      if (!lista || lista.length === 0) {
        grid.innerHTML = `<div class="loading-state"><p>No tienes grupos asignados actualmente.</p></div>`;
        return;
      }

      grid.innerHTML = lista.map(g => `
        <div class="team-card">
          <div class="card-avatar">🏆</div>
          <div class="card-name">${g.nombre}</div>
          <div class="card-role">${g.categoria?.nombre ?? 'Sin categoría'}</div>
          <div class="card-stats">
            <div class="stat">
              <span class="stat-val">${g.total_gimnastas ?? (g.gimnastas?.length ?? 0)}</span>
              <span class="stat-label">Gimnastas</span>
            </div>
          </div>
          <button class="btn-outline" onclick='verGimnastasDelGrupo(${g.id})'>Ver alumnas y teléfonos</button>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<div class="loading-state"><p style="color:red">Error cargando grupos.</p></div>`;
    }
  }

  async function verGimnastasDelGrupo(id) {
    const tbody = document.getElementById('tbodyGimnastasModal');
    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center">Cargando...</td></tr>';
    document.getElementById('modalGimnastasG').classList.add('open');

    try {
      const res = await fetch(`${API}/conjuntos/${id}`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const conjunto = data.data ?? data;

      document.getElementById('modalGrupoNombre').textContent = conjunto.nombre;
      document.getElementById('modalGrupoCategoria').textContent = conjunto.categoria?.nombre ?? 'Categoría';

      const gimnastas = conjunto.gimnastas ?? [];
      if (gimnastas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center">No hay gimnastas en este grupo.</td></tr>';
      } else {
        tbody.innerHTML = gimnastas.map(g => `
          <tr>
            <td><strong>${g.nombre} ${g.apellidos ?? ''}</strong></td>
            <td>${g.numero_licencia ?? '–'}</td>
            <td><a href="tel:${g.telefono_contacto ?? ''}" style="color:var(--burgundy); font-weight:600; text-decoration:none">${g.telefono_contacto ?? 'Sin teléfono'}</a></td>
          </tr>
        `).join('');
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:red">Error al cargar datos.</td></tr>';
    }
  }

  async function cargarGimnastas(pagina = 1) {
    const tbody = document.getElementById('tbodyGimnastasTabla');
    const search = document.getElementById('searchGimnastas').value;
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">Cargando...</td></tr>';

    try {
      let url = `${API}/usuarios?rol=gimnasta&page=${pagina}`;
      if (search) url += `&search=${encodeURIComponent(search)}`;

      const res = await fetch(url, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const lista = data.data ?? [];

      if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">No hay gimnastas registradas.</td></tr>';
      } else {
        tbody.innerHTML = lista.map(u => `
          <tr>
            <td><strong>${u.nombre} ${u.apellidos ?? ''}</strong></td>
            <td>${u.dni ?? '–'}</td>
            <td><span class="badge" style="background:var(--cream); color:var(--burgundy); border:1px solid var(--blush); padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem;">${u.gimnasta?.categoria?.nombre ?? '–'}</span></td>
            <td>${u.gimnasta?.conjunto?.nombre ?? '<small style="color:var(--muted)">Sin Asignar</small>'}</td>
            <td><a href="tel:${u.gimnasta?.telefono_contacto ?? ''}" style="color:var(--burgundy); font-weight:600; text-decoration:none">${u.gimnasta?.telefono_contacto ?? 'Sin teléfono'}</a></td>
          </tr>
        `).join('');
      }

      // Paginación
      const pagEl = document.getElementById('pagGimnastas');
      pagEl.innerHTML = '';
      if (data.last_page > 1) {
        for (let i = 1; i <= data.last_page; i++) {
          const btn = document.createElement('button');
          btn.style.padding = '0.5rem 0.8rem';
          btn.style.border = '1px solid var(--blush)';
          btn.style.borderRadius = '8px';
          btn.style.background = i === pagina ? 'var(--burgundy)' : 'white';
          btn.style.color = i === pagina ? 'white' : 'var(--text)';
          btn.style.cursor = 'pointer';
          btn.textContent = i;
          btn.onclick = () => cargarGimnastas(i);
          pagEl.appendChild(btn);
        }
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:red">Error al cargar gimnastas.</td></tr>';
    }
  }

  function buscarGimnastas() {
    clearTimeout(window._searchTimer);
    window._searchTimer = setTimeout(() => cargarGimnastas(1), 400);
  }

  function cerrarModal(id, event) {
    if (event.target === document.getElementById(id)) {
      document.getElementById(id).classList.remove('open');
    }
  }

  let calendarInstance = null;
  function initCalendar() {
    if (calendarInstance) return;
    const calendarEl = document.getElementById('calendar');
    fetch(`${API}/competiciones`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
      const events = data.map(c => ({
        title: c.nombre + ' (' + c.conjunto?.nombre + ')',
        start: c.fecha,
        color: 'var(--burgundy)'
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
