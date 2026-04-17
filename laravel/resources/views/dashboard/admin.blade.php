<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Administrador</title>
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
    body { font-family: 'DM Sans', sans-serif; background-color: var(--off-white); color: var(--text); display: flex; min-height: 100vh; }
    #app { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }

    /* === SIDEBAR === */
    .sidebar { width: 280px; background-color: var(--white); border-right: 1px solid var(--blush); display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 10; }
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

    /* === MAIN CONTENT === */
    .main-content { flex: 1; margin-left: 280px; padding: 3rem; }
    .view { display: none; }
    .view.active { display: block; }
    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; }
    .page-title { font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: var(--burgundy); }
    .page-subtitle { color: var(--muted); margin-top: 0.5rem; }

    /* === BUTTONS === */
    .btn-primary { background: linear-gradient(135deg, var(--burgundy), var(--rose)); color: var(--white); padding: 0.8rem 1.5rem; border-radius: var(--radius-md); border: none; font-family: 'DM Sans', sans-serif; font-weight: 500; cursor: pointer; transition: opacity 0.3s, transform 0.2s; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-outline { background: transparent; border: 1.5px solid var(--blush); color: var(--text); padding: 0.6rem 1.2rem; border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; }
    .btn-outline:hover { border-color: var(--burgundy); color: var(--burgundy); background-color: var(--cream); }
    .btn-danger { background: transparent; border: 1.5px solid rgba(217,79,79,.4); color: var(--error); padding: 0.5rem 1rem; border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s; font-size: 0.85rem; font-family: 'DM Sans', sans-serif; }
    .btn-danger:hover { background: #ffebee; }

    /* === EQUIPO TÉCNICO — listado === */
    .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
    .team-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); display: flex; flex-direction: column; align-items: center; text-align: center; transition: all 0.3s; }
    .team-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(107,26,58,.12); }
    .card-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, var(--cream), var(--blush)); color: var(--burgundy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; border: 2px solid var(--blush); font-weight: 600; font-family: 'Cormorant Garamond', serif; }
    .card-name { font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--text); margin-bottom: 0.3rem; }
    .card-role { font-size: 0.85rem; color: var(--rose); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; }
    .card-club { font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem; }
    .card-stats { display: flex; gap: 2rem; margin-bottom: 1.5rem; justify-content: center; }
    .stat { display: flex; flex-direction: column; align-items: center; }
    .stat-val { font-weight: 700; color: var(--burgundy); font-size: 1.3rem; font-family: 'Cormorant Garamond', serif; }
    .stat-label { font-size: 0.75rem; color: var(--muted); }
    .card-actions { display: flex; gap: 0.75rem; width: 100%; }

    /* === BADGE ESTADO === */
    .badge { display: inline-block; padding: 0.2rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-activa   { background-color: #e8f5e9; color: #2e7d32; }
    .badge-inactiva { background-color: #fff8e1; color: #f57f17; }
    .badge-baja     { background-color: #ffebee; color: #c62828; }

    /* === LOADING / EMPTY === */
    .loading-state { text-align: center; padding: 4rem 2rem; color: var(--muted); }
    .loading-spinner { width: 40px; height: 40px; border: 3px solid var(--blush); border-top-color: var(--rose); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1rem; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.5rem; margin-bottom: 0.5rem; }
    .empty-desc { color: var(--muted); }

    /* === MODAL PERFIL === */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(42,21,32,.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-content { background: var(--white); width: 90%; max-width: 540px; border-radius: var(--radius-lg); padding: 2.5rem; position: relative; transform: translateY(20px); transition: transform 0.3s; box-shadow: 0 20px 60px rgba(0,0,0,.15); max-height: 90vh; overflow-y: auto; }
    .modal-overlay.open .modal-content { transform: translateY(0); }
    .modal-header { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; }
    .modal-avatar { width: 70px; height: 70px; background: linear-gradient(135deg, var(--cream), var(--blush)); color: var(--burgundy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; font-family: 'Cormorant Garamond', serif; flex-shrink: 0; }
    .modal-name { font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; color: var(--text); }
    .modal-subtitle { color: var(--muted); font-size: 0.9rem; }
    .modal-section { margin-bottom: 1.5rem; }
    .modal-section-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.2rem; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--blush); }
    .modal-row { display: flex; gap: 0.75rem; padding: 0.5rem 0; }
    .modal-label { color: var(--muted); font-size: 0.85rem; min-width: 140px; }
    .modal-value { color: var(--text); font-weight: 500; font-size: 0.95rem; }
    .modal-bio { color: var(--text); line-height: 1.7; font-size: 0.95rem; font-style: italic; background: var(--cream); padding: 1rem; border-radius: var(--radius-md); }
    .modal-close-btn { width: 100%; padding: 0.8rem; background: var(--off-white); border: 1px solid var(--blush); border-radius: var(--radius-md); color: var(--muted); cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif; margin-top: 1.5rem; }
    .modal-close-btn:hover { background: var(--blush); color: var(--burgundy); }

    /* === MODAL USUARIO (crear/editar) === */
    .form-modal { max-width: 600px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { display: block; font-size: 0.82rem; font-weight: 500; color: var(--text); margin-bottom: 0.4rem; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 0.7rem 1rem; border: 1.5px solid var(--blush); border-radius: var(--radius-md); font-family: 'DM Sans', sans-serif; font-size: 0.9rem; color: var(--text); background: var(--cream); outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--rose); background: var(--white); box-shadow: 0 0 0 3px rgba(196,92,126,.12); }
    .form-textarea { resize: vertical; min-height: 100px; }
    .form-section-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.1rem; margin: 1rem 0 0.75rem; grid-column: 1 / -1; border-top: 1px solid var(--blush); padding-top: 1rem; }
    .modal-actions { display: flex; gap: 1rem; margin-top: 1.5rem; }
    .modal-actions .btn-primary { flex: 1; }
    .alert-banner { padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.9rem; display: none; }
    .alert-error   { background: #ffebee; color: var(--error); border: 1px solid rgba(217,79,79,.3); display: block; }
    .alert-success { background: #e8f5e9; color: var(--success); border: 1px solid #c8e6c9; display: block; }

    /* === TABLA GESTIÓN === */
    .table-toolbar { display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center; flex-wrap: wrap; }
    .search-input { flex: 1; min-width: 200px; padding: 0.7rem 1rem; border: 1.5px solid var(--blush); border-radius: var(--radius-md); font-family: 'DM Sans', sans-serif; font-size: 0.9rem; background: var(--white); outline: none; }
    .search-input:focus { border-color: var(--rose); }
    .filter-select { padding: 0.7rem 1rem; border: 1.5px solid var(--blush); border-radius: var(--radius-md); font-family: 'DM Sans', sans-serif; font-size: 0.9rem; background: var(--white); outline: none; }
    .table-wrap { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--blush); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: var(--cream); }
    th { padding: 1rem 1.5rem; text-align: left; font-size: 0.8rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 1rem 1.5rem; font-size: 0.9rem; border-top: 1px solid var(--blush); }
    tr:hover td { background: var(--off-white); }
    .td-actions { display: flex; gap: 0.5rem; }

    /* === PAGINATION === */
    .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
    .page-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--blush); background: var(--white); color: var(--text); cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
    .page-btn:hover, .page-btn.active { background: var(--burgundy); color: var(--white); border-color: var(--burgundy); }

    /* === NAV MOBILE === */
    .mobile-nav { display: none; padding: 1rem 1.5rem; background: var(--white); border-bottom: 1px solid var(--blush); align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 20; }
    .mobile-nav .brand { padding: 0; border: none; font-size: 1.8rem; text-align: left; }
    .menu-btn { background: none; border: none; font-size: 1.8rem; color: var(--burgundy); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; }

    @media (max-width: 768px) {
      .mobile-nav { display: flex; }
      .sidebar { transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: var(--shadow-soft); }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; padding: 1.5rem; }
      .form-grid { grid-template-columns: 1fr; }
      .header { flex-direction: column; align-items: flex-start; gap: 1rem; }
      .page-title { font-size: 2rem; }
      table { display: block; overflow-x: auto; white-space: nowrap; }
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
      <button class="nav-link active" id="nav-equipo"    onclick="showView('equipo')">👥 Equipo Técnico</button>
      <button class="nav-link"        id="nav-grupos"    onclick="showView('grupos')">🏆 Grupos / Clases</button>
      <button class="nav-link"        id="nav-gimnastas" onclick="showView('gimnastas')">🤸‍♀️ Gimnastas</button>
      <button class="nav-link"        id="nav-admins"    onclick="showView('admins')">⚙️ Administradores</button>
      <button class="nav-link"        id="nav-calendario"onclick="showView('calendario')">📅 Calendario</button>
    </nav>
    <div class="user-profile">
      <div class="avatar" id="sidebarAvatar">A</div>
      <div class="user-info">
        <div class="user-name" id="sidebarName">Admin</div>
        <div class="user-role">Administrador</div>
      </div>
      <button class="logout-btn" onclick="logout()" title="Cerrar sesión">🚪</button>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <!-- ── VISTA: EQUIPO TÉCNICO ───────────────────────────────── -->
    <div class="view active" id="view-equipo">
      <div class="header">
        <div>
          <h1 class="page-title">Equipo Técnico</h1>
          <p class="page-subtitle">Gestiona las entrenadoras del club</p>
        </div>
        <button class="btn-primary" onclick="abrirFormUsuario('entrenadora')">+ Nueva Entrenadora</button>
      </div>
      <div id="teamGrid" class="team-grid">
        <div class="loading-state">
          <div class="loading-spinner"></div>
          <p>Cargando entrenadoras…</p>
        </div>
      </div>
    </div>

    <!-- ── VISTA: GRUPOS ───────────────────────────────────────── -->
    <div class="view" id="view-grupos">
      <div class="header">
        <div>
          <h1 class="page-title">Grupos y Clases</h1>
          <p class="page-subtitle">Gestiona los conjuntos y sus gimnastas asignadas</p>
        </div>
      </div>
      <div id="gruposGrid" class="team-grid">
        <div class="loading-state">
          <div class="loading-spinner"></div>
          <p>Cargando grupos…</p>
        </div>
      </div>
    </div>

    <!-- ── VISTA: GIMNASTAS ────────────────────────────────────── -->
    <div class="view" id="view-gimnastas">
      <div class="header">
        <div>
          <h1 class="page-title">Gimnastas</h1>
          <p class="page-subtitle">Listado de todas las gimnastas del club</p>
        </div>
        <button class="btn-primary" onclick="abrirFormUsuario('gimnasta')">+ Nueva Gimnasta</button>
      </div>
      <div class="table-toolbar">
        <input type="text" id="searchGimnastas" class="search-input" placeholder="Buscar por nombre, DNI…" oninput="buscarUsuarios('gimnasta')"/>
        <select class="filter-select" id="filterEstadoGimnastas" onchange="buscarUsuarios('gimnasta')">
          <option value="">Todos los estados</option>
          <option value="1">Activas</option>
          <option value="0">Inactivas</option>
        </select>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Nombre</th><th>DNI</th><th>Categoría</th><th>Grupo / Clase</th><th>Teléfono</th><th>Estado</th><th>Acciones</th>
          </tr></thead>
          <tbody id="tbodyGimnastas"></tbody>
        </table>
      </div>
      <div class="pagination" id="pagGimnastas"></div>
    </div>

    <!-- ── VISTA: ADMINISTRADORES ──────────────────────────────── -->
    <div class="view" id="view-admins">
      <div class="header">
        <div>
          <h1 class="page-title">Administradores</h1>
          <p class="page-subtitle">Gestiona las cuentas de administrador</p>
        </div>
        <button class="btn-primary" onclick="abrirFormUsuario('administrador')">+ Nuevo Admin</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Nombre</th><th>DNI</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Acciones</th>
          </tr></thead>
          <tbody id="tbodyAdmins"></tbody>
        </table>
      </div>
    </div>
    
    <!-- ── VISTA: CALENDARIO ───────────────────────────────────── -->
    <div class="view" id="view-calendario">
      <div class="header">
        <div>
          <h1 class="page-title">Calendario de Competiciones</h1>
          <p class="page-subtitle">Visualiza todas las competiciones</p>
        </div>
        <button class="btn-primary" onclick="abrirFormCompeticion()">+ Añadir Competición</button>
      </div>
      <div class="table-wrap" style="padding: 2rem;">
        <div id="calendar"></div>
      </div>
    </div>

  </main>

  <!-- ── MODAL PERFIL ENTRENADORA ───────────────────────────────── -->
  <div class="modal-overlay" id="modalPerfil" onclick="cerrarModal('modalPerfil', event)">
    <div class="modal-content" onclick="event.stopPropagation()">
      <div class="modal-header">
        <div class="modal-avatar" id="mpAvatar">L</div>
        <div>
          <div class="modal-name" id="mpName">–</div>
          <div class="modal-subtitle" id="mpTitulacion">–</div>
        </div>
      </div>

      <div class="modal-section">
        <div class="modal-section-title">Información de contacto</div>
        <div class="modal-row"><span class="modal-label">Email</span><span class="modal-value" id="mpEmail">–</span></div>
        <div class="modal-row"><span class="modal-label">Teléfono</span><span class="modal-value" id="mpTelefono">–</span></div>
        <div class="modal-row"><span class="modal-label">DNI</span><span class="modal-value" id="mpDni">–</span></div>
        <div class="modal-row"><span class="modal-label">Club</span><span class="modal-value" id="mpClub">–</span></div>
      </div>

      <div class="modal-section">
        <div class="modal-section-title">Experiencia</div>
        <div class="modal-row"><span class="modal-label">Años de experiencia</span><span class="modal-value" id="mpAnios">–</span></div>
        <div class="modal-row"><span class="modal-label">Horas semanales</span><span class="modal-value" id="mpHoras">–</span></div>
        <div class="modal-row"><span class="modal-label">Estado</span><span class="modal-value" id="mpEstado">–</span></div>
      </div>

      <div class="modal-section" id="mpBiografiaSection" style="display:none">
        <div class="modal-section-title">Biografía</div>
        <div class="modal-bio" id="mpBiografia">–</div>
      </div>

      <button class="modal-close-btn" onclick="document.getElementById('modalPerfil').classList.remove('open')">
        Cerrar perfil
      </button>
    </div>
  </div>

  <!-- ── MODAL GESTIÓN GRUPO ──────────────────────────────────── -->
  <div class="modal-overlay" id="modalGrupo" onclick="cerrarModal('modalGrupo', event)">
    <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 700px;">
      <div class="modal-header" style="margin-bottom:1.5rem">
        <div>
          <h2 class="modal-name" id="mgTitle">Grupo</h2>
          <div class="modal-subtitle" id="mgCategoria">Categoría</div>
        </div>
      </div>
      
      <div class="alert-banner" id="mgAlert"></div>

      <div class="modal-section" style="margin-bottom: 2rem;">
        <div class="form-section-title full">Añadir Gimnasta (Misma categoría)</div>
        <div style="display:flex; gap:1rem; margin-top:0.5rem; align-items:center;">
          <select class="form-select" id="mgSelectGimnasta" style="flex:1;">
            <option value="">Cargando gimnastas...</option>
          </select>
          <button class="btn-primary" onclick="asignarGimnastaAlGrupo()">Añadir</button>
        </div>
      </div>

      <div class="modal-section">
        <div class="form-section-title full" style="margin-bottom: 0.5rem">Gimnastas asignadas (<span id="mgTotal">0</span>)</div>
        <div class="table-wrap" style="box-shadow:none; border:1px solid var(--blush)">
          <table>
            <thead style="background:var(--off-white)"><tr>
              <th>Nombre Completo</th>
              <th>Licencia</th>
              <th style="width: 80px;">Acción</th>
            </tr></thead>
            <tbody id="mgTbodyGimnastas">
            </tbody>
          </table>
        </div>
      </div>

      <button class="modal-close-btn" onclick="document.getElementById('modalGrupo').classList.remove('open')">
        Cerrar
      </button>
    </div>
  </div>

  <!-- ── MODAL CREAR / EDITAR USUARIO ──────────────────────────── -->
  <div class="modal-overlay" id="modalForm" onclick="cerrarModal('modalForm', event)">
    <div class="modal-content form-modal" onclick="event.stopPropagation()">
      <h2 class="modal-name" id="formTitle" style="margin-bottom:1.5rem">Nuevo Usuario</h2>
      <div class="alert-banner" id="formAlert"></div>

      <form id="userForm" onsubmit="submitUsuario(event)">
        <input type="hidden" id="formMode" value="crear" />
        <input type="hidden" id="formUserId" value="" />
        <input type="hidden" id="formRol" value="" />

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="formNombre">Nombre *</label>
            <input class="form-input" id="formNombre" type="text" required />
          </div>
          <div class="form-group">
            <label class="form-label" for="formApellidos">Apellidos *</label>
            <input class="form-input" id="formApellidos" type="text" required />
          </div>
          <div class="form-group">
            <label class="form-label" for="formDni">DNI * (8 dígitos + letra)</label>
            <input class="form-input" id="formDni" type="text" maxlength="9" placeholder="12345678A" required />
          </div>
          <div class="form-group">
            <label class="form-label" for="formEmail">Email</label>
            <input class="form-input" id="formEmail" type="email" />
          </div>
          <div class="form-group">
            <label class="form-label" for="formTelefono">Teléfono</label>
            <input class="form-input" id="formTelefono" type="text" maxlength="15" />
          </div>
          <div class="form-group">
            <label class="form-label" for="formPassword">Contraseña <span id="pwRequired">*</span></label>
            <input class="form-input" id="formPassword" type="password" placeholder="Mín. 8 car., mayús. y números" />
          </div>

          <!-- Campos entrenadora -->
          <div id="fieldsEntrenadora" style="display:contents">
            <div class="form-section-title full">Perfil Entrenadora</div>
            <div class="form-group">
              <label class="form-label" for="formTitulacion">Titulación</label>
              <input class="form-input" id="formTitulacion" type="text" placeholder="Ej. Nivel III RFEG" />
            </div>
            <div class="form-group">
              <label class="form-label" for="formAniosExp">Años de experiencia</label>
              <input class="form-input" id="formAniosExp" type="number" min="0" value="0" />
            </div>
            <div class="form-group">
              <label class="form-label" for="formHorasSem">Horas semanales</label>
              <input class="form-input" id="formHorasSem" type="number" min="0" value="0" />
            </div>
            <div class="form-group">
              <label class="form-label" for="formEstado">Estado</label>
              <select class="form-select" id="formEstado">
                <option value="activa">Activa</option>
                <option value="inactiva">Inactiva</option>
                <option value="baja">Baja</option>
              </select>
            </div>
            <div class="form-group full">
              <label class="form-label" for="formBiografia">Biografía</label>
              <textarea class="form-textarea" id="formBiografia" placeholder="Describe la trayectoria y experiencia de la entrenadora…"></textarea>
            </div>
          </div>

          <!-- Campos Gimnasta -->
          <div id="fieldsGimnasta" style="display:none">
            <div class="form-section-title full">Perfil Gimnasta</div>
            <div class="form-group">
              <label class="form-label" for="formGimnastaCat">Categoría <span style="color:var(--error)">*</span></label>
              <select class="form-select" id="formGimnastaCat" onchange="actualizarSelectConjuntos()" required></select>
            </div>
            <div class="form-group">
              <label class="form-label" for="formGimnastaConj">Grupo / Clase</label>
              <select class="form-select" id="formGimnastaConj">
                <option value="">Selecciona una categoría primero</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="formTelefonoContacto">Teléfono de Contacto (Gimnasta)</label>
              <input class="form-input" id="formTelefonoContacto" type="text" maxlength="20" placeholder="Teléfono de contacto" />
            </div>
          </div>

          <!-- Activo (solo edición) -->
          <div class="form-group" id="activoField" style="display:none">
            <label class="form-label" for="formActivo">Estado de cuenta</label>
            <select class="form-select" id="formActivo">
              <option value="1">Activa / Activo</option>
              <option value="0">Inactiva / Inactivo</option>
            </select>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-outline" onclick="document.getElementById('modalForm').classList.remove('open')">Cancelar</button>
          <button type="submit" class="btn-primary" id="btnSubmit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
    <!-- ── MODAL NUEVA COMPETICION ─────────────────────────────── -->
  <div class="modal-overlay" id="modalCompeticion" onclick="cerrarModal('modalCompeticion', event)">
    <div class="modal-content form-modal" onclick="event.stopPropagation()">
      <h2 class="modal-name" style="margin-bottom:1.5rem">Nueva Competición</h2>
      <div class="alert-banner" id="compAlert"></div>

      <form id="competicionForm" onsubmit="guardarCompeticion(event)">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label" for="compNombre">Nombre de la competición *</label>
            <input class="form-input" id="compNombre" type="text" placeholder="Ej: Torneo Nacional" required />
          </div>
          <div class="form-group full">
            <label class="form-label" for="compConjunto">Grupo que asiste *</label>
            <select class="form-select" id="compConjunto" required>
              <option value="">Cargando grupos...</option>
            </select>
          </div>
          <div class="form-group full">
            <label class="form-label" for="compFecha">Fecha / Horario *</label>
            <input class="form-input" id="compFecha" type="date" required />
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-outline" onclick="document.getElementById('modalCompeticion').classList.remove('open')">Cancelar</button>
          <button type="submit" class="btn-primary" id="btnSubmitComp">Guardar Competición</button>
        </div>
      </form>
    </div>
  </div>

</div>

</div>

<script>
  /* ════════════════════════════════════════════════════
   * Configuración
   * ════════════════════════════════════════════════════ */
  const API   = '/api';
  const token = localStorage.getItem('rytmia_token');
  const user  = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

  let _categoriasGlobales = [];
  let _conjuntosGlobales = [];

  // Guardia de seguridad: solo admins
  if (!token || user.rol !== 'administrador') {
    window.location.href = '/';
  }

  // Cifrado de cat y conjuntos en fondo
  Promise.all([
    apiFetch('/categorias').then(r => _categoriasGlobales = (r.data || r)),
    apiFetch('/conjuntos').then(r => _conjuntosGlobales = (r.data || []))
  ]).catch(() => console.error("Error pre-cargando cat/conj"));

  // Nombre en sidebar
  document.getElementById('sidebarName').textContent   = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
  document.getElementById('sidebarAvatar').textContent = (user.nombre?.[0] ?? 'A').toUpperCase();

  /* ════════════════════════════════════════════════════
   * Navegación entre vistas
   * ════════════════════════════════════════════════════ */
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
  }

  function showView(name) {
    document.querySelector('.sidebar').classList.remove('open'); // Cerrar en móviles si estaba abierto
    
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById('view-' + name).classList.add('active');
    document.getElementById('nav-' + name).classList.add('active');

    if (name === 'equipo')    cargarEntrenadoras();
    if (name === 'grupos')    cargarGrupos();
    if (name === 'gimnastas') cargarTablaUsuarios('gimnasta', 1);
    if (name === 'admins')    cargarTablaUsuarios('administrador', 1);
    if (name === 'calendario') initCalendar();
  }

  /* ════════════════════════════════════════════════════
   * Helpers fetch
   * ════════════════════════════════════════════════════ */
  async function apiFetch(path, opts = {}) {
    const res = await fetch(API + path, {
      ...opts,
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'Authorization': `Bearer ${token}`,
        ...(opts.headers ?? {}),
      },
    });
    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  }

  /* ════════════════════════════════════════════════════
   * EQUIPO TÉCNICO — tarjetas entrenadoras
   * ════════════════════════════════════════════════════ */
  async function cargarEntrenadoras() {
    const grid = document.getElementById('teamGrid');
    grid.innerHTML = `<div class="loading-state"><div class="loading-spinner"></div><p>Cargando entrenadoras…</p></div>`;

    try {
      const data = await apiFetch('/usuarios?rol=entrenadora&per_page=50');
      const lista = data.data ?? [];

      if (!lista.length) {
        grid.innerHTML = `<div class="empty-state"><div class="empty-icon">👥</div><div class="empty-title">Sin entrenadoras</div><div class="empty-desc">Aún no hay entrenadoras registradas en el club.</div></div>`;
        return;
      }

      grid.innerHTML = lista.map(u => `
        <div class="team-card">
          <div class="card-avatar">${(u.nombre?.[0] ?? '?').toUpperCase()}</div>
          <div class="card-name">${u.nombre} ${u.apellidos ?? ''}</div>
          <div class="card-role">${u.entrenador?.titulacion ?? 'Entrenadora'}</div>
          <div class="card-club">${u.entrenador?.club?.nombre ?? '–'}</div>
          <div class="card-stats">
            <div class="stat">
              <span class="stat-val">${u.entrenador?.anios_experiencia ?? 0}</span>
              <span class="stat-label">Años exp.</span>
            </div>
            <div class="stat">
              <span class="stat-val">${u.entrenador?.horas_semanales ?? 0}</span>
              <span class="stat-label">H/Semana</span>
            </div>
          </div>
          <div class="card-actions">
            <button class="btn-outline" style="flex:1" onclick='abrirPerfilEntrenadora(${JSON.stringify(u)})'>Ver perfil</button>
            <button class="btn-outline" onclick='abrirFormEditar(${JSON.stringify(u)})'>✏️</button>
            <button class="btn-danger"  onclick='eliminarUsuario(${u.id}, "la entrenadora")'>🗑️</button>
          </div>
        </div>
      `).join('');

    } catch (err) {
      grid.innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><div class="empty-title">Error al cargar</div><div class="empty-desc">${err.message ?? 'Inténtalo de nuevo.'}</div></div>`;
    }
  }

  function abrirPerfilEntrenadora(u) {
    document.getElementById('mpAvatar').textContent   = (u.nombre?.[0] ?? '?').toUpperCase();
    document.getElementById('mpName').textContent     = `${u.nombre} ${u.apellidos ?? ''}`;
    document.getElementById('mpTitulacion').textContent = u.entrenador?.titulacion ?? 'Entrenadora';
    document.getElementById('mpEmail').textContent    = u.email ?? '–';
    document.getElementById('mpTelefono').textContent = u.telefono ?? '–';
    document.getElementById('mpDni').textContent      = u.dni ?? '–';
    document.getElementById('mpClub').textContent     = u.entrenador?.club?.nombre ?? '–';
    document.getElementById('mpAnios').textContent    = `${u.entrenador?.anios_experiencia ?? 0} años`;
    document.getElementById('mpHoras').textContent    = `${u.entrenador?.horas_semanales ?? 0} h/semana`;
    document.getElementById('mpEstado').innerHTML     = `<span class="badge badge-${u.entrenador?.estado ?? 'activa'}">${u.entrenador?.estado ?? 'activa'}</span>`;

    const bio = u.entrenador?.biografia;
    const bioSec = document.getElementById('mpBiografiaSection');
    if (bio) {
      document.getElementById('mpBiografia').textContent = bio;
      bioSec.style.display = 'block';
    } else {
      bioSec.style.display = 'none';
    }

    document.getElementById('modalPerfil').classList.add('open');
  }

  /* ════════════════════════════════════════════════════
   * TABLA USUARIOS (gimnastas / admins)
   * ════════════════════════════════════════════════════ */
  let paginaActual = { gimnasta: 1, administrador: 1 };

  async function cargarTablaUsuarios(rol, pagina = 1) {
    paginaActual[rol] = pagina;
    const search  = document.getElementById('searchGimnastas')?.value ?? '';
    const activo  = document.getElementById('filterEstadoGimnastas')?.value ?? '';
    const tbodyId = rol === 'gimnasta' ? 'tbodyGimnastas' : 'tbodyAdmins';
    const pagId   = rol === 'gimnasta' ? 'pagGimnastas' : null;

    let url = `/usuarios?rol=${rol}&page=${pagina}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (activo !== '') url += `&activo=${activo}`;

    const tbody = document.getElementById(tbodyId);
    tbody.innerHTML = `<tr><td colspan="100%" style="text-align:center;padding:2rem;color:var(--muted)">Cargando…</td></tr>`;

    try {
      const data = await apiFetch(url);
      const lista = data.data ?? [];

      if (!lista.length) {
        tbody.innerHTML = `<tr><td colspan="100%" style="text-align:center;padding:2rem;color:var(--muted)">Sin resultados.</td></tr>`;
      } else {
        tbody.innerHTML = lista.map(u => {
          let cols = `
            <td><strong>${u.nombre} ${u.apellidos ?? ''}</strong></td>
            <td>${u.dni ?? '–'}</td>`;
          if (rol === 'gimnasta') {
            cols += `
            <td><span class="badge" style="background:var(--cream); color:var(--burgundy); border:1px solid var(--blush)">${u.gimnasta?.categoria?.nombre ?? 'Sin calc'}</span></td>
            <td>${u.gimnasta?.conjunto?.nombre ?? '<small style="color:var(--muted)">Sin Asignar</small>'}</td>`;
          } else {
            cols += `<td>${u.email ?? '–'}</td>`;
          }
          cols += `
            <td>${u.telefono ?? '–'}</td>
            <td><span class="badge ${u.activo ? 'badge-activa' : 'badge-inactiva'}">${u.activo ? 'Activo' : 'Inactivo'}</span></td>
            <td>
              <div class="td-actions">
                <button class="btn-outline" onclick='abrirFormEditar(${JSON.stringify(u)})'>✏️ Editar</button>
                <button class="btn-danger"  onclick='eliminarUsuario(${u.id}, "el usuario")'>🗑️</button>
              </div>
            </td>`;
          return `<tr>${cols}</tr>`;
        }).join('');
      }

      // Paginación
      if (pagId && data.last_page > 1) {
        const pagEl = document.getElementById(pagId);
        pagEl.innerHTML = '';
        for (let i = 1; i <= data.last_page; i++) {
          const btn = document.createElement('button');
          btn.className = 'page-btn' + (i === pagina ? ' active' : '');
          btn.textContent = i;
          btn.onclick = () => cargarTablaUsuarios(rol, i);
          pagEl.appendChild(btn);
        }
      }

    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--error)">Error: ${err.message ?? 'inténtalo de nuevo.'}</td></tr>`;
    }
  }

  function buscarUsuarios(rol) {
    clearTimeout(window._searchTimer);
    window._searchTimer = setTimeout(() => cargarTablaUsuarios(rol, 1), 400);
  }

  /* ════════════════════════════════════════════════════
   * CRUD — Formulario crear / editar
   * ════════════════════════════════════════════════════ */
  function abrirFormUsuario(rol) {
    document.getElementById('userForm').reset();
    document.getElementById('formMode').value   = 'crear';
    document.getElementById('formUserId').value = '';
    document.getElementById('formRol').value    = rol;
    document.getElementById('formTitle').textContent = `Nueva ${rol === 'entrenadora' ? 'Entrenadora' : rol === 'gimnasta' ? 'Gimnasta' : 'Administradora'}`;
    document.getElementById('pwRequired').style.display = 'inline';
    document.getElementById('formPassword').required = true;
    document.getElementById('activoField').style.display = 'none';
    
    document.getElementById('fieldsEntrenadora').style.display = rol === 'entrenadora' ? 'contents' : 'none';
    document.getElementById('fieldsGimnasta').style.display = rol === 'gimnasta' ? 'contents' : 'none';
    if (rol === 'gimnasta') {
      document.getElementById('formGimnastaCat').required = true;
      prepararFormGimnasta();
    } else {
      document.getElementById('formGimnastaCat').required = false;
    }

    limpiarAlerta();
    document.getElementById('modalForm').classList.add('open');
  }

  function abrirFormEditar(u) {
    document.getElementById('userForm').reset();
    document.getElementById('formMode').value     = 'editar';
    document.getElementById('formUserId').value   = u.id;
    document.getElementById('formRol').value      = u.rol;
    document.getElementById('formTitle').textContent = `Editar — ${u.nombre} ${u.apellidos ?? ''}`;
    document.getElementById('formNombre').value   = u.nombre ?? '';
    document.getElementById('formApellidos').value= u.apellidos ?? '';
    document.getElementById('formDni').value      = u.dni ?? '';
    document.getElementById('formEmail').value    = u.email ?? '';
    document.getElementById('formTelefono').value = u.telefono ?? '';
    document.getElementById('formPassword').required = false;
    document.getElementById('pwRequired').style.display = 'none';
    document.getElementById('activoField').style.display = 'block';
    document.getElementById('formActivo').value   = u.activo ? '1' : '0';

    const esEntrenadora = u.rol === 'entrenadora';
    const esGimnasta = u.rol === 'gimnasta';
    
    document.getElementById('fieldsEntrenadora').style.display = esEntrenadora ? 'contents' : 'none';
    document.getElementById('fieldsGimnasta').style.display = esGimnasta ? 'contents' : 'none';
    document.getElementById('formGimnastaCat').required = esGimnasta;

    if (esGimnasta) {
      prepararFormGimnasta(u.gimnasta?.categoria?.id, u.gimnasta?.conjunto?.id);
      document.getElementById('formTelefonoContacto').value = u.gimnasta?.telefono_contacto ?? '';
    }

    if (esEntrenadora && u.entrenador) {
      document.getElementById('formTitulacion').value = u.entrenador.titulacion ?? '';
      document.getElementById('formAniosExp').value   = u.entrenador.anios_experiencia ?? 0;
      document.getElementById('formHorasSem').value   = u.entrenador.horas_semanales ?? 0;
      document.getElementById('formEstado').value     = u.entrenador.estado ?? 'activa';
      document.getElementById('formBiografia').value  = u.entrenador.biografia ?? '';
    }
    limpiarAlerta();
    document.getElementById('modalForm').classList.add('open');
  }

  async function submitUsuario(e) {
    e.preventDefault();
    const btn  = document.getElementById('btnSubmit');
    const modo = document.getElementById('formMode').value;
    const id   = document.getElementById('formUserId').value;
    const rol  = document.getElementById('formRol').value;

    btn.disabled = true;
    btn.textContent = 'Guardando…';
    limpiarAlerta();

    const payload = {
      nombre:    document.getElementById('formNombre').value.trim(),
      apellidos: document.getElementById('formApellidos').value.trim(),
      dni:       document.getElementById('formDni').value.trim().toUpperCase(),
      email:     document.getElementById('formEmail').value.trim() || undefined,
      telefono:  document.getElementById('formTelefono').value.trim() || undefined,
      rol,
    };

    const pw = document.getElementById('formPassword').value;
    if (pw) payload.password = pw;

    if (modo === 'editar') {
      payload.activo = document.getElementById('formActivo').value === '1';
    }

    if (rol === 'entrenadora') {
      payload.titulacion        = document.getElementById('formTitulacion').value.trim() || undefined;
      payload.anios_experiencia = parseInt(document.getElementById('formAniosExp').value) || 0;
      payload.horas_semanales   = parseInt(document.getElementById('formHorasSem').value) || 0;
      payload.estado            = document.getElementById('formEstado').value;
      payload.biografia         = document.getElementById('formBiografia').value.trim() || undefined;
      payload.club_id           = 1;
    }

    if (rol === 'gimnasta') {
      payload.categoria_id = document.getElementById('formGimnastaCat').value;
      payload.conjunto_id  = document.getElementById('formGimnastaConj').value || null;
      payload.telefono_contacto = document.getElementById('formTelefonoContacto').value.trim() || undefined;
      payload.club_id      = 1; // Asumimos club maestra 1
    }

    try {
      if (modo === 'crear') {
        await apiFetch('/usuarios', { method: 'POST', body: JSON.stringify(payload) });
      } else {
        await apiFetch(`/usuarios/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
      }

      document.getElementById('modalForm').classList.remove('open');

      // Recargar vista correspondiente
      if (rol === 'entrenadora') cargarEntrenadoras();
      else if (rol === 'gimnasta') cargarTablaUsuarios('gimnasta', paginaActual.gimnasta);
      else cargarTablaUsuarios('administrador', paginaActual.administrador);

    } catch (err) {
      const msgs = err.errors ? Object.values(err.errors).flat().join(' | ') : (err.message ?? 'Error inesperado.');
      mostrarAlerta(msgs, 'error');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar';
    }
  }

  async function eliminarUsuario(id, label) {
    if (!confirm(`¿Deseas desactivar ${label}? (borrado lógico)`)) return;
    try {
      await apiFetch(`/usuarios/${id}`, { method: 'DELETE' });
      // Recargar vista activa
      const vActiva = document.querySelector('.view.active')?.id;
      if (vActiva === 'view-equipo')    cargarEntrenadoras();
      if (vActiva === 'view-gimnastas') cargarTablaUsuarios('gimnasta', paginaActual.gimnasta);
      if (vActiva === 'view-admins')    cargarTablaUsuarios('administrador', paginaActual.administrador);
    } catch (err) {
      alert(err.message ?? 'No se pudo eliminar el usuario.');
    }
  }

  /* ════════════════════════════════════════════════════
   * Alertas formulario
   * ════════════════════════════════════════════════════ */
  function mostrarAlerta(msg, tipo) {
    const el = document.getElementById('formAlert');
    el.textContent = msg;
    el.className   = `alert-banner alert-${tipo}`;
  }
  function limpiarAlerta() {
    const el = document.getElementById('formAlert');
    el.className = 'alert-banner';
    el.textContent = '';
  }

  /* ════════════════════════════════════════════════════
   * Modal helper
   * ════════════════════════════════════════════════════ */
  function cerrarModal(id, event) {
    if (!event || event.target.classList.contains('modal-overlay')) {
      document.getElementById(id).classList.remove('open');
    }
  }

  /* ════════════════════════════════════════════════════
   * Logout
   * ════════════════════════════════════════════════════ */
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

  /* ════════════════════════════════════════════════════
   * GRUPOS Y CLASES
   * ════════════════════════════════════════════════════ */
  let grupoActualActivo = null;
  let gimnastasDisponibles = [];

  async function cargarGrupos() {
    const grid = document.getElementById('gruposGrid');
    grid.innerHTML = `<div class="loading-state"><div class="loading-spinner"></div><p>Cargando grupos…</p></div>`;

    try {
      const data = await apiFetch('/conjuntos');
      const lista = data.data ?? [];

      if (!lista.length) {
        grid.innerHTML = `<div class="empty-state"><div class="empty-icon">🏆</div><div class="empty-title">Sin Grupos</div><div class="empty-desc">Aún no hay grupos creados.</div></div>`;
        return;
      }

      grid.innerHTML = lista.map(g => `
        <div class="team-card">
          <div class="card-avatar" style="font-size:1.5rem">🏆</div>
          <div class="card-name">${g.nombre}</div>
          <div class="card-role">${g.categoria?.nombre ?? 'Sin categoría'}</div>
          <div class="card-club">${g.horario ?? 'Sin horario definido'}</div>
          <div class="card-stats">
            <div class="stat">
              <span class="stat-val">${g.total_gimnastas ?? 0}</span>
              <span class="stat-label">Gimnastas</span>
            </div>
            <div class="stat">
              <span class="stat-val">${g.total_entrenadores ?? 0}</span>
              <span class="stat-label">Entrenadoras</span>
            </div>
          </div>
          <div class="card-actions">
            <button class="btn-primary" style="flex:1" onclick='abrirGestionGrupo(${g.id})'>Gestionar Alumnas</button>
          </div>
        </div>
      `).join('');
    } catch (err) {
      grid.innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><div class="empty-title">Error</div><div class="empty-desc">${err.message ?? 'Carga fallida.'}</div></div>`;
    }
  }

  async function abrirGestionGrupo(id) {
    document.getElementById('modalGrupo').classList.add('open');
    document.getElementById('mgTbodyGimnastas').innerHTML = `<tr><td colspan="3" style="text-align:center">Cargando...</td></tr>`;
    document.getElementById('mgAlert').className = 'alert-banner';
    document.getElementById('mgAlert').textContent = '';

    try {
      // Fetch completo del grupo (gimnastas incluidas)
      const resp = await apiFetch(`/conjuntos/${id}`);
      const g = resp.data;
      grupoActualActivo = g;

      document.getElementById('mgTitle').textContent = g.nombre;
      document.getElementById('mgCategoria').textContent = 'Categoría: ' + (g.categoria?.nombre ?? '');
      document.getElementById('mgTotal').textContent = g.gimnastas?.length ?? 0;

      renderTablaGimnastasGrupo(g.gimnastas ?? []);

      // Fetch de TODAS las gimnastas para filtrarlas
      const resGim = await apiFetch('/usuarios?rol=gimnasta&per_page=1000');
      const allGimnastas = resGim.data ?? [];

      // Filtramos: 
      // 1. Misma categoría que el grupo
      // 2. Que NO estén ya en este grupo
      gimnastasDisponibles = allGimnastas.filter(u => 
        u.gimnasta?.categoria?.id === g.categoria?.id && 
        u.gimnasta?.conjunto?.id !== g.id
      );

      const sel = document.getElementById('mgSelectGimnasta');
      sel.innerHTML = '<option value="">Selecciona una gimnasta para añadir...</option>';
      if (gimnastasDisponibles.length === 0) {
        sel.innerHTML = '<option value="">No hay gimnastas disponibles de esta categoría</option>';
      } else {
        gimnastasDisponibles.forEach(u => {
          const groupInfo = u.gimnasta?.conjunto?.nombre ? ` (Actual: ${u.gimnasta.conjunto.nombre})` : ' (Sin grupo)';
          sel.innerHTML += `<option value="${u.gimnasta?.id}">${u.nombre} ${u.apellidos ?? ''}${groupInfo}</option>`;
        });
      }

    } catch (err) {
      document.getElementById('mgTbodyGimnastas').innerHTML = `<tr><td colspan="3" style="color:var(--error)">Error al cargar la información del grupo.</td></tr>`;
    }
  }

  function renderTablaGimnastasGrupo(gimnastas) {
    const tb = document.getElementById('mgTbodyGimnastas');
    if (!gimnastas.length) {
      tb.innerHTML = `<tr><td colspan="3" style="text-align:center;color:var(--muted)">Sin gimnastas asignadas.</td></tr>`;
      return;
    }
    tb.innerHTML = gimnastas.map(g => `
      <tr>
        <td><strong>${g.nombre} ${g.apellidos ?? ''}</strong></td>
        <td>${g.numero_licencia ?? '–'}</td>
        <td>
          <button class="btn-danger" style="padding: 0.3rem 0.6rem" onclick="quitarGimnastaDeGrupo(${g.id})">Quitar</button>
        </td>
      </tr>
    `).join('');
  }

  async function asignarGimnastaAlGrupo() {
    if (!grupoActualActivo) return;
    const gId = document.getElementById('mgSelectGimnasta').value;
    if (!gId) {
      mostrarAlertaMg('Por favor, selecciona una gimnasta.', 'error');
      return;
    }

    try {
      await apiFetch(`/conjuntos/${grupoActualActivo.id}/gimnastas`, {
        method: 'POST',
        body: JSON.stringify({ gimnasta_id: parseInt(gId) })
      });
      mostrarAlertaMg('Gimnasta añadida con éxito.', 'success');
      abrirGestionGrupo(grupoActualActivo.id); // Recargar
      cargarGrupos(); // Actualizar listado de grupos de fondo
    } catch (err) {
      const msgs = err.message ?? (err.errors ? Object.values(err.errors).flat().join(', ') : 'Error al asignar');
      mostrarAlertaMg(msgs, 'error');
    }
  }

  async function quitarGimnastaDeGrupo(gimnastaId) {
    if (!grupoActualActivo) return;
    if (!confirm('¿Seguro que deseas quitar a esta gimnasta del grupo? (Quedará sin grupo asignado)')) return;

    try {
      await apiFetch(`/conjuntos/${grupoActualActivo.id}/gimnastas/${gimnastaId}`, {
        method: 'DELETE'
      });
      mostrarAlertaMg('Gimnasta desvinculada del grupo.', 'success');
      abrirGestionGrupo(grupoActualActivo.id); // Recargar
      cargarGrupos(); // Actualizar listado
    } catch (err) {
      mostrarAlertaMg(err.message ?? 'Error al desasignar.', 'error');
    }
  }

  function mostrarAlertaMg(msg, tipo) {
    const el = document.getElementById('mgAlert');
    el.textContent = msg;
    el.className = 'alert-banner alert-' + tipo;
  }

  /* ════════════════════════════════════════════════════
   * Inicialización
   * ════════════════════════════════════════════════════ */
  /* ════════════════════════════════════════════════════
   * Selecters dependent (Gimnastas)
   * ════════════════════════════════════════════════════ */
  function prepararFormGimnasta(catId = null, conjId = null) {
    const selCat = document.getElementById('formGimnastaCat');
    selCat.innerHTML = '<option value="">Selecciona una categoría...</option>';
    _categoriasGlobales.forEach(c => {
      selCat.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
    });

    if (catId) {
      selCat.value = catId;
    }
    actualizarSelectConjuntos(conjId);
  }

  function actualizarSelectConjuntos(conjId = null) {
    const catId = document.getElementById('formGimnastaCat').value;
    const selConj = document.getElementById('formGimnastaConj');
    selConj.innerHTML = '<option value="">Sin asignar a grupo</option>';

    if (!catId) return;

    const gruposCompatibles = _conjuntosGlobales.filter(c => c.categoria_id == catId || c.categoria?.id == catId);
    gruposCompatibles.forEach(c => {
      selConj.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
    });

    if (conjId && typeof conjId !== 'object') {
      selConj.value = conjId;
    }
  }

  /* ════════════════════════════════════════════════════
   * Modal Competiciones
   * ════════════════════════════════════════════════════ */
  function abrirFormCompeticion() {
    document.getElementById('competicionForm').reset();
    document.getElementById('compAlert').className = 'alert-banner';
    document.getElementById('compAlert').textContent = '';
    
    // Rellenamos el selector de conjuntos
    const sel = document.getElementById('compConjunto');
    sel.innerHTML = '<option value="">Selecciona un grupo...</option>';
    _conjuntosGlobales.forEach(c => {
      sel.innerHTML += `<option value="${c.id}">${c.nombre} (Cat: ${c.categoria?.nombre ?? '-'})</option>`;
    });

    document.getElementById('modalCompeticion').classList.add('open');
  }

  async function guardarCompeticion(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitComp');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
      nombre: document.getElementById('compNombre').value.trim(),
      conjunto_id: document.getElementById('compConjunto').value,
      fecha: document.getElementById('compFecha').value
    };

    try {
      await apiFetch('/competiciones', {
        method: 'POST',
        body: JSON.stringify(payload)
      });
      document.getElementById('modalCompeticion').classList.remove('open');
      
      // Recargar el calendario si está visible
      if (document.getElementById('view-calendario').classList.contains('active')) {
        calendarInstance.destroy();
        calendarInstance = null;
        initCalendar();
      }
    } catch (err) {
      const el = document.getElementById('compAlert');
      el.textContent = err.message ?? 'Error al guardar la competición.';
      el.className = 'alert-banner alert-error';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar Competición';
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

  cargarEntrenadoras();
</script>
</body>
</html>