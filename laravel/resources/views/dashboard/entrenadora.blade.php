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
  @if(env('GOOGLE_MAPS_API_KEY') && env('GOOGLE_MAPS_API_KEY') !== 'vuestra_maps_key_aca')
  <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places" async defer></script>
  @endif
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
      content: '';
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
    .btn-primary { background: linear-gradient(135deg, var(--burgundy), var(--rose)); color: var(--white); padding: 0.8rem 1.5rem; border-radius: var(--radius-md); border: none; font-family: 'DM Sans', sans-serif; font-weight: 500; cursor: pointer; transition: opacity 0.3s, transform 0.2s; display: inline-block; text-align: center; text-decoration: none; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

    .btn-outline { background: transparent; border: 1.5px solid var(--blush); color: var(--text); padding: 0.6rem 1.2rem; border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; width: 100%; }
    .btn-outline:hover { border-color: var(--burgundy); color: var(--burgundy); background-color: var(--cream); }

    /* === INFO PERFIL === */
    .perfil-card { background: var(--white); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .perfil-title { font-family: 'Cormorant Garamond', serif; color: var(--burgundy); font-size: 1.5rem; margin-bottom: 1.5rem; }
    .perfil-row { display: flex; gap: 0.75rem; align-items: baseline; padding: 0.75rem 0; border-bottom: 1px solid var(--blush); }
    .perfil-row:last-child { border-bottom: none; }
    .perfil-label { color: var(--muted); font-size: 0.85rem; min-width: 160px; }
    .perfil-value { color: var(--text); font-weight: 500; }

    /* === MAP & COMPETITION DETAIL === */
    #map-ent { height: 300px; width: 100%; border-radius: var(--radius-md); margin-top: 1rem; border: 1px solid var(--blush); }
    .competition-detail { margin-top: 1.5rem; display: none; padding: 2rem; background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--blush); }
    .competition-detail.visible { display: block; }
    .comp-meta { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
    .comp-meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--muted); }
    .comp-meta-item strong { color: var(--text); }
    .comp-location-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--rose); text-decoration: none; font-size: 0.85rem; font-weight: 500; margin-top: 0.5rem; transition: color 0.2s; }
    .comp-location-link:hover { color: var(--burgundy); }
    .map-unavailable { display: flex; align-items: center; justify-content: center; height: 100px; background: var(--cream); border-radius: var(--radius-md); border: 1px dashed var(--blush); color: var(--muted); font-size: 0.9rem; margin-top: 1rem; }

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
    .td-actions { display: flex; gap: 0.5rem; }

    /* === MENSAJES === */
    .mensaje-lista { display: flex; flex-direction: column; }
    .mensaje-item { padding: 1.5rem; border-bottom: 1px solid var(--blush); cursor: pointer; transition: background 0.2s; position: relative; }
    .mensaje-item:hover { background: var(--cream); }
    .mensaje-item.active { background: var(--cream); border-left: 4px solid var(--burgundy); }
    .mensaje-item.unread::before { content: ''; position: absolute; right: 1rem; top: 1.5rem; width: 8px; height: 8px; background: var(--rose); border-radius: 50%; }
    .msg-emisor { font-weight: 600; font-size: 0.95rem; color: var(--text); margin-bottom: 0.25rem; display: block; }
    .msg-asunto { font-size: 0.85rem; color: var(--burgundy); font-weight: 500; margin-bottom: 0.5rem; display: block; }
    .msg-snippet { font-size: 0.8rem; color: var(--muted); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-fecha { font-size: 0.75rem; color: var(--muted); margin-top: 0.5rem; display: block; text-align: right; }

    /* === FORM FIELDS === */
    .form-group { margin-bottom: 1rem; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { display: block; font-size: 0.82rem; font-weight: 500; color: var(--text); margin-bottom: 0.4rem; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 0.7rem 1rem; border: 1.5px solid var(--blush); border-radius: var(--radius-md); font-family: 'DM Sans', sans-serif; font-size: 0.9rem; color: var(--text); background: var(--cream); outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--rose); background: var(--white); box-shadow: 0 0 0 3px rgba(196,92,126,.12); }
    .form-textarea { resize: vertical; min-height: 100px; }

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
      <button class="nav-link active" id="nav-panel"      onclick="showView('panel')">Mi Panel</button>
      <button class="nav-link"        id="nav-grupos"     onclick="showView('grupos')">Mis Grupos</button>
      <button class="nav-link"        id="nav-gimnastas"  onclick="showView('gimnastas')">Mis Gimnastas</button>
      <button class="nav-link"        id="nav-mensajes"   onclick="showView('mensajes')">Mensajes Padres</button>
      <button class="nav-link"        id="nav-calendario" onclick="showView('calendario')">Calendario</button>
    </nav>
    <div class="user-profile">
      <div class="avatar" id="sidebarAvatar">E</div>
      <div class="user-info">
        <div class="user-name" id="sidebarName">Entrenadora</div>
        <div class="user-role">Entrenadora</div>
      </div>
      <button class="logout-btn" onclick="logout()" title="Cerrar sesión" style="font-size: 0.9rem; font-weight: 500;">Salir</button>
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
        <div class="welcome-title">¡Hola, <span id="welcomeName">entrenadora</span>!</div>
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

    <!-- ── VISTA: MENSAJES ────────────────────────────────────── -->
    <div id="view-mensajes" class="view">
      <header class="header">
        <div>
          <h1 class="page-title">Bandeja de Entrada</h1>
          <p class="page-subtitle">Mensajes enviados por los padres de tus gimnastas</p>
        </div>
        <button class="btn-primary" onclick="abrirModalMensajeAdmin()">Contactar Administración</button>
      </header>

      <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start;">
        <!-- Lista de mensajes -->
        <div class="table-wrap" style="height: 600px; overflow-y: auto;">
          <div id="mensajeLista" class="mensaje-lista">
            <div class="loading-state"><div class="loading-spinner"></div></div>
          </div>
        </div>

        <!-- Detalle del mensaje -->
        <div id="mensajeDetalle" class="perfil-card" style="min-height: 400px; display: none;">
          <div id="mensajeContenido">
            <h3 id="detAsunto" style="font-family:'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--burgundy); margin-bottom: 0.5rem;">Asunto</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-size: 0.9rem; color: var(--muted);">
              <span id="detEmisor">De: -</span>
              <span id="detFecha">-</span>
            </div>
            <div id="detTexto" style="line-height: 1.6; margin-bottom: 2rem; white-space: pre-wrap;">Contenido...</div>
            
            <hr style="border: none; border-top: 1px solid var(--blush); margin-bottom: 2rem;">
            
            <h4 style="font-size: 1rem; color: var(--burgundy); margin-bottom: 1rem;">Responder mensaje</h4>
            <textarea id="resContenido" class="form-textarea" placeholder="Escribe tu respuesta aquí..." style="margin-bottom: 1rem;"></textarea>
            <button class="btn-primary" onclick="enviarRespuesta()">Enviar Respuesta</button>
          </div>
        </div>
        
        <div id="mensajePlaceholder" class="perfil-card" style="height: 400px; display: flex; align-items: center; justify-content: center; color: var(--muted); font-style: italic;">
          Selecciona un mensaje para leerlo
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
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyGimnastasTabla">
            <!-- Cargado dinámicamente -->
          </tbody>
        </table>
      </div>
      <div class="pagination" id="pagGimnastas" style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.5rem;"></div>
    </div>
...
  <!-- ── MODAL NUEVO MENSAJE ─────────────────────────────────── -->
  <div class="modal-overlay" id="modalMensaje" onclick="cerrarModal('modalMensaje', event)">
    <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 500px;">
      <h2 class="modal-name" style="margin-bottom: 1.5rem;">Enviar mensaje a <span id="nmDestinatario">...</span></h2>
      
      <div class="form-group">
        <label class="form-label">Asunto</label>
        <input type="text" id="nmAsunto" class="form-input" placeholder="Ej: Falta de asistencia, Equipación...">
      </div>
      
      <div class="form-group">
        <label class="form-label">Mensaje</label>
        <textarea id="nmContenido" class="form-textarea" style="height: 150px;" placeholder="Escribe tu mensaje aquí..."></textarea>
      </div>

      <div class="modal-actions" style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button class="btn-outline" style="flex:1" onclick="document.getElementById('modalMensaje').classList.remove('open')">Cancelar</button>
        <button class="btn-primary" style="flex:1" onclick="enviarMensajeNuevo()">Enviar Mensaje</button>
      </div>
    </div>
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

      <!-- Detalle de competición -->
      <div id="comp-detail-ent" class="competition-detail">
        <h2 class="perfil-title" id="comp-title-ent">Nombre Competición</h2>
        <div class="comp-meta">
          <div class="comp-meta-item">📅 <span id="comp-fecha-ent">–</span></div>
          <div class="comp-meta-item" id="comp-hora-wrap-ent" style="display:none">🕐 <strong id="comp-hora-ent">–</strong></div>
          <div class="comp-meta-item" id="comp-dir-wrap-ent" style="display:none">📍 <span id="comp-dir-ent">–</span></div>
        </div>
        <a id="comp-maps-link-ent" href="#" target="_blank" rel="noopener" class="comp-location-link" style="display:none">
          🗺️ Abrir en Google Maps
        </a>
        <div id="map-ent"></div>
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
              <th>Teléfono</th>
              <th>Acciones</th>
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
    
    if (name === 'grupos') cargarGrupos();
    if (name === 'gimnastas') cargarGimnastas();
    if (name === 'mensajes') cargarMensajes();
    if (name === 'calendario') initCalendar();
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
          <div class="card-avatar">${(g.nombre?.[0] ?? 'C').toUpperCase()}</div>
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
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center">Cargando...</td></tr>';
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
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center">No hay gimnastas en este grupo.</td></tr>';
      } else {
        tbody.innerHTML = gimnastas.map(g => `
          <tr>
            <td><strong>${g.nombre} ${g.apellidos ?? ''}</strong></td>
            <td>${g.numero_licencia ?? '–'}</td>
            <td><a href="tel:${g.telefono_contacto ?? ''}" style="color:var(--burgundy); font-weight:600; text-decoration:none">${g.telefono_contacto ?? 'Sin teléfono'}</a></td>
            <td>
               <button class="btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" onclick="abrirModalMensaje(${g.user?.id}, '${g.nombre} ${g.apellidos ?? ''}')">Mensaje</button>
            </td>
          </tr>
        `).join('');
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:red">Error al cargar datos.</td></tr>';
    }
  }

  async function cargarGimnastas(pagina = 1) {
    const tbody = document.getElementById('tbodyGimnastasTabla');
    const search = document.getElementById('searchGimnastas').value;
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">Cargando...</td></tr>';

    try {
      let url = `${API}/usuarios?rol=gimnasta&page=${pagina}`;
      if (search) url += `&search=${encodeURIComponent(search)}`;
      if (entrenadorId) url += `&entrenador_id=${entrenadorId}`;

      const res = await fetch(url, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const lista = data.data ?? [];

      if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">No tienes gimnastas asignadas.</td></tr>';
      } else {
        tbody.innerHTML = lista.map(u => `
          <tr>
            <td><strong>${u.nombre} ${u.apellidos ?? ''}</strong></td>
            <td>${u.dni ?? '–'}</td>
            <td><span class="badge" style="background:var(--cream); color:var(--burgundy); border:1px solid var(--blush); padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem;">${u.gimnasta?.categoria?.nombre ?? '–'}</span></td>
            <td>${u.gimnasta?.conjunto?.nombre ?? '<small style="color:var(--muted)">Sin Asignar</small>'}</td>
            <td><a href="tel:${u.gimnasta?.telefono_contacto ?? ''}" style="color:var(--burgundy); font-weight:600; text-decoration:none">${u.gimnasta?.telefono_contacto ?? 'Sin teléfono'}</a></td>
            <td>
              <button class="btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="abrirModalMensaje(${u.id}, '${u.nombre} ${u.apellidos ?? ''}')">Mensaje</button>
            </td>
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
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:red">Error al cargar gimnastas.</td></tr>';
    }
  }

  let nmDestinatarioId = null;
  function abrirModalMensaje(id, nombre) {
    nmDestinatarioId = id;
    document.getElementById('nmDestinatario').textContent = nombre;
    document.getElementById('nmAsunto').value = '';
    document.getElementById('nmContenido').value = '';
    document.getElementById('modalMensaje').classList.add('open');
  }

  async function abrirModalMensajeAdmin() {
    try {
      const res = await fetch(`${API}/usuarios-por-rol/administrador`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const admins = await res.json();
      const listAdmins = admins.data ?? admins;
      if (listAdmins && listAdmins.length > 0) {
        const admin = listAdmins[0]; // Seleccionamos el primer administrador disponible
        abrirModalMensaje(admin.id, `Administración (${admin.nombre})`);
      } else {
        alert('No hay administradores disponibles en este momento.');
      }
    } catch (e) {
      alert('Error al obtener la información de administración.');
    }
  }

  async function enviarMensajeNuevo() {
    const asunto = document.getElementById('nmAsunto').value.trim();
    const contenido = document.getElementById('nmContenido').value.trim();
    
    if (!contenido || !nmDestinatarioId) {
      alert('Por favor, escribe el contenido del mensaje.');
      return;
    }

    try {
      const res = await fetch(`${API}/mensajes`, {
        method: 'POST',
        headers: { 
          'Authorization': `Bearer ${token}`, 
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          receptor_id: nmDestinatarioId,
          asunto: asunto || 'Sin asunto',
          contenido: contenido
        })
      });

      if (res.ok) {
        alert('Mensaje enviado con éxito');
        document.getElementById('modalMensaje').classList.remove('open');
      } else {
        alert('Error al enviar el mensaje');
      }
    } catch (e) {
      alert('Error de conexión');
    }
  }

  let selectedMsg = null;
  async function cargarMensajes() {
    const lista = document.getElementById('mensajeLista');
    lista.innerHTML = '<div class="loading-state"><div class="loading-spinner"></div></div>';
    try {
      const res = await fetch(`${API}/mensajes`, {
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      });
      const data = await res.json();
      
      if (!data.length) {
        lista.innerHTML = '<div style="padding:2rem; text-align:center; color:var(--muted)">No tienes mensajes nuevos</div>';
        return;
      }

      lista.innerHTML = data.map(m => `
        <div class="mensaje-item ${m.leido_at ? '' : 'unread'} ${selectedMsg?.id === m.id ? 'active' : ''}" onclick='verMensaje(${JSON.stringify(m)})'>
          <span class="msg-emisor">${m.emisor?.nombre} ${m.emisor?.apellidos ?? ''}</span>
          <span class="msg-asunto">${m.asunto}</span>
          <span class="msg-snippet">${m.contenido}</span>
          <span class="msg-fecha">${new Date(m.created_at).toLocaleDateString()}</span>
        </div>
      `).join('');
    } catch (e) {
      lista.innerHTML = '<div style="padding:2rem; text-align:center; color:var(--error)">Error al cargar mensajes</div>';
    }
  }

  function verMensaje(m) {
    selectedMsg = m;
    document.getElementById('mensajePlaceholder').style.display = 'none';
    document.getElementById('mensajeDetalle').style.display = 'block';
    
    document.getElementById('detAsunto').textContent = m.asunto;
    document.getElementById('detEmisor').textContent = `De: ${m.emisor?.nombre} ${m.emisor?.apellidos ?? ''}`;
    document.getElementById('detFecha').textContent = new Date(m.created_at).toLocaleString();
    document.getElementById('detTexto').textContent = m.contenido;
    document.getElementById('resContenido').value = '';

    // Actualizar lista para marcar como activo
    document.querySelectorAll('.mensaje-item').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    // Marcar como leído si no lo está
    if (!m.leido_at) {
      fetch(`${API}/mensajes/${m.id}/marcar-leido`, {
        method: 'PATCH',
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
      }).then(() => {
        m.leido_at = new Date();
        cargarMensajes();
      });
    }
  }

  async function enviarRespuesta() {
    const contenido = document.getElementById('resContenido').value.trim();
    if (!contenido || !selectedMsg) return;

    try {
      const res = await fetch(`${API}/mensajes`, {
        method: 'POST',
        headers: { 
          'Authorization': `Bearer ${token}`, 
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          receptor_id: selectedMsg.emisor_id,
          asunto: `RE: ${selectedMsg.asunto}`,
          contenido: contenido
        })
      });

      if (res.ok) {
        alert('Respuesta enviada con éxito');
        document.getElementById('resContenido').value = '';
      } else {
        alert('Error al enviar la respuesta');
      }
    } catch (e) {
      alert('Error de conexión');
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
  let _compDataEnt = [];
  function initCalendar() {
    if (calendarInstance) return;
    const calendarEl = document.getElementById('calendar');
    fetch(`${API}/competiciones`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
      _compDataEnt = data;
      const events = data.map(c => ({
        id: c.id,
        title: c.nombre + (c.conjuntos && c.conjuntos.length > 0 ? ' (' + c.conjuntos.map(cj => cj.nombre).join(', ') + ')' : ''),
        start: c.fecha,
        color: 'var(--burgundy)'
      }));
      calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: events,
        eventClick: function(info) {
          const comp = _compDataEnt.find(c => c.id == info.event.id);
          showCompetitionDetailEnt(comp);
        }
      });
      calendarInstance.render();
    }).catch(() => {
      calendarEl.innerHTML = '<p style="color:red">Error cargando calendario.</p>';
    });
  }

  function showCompetitionDetailEnt(comp) {
    if (!comp) return;
    const detail = document.getElementById('comp-detail-ent');
    detail.classList.add('visible');
    document.getElementById('comp-title-ent').textContent = comp.nombre;

    // Fecha formateada
    const fecha = comp.fecha ? new Date(comp.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long', year:'numeric' }) : '–';
    document.getElementById('comp-fecha-ent').textContent = fecha;

    // Hora
    const horaWrap = document.getElementById('comp-hora-wrap-ent');
    if (comp.hora) {
      document.getElementById('comp-hora-ent').textContent = comp.hora.substring(0, 5) + ' h';
      horaWrap.style.display = 'flex';
    } else {
      horaWrap.style.display = 'none';
    }

    // Dirección
    const dir = comp.direccion ?? comp.lugar ?? null;
    const dirWrap = document.getElementById('comp-dir-wrap-ent');
    if (dir) {
      document.getElementById('comp-dir-ent').textContent = dir;
      dirWrap.style.display = 'flex';
    } else {
      dirWrap.style.display = 'none';
    }

    // Enlace a Google Maps
    const mapsLink = document.getElementById('comp-maps-link-ent');
    if (comp.lat && comp.lng) {
      mapsLink.href = `https://www.google.com/maps?q=${comp.lat},${comp.lng}`;
      mapsLink.style.display = 'inline-flex';
    } else if (dir) {
      mapsLink.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(dir)}`;
      mapsLink.style.display = 'inline-flex';
    } else {
      mapsLink.style.display = 'none';
    }

    // Mapa
    const mapEl = document.getElementById('map-ent');
    mapEl.innerHTML = '';
    if (comp.lat && comp.lng && typeof google !== 'undefined') {
      const pos = { lat: parseFloat(comp.lat), lng: parseFloat(comp.lng) };
      const map = new google.maps.Map(mapEl, {
        zoom: 15,
        center: pos,
        mapTypeControl: false,
        streetViewControl: false,
        styles: [
          { featureType:'poi', elementType:'labels', stylers:[{visibility:'off'}] }
        ]
      });
      new google.maps.Marker({
        position: pos,
        map: map,
        title: comp.nombre,
        animation: google.maps.Animation.DROP
      });
    } else if (dir) {
      mapEl.innerHTML = `<div class="map-unavailable">📍 ${dir}</div>`;
    } else {
      mapEl.innerHTML = '<div class="map-unavailable">Ubicación no disponible</div>';
    }

    detail.scrollIntoView({ behavior: 'smooth' });
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
