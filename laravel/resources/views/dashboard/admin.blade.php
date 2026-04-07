<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Administrador</title>
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
      --accent-gold: #C0A080;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--off-white);
      color: var(--text);
      min-height: 100vh;
    }

    .layout { display: flex; min-height: 100vh; }

    /* ── Sidebar ── */
    .sidebar {
      width: 250px;
      background: var(--cream);
      border-right: 1px solid var(--blush);
      display: flex;
      flex-direction: column;
      padding: 2.5rem 1.4rem;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 10;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 3.5rem;
      padding-left: .5rem;
    }

    .sidebar-logo svg {
      width: 36px; 
      height: 36px;
      fill: var(--burgundy);
    }

    .sidebar-logo span {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--burgundy);
    }

    .nav-label {
      font-size: .72rem;
      font-weight: 500;
      color: var(--muted);
      letter-spacing: .12em;
      text-transform: uppercase;
      padding: 0 .8rem;
      margin-bottom: .6rem;
      margin-top: 1.5rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: .8rem;
      padding: .75rem 1.1rem;
      border-radius: 20px;
      font-size: .9rem;
      color: var(--text);
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
      margin-bottom: .3rem;
    }

    .nav-item:hover { color: var(--burgundy); }

    .nav-item.active {
      background: var(--blush);
      color: var(--burgundy);
      font-weight: 600;
    }

    .nav-icon {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }

    .sidebar-footer {
      margin-top: auto;
      border-top: 1px solid var(--blush);
      padding-top: 1.5rem;
    }

    .user-chip {
      display: flex;
      align-items: center;
      gap: .8rem;
      padding: .5rem .3rem;
    }

    .user-avatar {
      width: 40px; height: 40px;
      background: linear-gradient(135deg, var(--burgundy), var(--rose));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      color: white;
      font-size: .95rem;
      font-weight: 600;
      flex-shrink: 0;
      font-family: 'DM Sans', sans-serif;
    }

    .user-info { flex: 1; min-width: 0; }
    .user-name { font-size: .9rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-role { font-size: .8rem; color: var(--muted); }

    .btn-logout-new {
      background: none; border: none; cursor: pointer;
      color: var(--text);
      padding: .5rem; border-radius: 6px;
      transition: all .2s;
      display: flex; align-items: center; justify-content: center;
    }
    .btn-logout-new:hover { color: #D94F4F; background: rgba(217,79,79,.05); }

    /* ── Main ── */
    .main {
      margin-left: 250px;
      flex: 1;
      padding: 3rem;
    }

    .page-header { margin-bottom: 2.5rem; }

    .page-header h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.2rem;
      font-weight: 600;
      color: var(--burgundy);
      line-height: 1.2;
    }

    .page-header p {
      font-size: .92rem;
      color: var(--muted);
      margin-top: .4rem;
    }

    /* ── Controles ── */
    .controls-container {
      display: flex;
      gap: 1rem;
      margin-bottom: 2.5rem;
      align-items: center;
      padding: 1rem;
      background: var(--white);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-soft);
    }

    .search-wrap { position: relative; flex: 1; max-width: 360px; }

    .search-wrap input {
      width: 100%;
      padding: .7rem 1rem .7rem 2.8rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: .9rem;
      color: var(--text);
      background: var(--white);
      outline: none;
      transition: border-color .2s;
    }

    .search-wrap input:focus { border-color: var(--rose); }
    .search-wrap input::placeholder { color: var(--muted); font-size: .88rem; }

    .search-icon-new {
      position: absolute;
      left: 1rem; top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      width: 18px; height: 18px;
    }

    .filter-select {
      padding: .7rem 1.2rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: .9rem;
      color: var(--text);
      background: var(--white);
      outline: none;
      cursor: pointer;
      min-width: 170px;
    }

    .filter-select:focus { border-color: var(--rose); }

    .btn-add-new {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: .6rem;
      padding: .75rem 1.4rem;
      background: var(--burgundy);
      color: white;
      border: none;
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: .9rem;
      font-weight: 500;
      cursor: pointer;
      transition: background .2s;
    }

    .btn-add-new:hover { background: var(--rose); }

    /* ── Estado Vacío ── */
    .empty-state-elegant {
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 5rem 2rem;
      flex: 1;
    }

    .empty-illustration {
      width: 140px;
      height: 140px;
      margin-bottom: 2rem;
      fill: none;
      stroke: var(--accent-gold);
      stroke-width: 1.5;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .empty-state-elegant h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--burgundy);
      margin-bottom: .6rem;
    }

    .empty-state-elegant p {
      font-size: .9rem;
      color: var(--muted);
      max-width: 320px;
      margin-bottom: 1.5rem;
    }

    .test-modal-link {
      color: var(--rose);
      font-size: 0.9rem;
      text-decoration: underline;
      cursor: pointer;
      font-weight: 500;
    }

    /* ── Modal Perfil Detallado ── */
    .modal-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(42,21,32,.6);
      backdrop-filter: blur(5px);
      z-index: 100;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal-overlay.open { display: flex; opacity: 1; }

    .modal {
      background: var(--off-white);
      border-radius: var(--radius-lg);
      width: 100%;
      max-width: 540px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 24px 60px rgba(107,26,58,.2);
      transform: translateY(20px) scale(0.95);
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .modal-overlay.open .modal { transform: translateY(0) scale(1); }

    .modal-header {
      background: var(--burgundy);
      padding: 2.5rem 2rem;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      position: relative;
    }

    .modal-avatar {
      width: 76px; height: 76px;
      border-radius: 50%;
      background: var(--rose);
      border: 3px solid var(--white);
      display: flex; align-items: center; justify-content: center;
      font-size: 2rem;
      font-weight: 600;
      color: white;
      font-family: 'Cormorant Garamond', serif;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .modal-title { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; color: white; line-height: 1.1; }
    .modal-subtitle { font-size: .85rem; color: var(--blush); margin-top: .4rem; font-weight: 500;}

    .modal-body { padding: 2rem; background: var(--white); }

    .modal-section-title {
      font-size: .75rem;
      font-weight: 600;
      color: var(--muted);
      letter-spacing: .12em;
      text-transform: uppercase;
      margin-bottom: 1rem;
      margin-top: 1.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .modal-section-title::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--blush);
    }

    .modal-section-title:first-child { margin-top: 0; }

    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .detail-item {
      background: var(--off-white);
      border-radius: var(--radius-md);
      padding: 1rem 1.2rem;
      border: 1px solid rgba(242, 213, 223, 0.5);
    }

    .detail-label { font-size: .7rem; color: var(--muted); margin-bottom: .3rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 500;}
    .detail-value { font-size: .95rem; font-weight: 500; color: var(--text); }

    .modal-bio {
      background: var(--off-white);
      border-left: 3px solid var(--rose);
      border-radius: 0 var(--radius-md) var(--radius-md) 0;
      padding: 1.2rem 1.5rem;
      font-size: .9rem;
      color: var(--text);
      line-height: 1.6;
      font-style: italic;
    }

    .modal-close-btn {
      display: block;
      width: 100%;
      margin-top: 2rem;
      padding: 1rem;
      background: var(--cream);
      border: 1px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: .95rem;
      font-weight: 500;
      color: var(--burgundy);
      cursor: pointer;
      transition: all .2s;
    }

    .modal-close-btn:hover { background: var(--blush); }

    @media (max-width: 900px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 2rem; }
      .controls-container { flex-wrap: wrap; }
      .btn-add-new { margin-left: 0; width: 100%; justify-content: center; }
      .detail-grid { grid-template-columns: 1fr; }
      .detail-item[style*="grid-column"] { grid-column: auto !important; }
    }
  </style>
</head>
<body>

<div class="layout">

  <aside class="sidebar">
    <div class="sidebar-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
      </svg>
      <span>Rytmia</span>
    </div>

    <div class="nav-label">Principal</div>
    <a class="nav-item active" href="#">
      <svg class="nav-icon" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
      Equipo Técnico
    </a>
    <a class="nav-item" href="#">
      <svg class="nav-icon" viewBox="0 0 24 24">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>
      </svg>
      Gimnastas
    </a>

    <div class="nav-label">Gestión</div>
    <a class="nav-item" href="#">
      <svg class="nav-icon" viewBox="0 0 24 24">
        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97.7l-2.03-1.1c-.5-.28-1-.18-1 .3l-.3 3.6c-.02.22.06.44.22.6.16.16.38.2.6.17l3.7-.6c.26-.03.26-.5.2-.67l-1.3-.9c-.2-.14-.23-.42-.08-.6l2.1-2.4a.5.5 0 0 1 .45-.2h5c.2 0 .4-.1.46-.27l2.1-2.4c.15-.18.12-.46-.08-.6l-1.3-.9c-.18-.12-.22-.38-.2-.67l3.7.6c.22.03.44-.01.6-.17.16-.16.24-.38.22-.6l-.3-3.6c0-.48-.5-.58-1-.3l-2.03 1.1c-.5.28-.97-.15-.97-.7V14.66a2.5 2.5 0 0 0 1.66-2.36V7.5a1.5 1.5 0 0 0-1.5-1.5H16a2 2 0 0 1-2-2h-1c-.55 0-1 .45-1 1a2 2 0 1 1-2 2H8a1.5 1.5 0 0 0-1.5 1.5v4.8c0 1.25.68 2.25 1.66 2.36z"></path>
      </svg>
      Competiciones
    </a>
    <a class="nav-item" href="#">
      <svg class="nav-icon" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>
      </svg>
      Bailes
    </a>
    <a class="nav-item" href="#">
      <svg class="nav-icon" viewBox="0 0 24 24">
        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line>
      </svg>
      Pagos
    </a>

    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="user-avatar" id="sidebarAvatar">A</div>
        <div class="user-info">
          <div class="user-name" id="sidebarName">Admin Rytmia</div>
          <div class="user-role">Administrador</div>
        </div>
        <button class="btn-logout-new" onclick="logout()" title="Cerrar sesión">
          <svg class="nav-icon" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
        </button>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <h1>Equipo Técnico</h1>
      <p>Gestiona las entrenadoras del club — perfiles, experiencia y estado.</p>
    </div>

    <div class="controls-container">
      <div class="search-wrap">
        <svg class="search-icon-new" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="searchInput" placeholder="Buscar por nombre, DNI..." />
      </div>
      <select class="filter-select" id="estadoFilter">
        <option value="">Todos los estados</option>
      </select>
      <button class="btn-add-new" onclick="abrirFormNueva()">
        <svg class="nav-icon" viewBox="0 0 24 24" style="stroke: white; margin-right: 4px;">
          <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nueva entrenadora
      </button>
    </div>

    <div class="empty-state-elegant" id="emptyState">
      <svg class="empty-illustration" viewBox="0 0 24 24">
        <path d="M12 3a2 2 0 1 0 0 4 2 2 0 1 0 0-4z"></path>
        <path d="M12 7v7"></path>
        <path d="M9 10c3 1 6-1 9-4"></path>
        <path d="M12 14c-2 2-4 5-6 8"></path>
        <path d="M12 14c2 2 5 2 8 1"></path>
      </svg>
      <h3>Sin resultados</h3>
      <p>No hay entrenadoras que coincidan con la búsqueda.</p>
      
      <span class="test-modal-link" onclick="abrirModalPerfil()">✨ Ver perfil de prueba</span>
    </div>
  </main>
</div>

<div class="modal-overlay" id="modalOverlay" onclick="cerrarModal(event)">
  <div class="modal" id="modal">
    <div class="modal-header">
      <div class="modal-avatar" id="modalAvatar">M</div>
      <div>
        <div class="modal-title" id="modalNombre">María Gómez</div>
        <div class="modal-subtitle" id="modalClub">📍 Club Rytmia Central</div>
      </div>
    </div>
    <div class="modal-body">
      <div class="modal-section-title">Información de contacto</div>
      <div class="detail-grid">
        <div class="detail-item">
          <div class="detail-label">DNI</div>
          <div class="detail-value" id="modalDni">12345678A</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Teléfono</div>
          <div class="detail-value" id="modalTelefono">+34 600 000 000</div>
        </div>
        <div class="detail-item" style="grid-column: span 2;">
          <div class="detail-label">Email</div>
          <div class="detail-value" id="modalEmail">maria.gomez@rytmia.com</div>
        </div>
      </div>

      <div class="modal-section-title">Experiencia profesional</div>
      <div class="detail-grid">
        <div class="detail-item">
          <div class="detail-label">Titulación</div>
          <div class="detail-value" id="modalTitulacion">Nivel III</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Años exp.</div>
          <div class="detail-value" id="modalAnios">5 años</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Horas semanales</div>
          <div class="detail-value" id="modalHoras">20 h/semana</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Estado</div>
          <div class="detail-value" id="modalEstado">Activa</div>
        </div>
      </div>

      <div class="modal-section-title">Biografía</div>
      <div class="modal-bio" id="modalBio">
        Especialista en aparatos y preparación física. Ha competido a nivel nacional durante 10 años antes de dedicarse a la formación de nuevas gimnastas.
      </div>
      
      <button class="modal-close-btn" onclick="cerrarModal()">Cerrar perfil</button>
    </div>
  </div>
</div>

<script>
  // Script para manejar el comportamiento de la interfaz
  const user = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

  if (user.nombre) {
    document.getElementById('sidebarName').textContent = user.nombre + ' ' + (user.apellidos || '');
    document.getElementById('sidebarAvatar').textContent = user.nombre[0].toUpperCase();
  }

  function abrirFormNueva() {
    alert('Formulario de nueva entrenadora — próximamente.');
  }

  function logout() {
    alert('Cerrando sesión...');
    localStorage.removeItem('rytmia_token');
    localStorage.removeItem('rytmia_user');
    window.location.href = '/';
  }

  // Funciones del Modal de Perfil
  function abrirModalPerfil() {
    document.getElementById('modalOverlay').classList.add('open');
  }

  function cerrarModal(e) {
    // Solo cierra si no hay evento (llamado desde el botón) o si el clic fue en el overlay oscuro
    if (!e || e.target === document.getElementById('modalOverlay')) {
      document.getElementById('modalOverlay').classList.remove('open');
    }
  }
</script>

</body>
</html>