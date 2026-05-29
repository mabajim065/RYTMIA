<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rytmia · Panel Administrador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
  @if(config('services.google_maps.key') && config('services.google_maps.key') !== 'vuestra_maps_key_aca')
    <script>
      // Indica si Google Maps ya terminó de cargar
      window._googleMapsReady = false;
      window._googleMapsCallbacks = [];

      // Google Maps llama a esta función cuando termina de cargar
      function onGoogleMapsLoaded() {
        window._googleMapsReady = true;
        window._googleMapsCallbacks.forEach(fn => fn());
        window._googleMapsCallbacks = [];
      }

      // Ejecuta una función cuando Maps esté listo (o inmediatamente si ya lo está)
      function onGoogleMapsReady(fn) {
        if (window._googleMapsReady) { fn(); } else { window._googleMapsCallbacks.push(fn); }
      }
    </script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=onGoogleMapsLoaded"
      async defer></script>
  @endif

  <style>
    :root {
      --burgundy: #6B1A3A;
      --rose: #C45C7E;
      --blush: #F2D5DF;
      --cream: #FDF6F0;
      --off-white: #FAF6F1;
      --text: #2A1520;
      --muted: #9B7080;
      --white: #ffffff;
      --error: #D94F4F;
      --success: #2e7d32;
      --shadow-soft: 0 10px 30px rgba(107, 26, 58, .08);
      --radius-lg: 20px;
      --radius-md: 14px;
      --badge-activa-bg: #e8f5e9;
      --badge-activa-text: #2e7d32;
      --badge-inactiva-bg: #fff8e1;
      --badge-inactiva-text: #f57f17;
      --badge-baja-bg: #ffebee;
      --badge-baja-text: #c62828;
    }

    @media (prefers-color-scheme: dark) {
      :root {
        --burgundy: #EFA6C0;
        --rose: #D87D9C;
        --blush: #4A2B38;
        --cream: #1F1318;
        --off-white: #140C10;
        --text: #FDF6F0;
        --muted: #A88894;
        --white: #1E1216;
        --error: #EF6E6E;
        --success: #66BB6A;
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.5);
        --badge-activa-bg: #1B3320;
        --badge-activa-text: #81C784;
        --badge-inactiva-bg: #332D1B;
        --badge-inactiva-text: #FFB74D;
        --badge-baja-bg: #331B1B;
        --badge-baja-text: #E57373;
      }
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background-color: var(--off-white);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    #app {
      width: 100%;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
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

    .brand {
      padding: 2rem;
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--burgundy);
      text-align: center;
      border-bottom: 1px solid var(--blush);
    }

    .nav-links {
      flex: 1;
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .nav-link {
      padding: 1rem 1.5rem;
      border-radius: var(--radius-md);
      color: var(--muted);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 1rem;
      cursor: pointer;
      border: none;
      background: none;
      font-size: 1rem;
      width: 100%;
      text-align: left;
    }

    .nav-link:hover,
    .nav-link.active {
      background-color: var(--cream);
      color: var(--burgundy);
    }

    .user-profile {
      padding: 2rem;
      border-top: 1px solid var(--blush);
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .avatar {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--burgundy), var(--rose));
      color: var(--white);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .user-info {
      flex: 1;
      min-width: 0;
    }

    .user-name {
      font-weight: 600;
      font-size: 0.9rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .user-role {
      font-size: 0.8rem;
      color: var(--muted);
    }

    .logout-btn {
      background: none;
      border: none;
      color: var(--muted);
      cursor: pointer;
      font-size: 1.2rem;
      transition: color 0.3s;
    }

    .logout-btn:hover {
      color: var(--error);
    }

    /* === MAIN CONTENT === */
    .main-content {
      flex: 1;
      margin-left: 280px;
      padding: 3rem;
    }

    .view {
      display: none;
    }

    .view.active {
      display: block;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 3rem;
    }

    .page-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.5rem;
      color: var(--burgundy);
    }

    .page-subtitle {
      color: var(--muted);
      margin-top: 0.5rem;
    }

    /* === BUTTONS === */
    .btn-primary {
      background: linear-gradient(135deg, var(--burgundy), var(--rose));
      color: var(--white);
      padding: 0.8rem 1.5rem;
      border-radius: var(--radius-md);
      border: none;
      font-family: 'DM Sans', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: opacity 0.3s, transform 0.2s;
    }

    .btn-primary:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    .btn-outline {
      background: transparent;
      border: 1.5px solid var(--blush);
      color: var(--text);
      padding: 0.6rem 1.2rem;
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: all 0.3s;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
    }

    .btn-outline:hover {
      border-color: var(--burgundy);
      color: var(--burgundy);
      background-color: var(--cream);
    }

    .btn-danger {
      background: transparent;
      border: 1.5px solid rgba(217, 79, 79, .4);
      color: var(--error);
      padding: 0.5rem 1rem;
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: all 0.3s;
      font-size: 0.85rem;
      font-family: 'DM Sans', sans-serif;
    }

    .btn-danger:hover {
      background: #ffebee;
    }

    /* === EQUIPO TÉCNICO — listado === */
    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
    }

    .team-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2rem;
      box-shadow: var(--shadow-soft);
      border: 1px solid var(--blush);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: all 0.3s;
    }

    .team-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(107, 26, 58, .12);
    }

    .card-avatar {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--cream), var(--blush));
      color: var(--burgundy);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 1rem;
      border: 2px solid var(--blush);
      font-weight: 600;
      font-family: 'Cormorant Garamond', serif;
    }

    .card-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      color: var(--text);
      margin-bottom: 0.3rem;
    }

    .card-role {
      font-size: 0.85rem;
      color: var(--rose);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 1rem;
    }

    .card-club {
      font-size: 0.85rem;
      color: var(--muted);
      margin-bottom: 1.5rem;
    }

    .card-stats {
      display: flex;
      gap: 2rem;
      margin-bottom: 1.5rem;
      justify-content: center;
    }

    .stat {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .stat-val {
      font-weight: 700;
      color: var(--burgundy);
      font-size: 1.3rem;
      font-family: 'Cormorant Garamond', serif;
    }

    .stat-label {
      font-size: 0.75rem;
      color: var(--muted);
    }

    .card-actions {
      display: flex;
      gap: 0.75rem;
      width: 100%;
    }

    /* === BADGE ESTADO === */
    .badge {
      display: inline-block;
      padding: 0.2rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-activa {
      background-color: var(--badge-activa-bg);
      color: var(--badge-activa-text);
    }

    .badge-inactiva {
      background-color: var(--badge-inactiva-bg);
      color: var(--badge-inactiva-text);
    }

    .badge-baja {
      background-color: var(--badge-baja-bg);
      color: var(--badge-baja-text);
    }

    /* === LOADING / EMPTY === */
    .loading-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--muted);
    }

    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid var(--blush);
      border-top-color: var(--rose);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 1rem;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
    }

    .empty-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .empty-title {
      font-family: 'Cormorant Garamond', serif;
      color: var(--burgundy);
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .empty-desc {
      color: var(--muted);
    }

    /* === MODAL PERFIL === */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(42, 21, 32, .4);
      backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s;
    }

    .modal-overlay.open {
      opacity: 1;
      pointer-events: auto;
    }

    .modal-content {
      background: var(--white);
      width: 90%;
      max-width: 540px;
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      position: relative;
      transform: translateY(20px);
      transition: transform 0.3s;
      box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-overlay.open .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .modal-avatar {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--cream), var(--blush));
      color: var(--burgundy);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      font-weight: 700;
      font-family: 'Cormorant Garamond', serif;
      flex-shrink: 0;
    }

    .modal-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.6rem;
      color: var(--text);
    }

    .modal-subtitle {
      color: var(--muted);
      font-size: 0.9rem;
    }

    .modal-section {
      margin-bottom: 1.5rem;
    }

    .modal-section-title {
      font-family: 'Cormorant Garamond', serif;
      color: var(--burgundy);
      font-size: 1.2rem;
      margin-bottom: 0.75rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--blush);
    }

    .modal-row {
      display: flex;
      gap: 0.75rem;
      padding: 0.5rem 0;
    }

    .modal-label {
      color: var(--muted);
      font-size: 0.85rem;
      min-width: 140px;
    }

    .modal-value {
      color: var(--text);
      font-weight: 500;
      font-size: 0.95rem;
    }

    .modal-bio {
      color: var(--text);
      line-height: 1.7;
      font-size: 0.95rem;
      font-style: italic;
      background: var(--cream);
      padding: 1rem;
      border-radius: var(--radius-md);
    }

    .modal-close-btn {
      width: 100%;
      padding: 0.8rem;
      background: var(--off-white);
      border: 1px solid var(--blush);
      border-radius: var(--radius-md);
      color: var(--muted);
      cursor: pointer;
      transition: all 0.3s;
      font-family: 'DM Sans', sans-serif;
      margin-top: 1.5rem;
    }

    .modal-close-btn:hover {
      background: var(--blush);
      color: var(--burgundy);
    }

    /* === MODAL USUARIO (crear/editar) === */
    .form-modal {
      max-width: 600px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group.full {
      grid-column: 1 / -1;
    }

    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 500;
      color: var(--text);
      margin-bottom: 0.4rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
      width: 100%;
      padding: 0.7rem 1rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      color: var(--text);
      background: var(--cream);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
      border-color: var(--rose);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(196, 92, 126, .12);
    }

    .form-textarea {
      resize: vertical;
      min-height: 100px;
    }

    .form-section-title {
      font-family: 'Cormorant Garamond', serif;
      color: var(--burgundy);
      font-size: 1.1rem;
      margin: 1rem 0 0.75rem;
      grid-column: 1 / -1;
      border-top: 1px solid var(--blush);
      padding-top: 1rem;
    }

    .modal-actions {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .modal-actions .btn-primary {
      flex: 1;
    }

    .alert-banner {
      padding: 0.75rem 1rem;
      border-radius: var(--radius-md);
      margin-bottom: 1rem;
      font-size: 0.9rem;
      display: none;
    }

    .alert-error {
      background: #ffebee;
      color: var(--error);
      border: 1px solid rgba(217, 79, 79, .3);
      display: block;
    }

    .alert-success {
      background: #e8f5e9;
      color: var(--success);
      border: 1px solid #c8e6c9;
      display: block;
    }

    /* === TABLA GESTIÓN === */
    .table-toolbar {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      align-items: center;
      flex-wrap: wrap;
    }

    .search-input {
      flex: 1;
      min-width: 200px;
      padding: 0.7rem 1rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      background: var(--white);
      outline: none;
    }

    .search-input:focus {
      border-color: var(--rose);
    }

    .filter-select {
      padding: 0.7rem 1rem;
      border: 1.5px solid var(--blush);
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      background: var(--white);
      outline: none;
    }

    .table-wrap {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-soft);
      border: 1px solid var(--blush);
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    table {
      width: 100%;
      min-width: 900px;
      border-collapse: collapse;
    }

    thead {
      background: var(--cream);
    }

    th {
      padding: 0.85rem 1rem;
      text-align: left;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    td {
      padding: 0.85rem 1rem;
      font-size: 0.85rem;
      border-top: 1px solid var(--blush);
      white-space: nowrap;
    }

    tr:hover td {
      background: var(--off-white);
    }

    .td-actions {
      display: flex;
      gap: 0.5rem;
    }

    /* === MENSAJES === */
    .mensaje-lista {
      display: flex;
      flex-direction: column;
    }

    .mensaje-item {
      padding: 1.5rem;
      border-bottom: 1px solid var(--blush);
      cursor: pointer;
      transition: background 0.2s;
      position: relative;
    }

    .mensaje-item:hover {
      background: var(--cream);
    }

    .mensaje-item.active {
      background: var(--cream);
      border-left: 4px solid var(--burgundy);
    }

    .mensaje-item.unread::before {
      content: '';
      position: absolute;
      right: 1rem;
      top: 1.5rem;
      width: 8px;
      height: 8px;
      background: var(--rose);
      border-radius: 50%;
    }

    .msg-emisor {
      font-weight: 600;
      font-size: 0.95rem;
      color: var(--text);
      margin-bottom: 0.25rem;
      display: block;
    }

    .msg-asunto {
      font-size: 0.85rem;
      color: var(--burgundy);
      font-weight: 500;
      margin-bottom: 0.5rem;
      display: block;
    }

    .msg-snippet {
      font-size: 0.8rem;
      color: var(--muted);
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .msg-fecha {
      font-size: 0.75rem;
      color: var(--muted);
      margin-top: 0.5rem;
      display: block;
      text-align: right;
    }

    .msg-badge {
      font-size: 0.7rem;
      padding: 0.1rem 0.4rem;
      border-radius: 4px;
      background: var(--blush);
      color: var(--rose);
      margin-left: 0.5rem;
      font-weight: 600;
    }

    /* === PAGINATION === */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
    }

    .page-btn {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      border: 1px solid var(--blush);
      background: var(--white);
      color: var(--text);
      cursor: pointer;
      font-size: 0.85rem;
      transition: all 0.2s;
    }

    .page-btn:hover,
    .page-btn.active {
      background: var(--burgundy);
      color: var(--white);
      border-color: var(--burgundy);
    }

    /* === NAV MOBILE === */
    .mobile-nav {
      display: none;
      padding: 1rem 1.5rem;
      background: var(--white);
      border-bottom: 1px solid var(--blush);
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 20;
    }

    .mobile-nav .brand {
      padding: 0;
      border: none;
      font-size: 1.8rem;
      text-align: left;
    }

    .menu-btn {
      background: none;
      border: none;
      font-size: 1.8rem;
      color: var(--burgundy);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
    }

    @media (max-width: 768px) {
      .mobile-nav {
        display: flex;
      }

      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-soft);
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
        padding: 1.5rem;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .page-title {
        font-size: 2rem;
      }

      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
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
        <button class="nav-link active" id="nav-equipo" onclick="showView('equipo')">Equipo Técnico</button>
        <button class="nav-link" id="nav-grupos" onclick="showView('grupos')">Grupos / Clases</button>
        <button class="nav-link" id="nav-gimnastas" onclick="showView('gimnastas')">Gimnastas</button>
        <button class="nav-link" id="nav-mensajes" onclick="showView('mensajes')">Historial Mensajes</button>
        <button class="nav-link" id="nav-admins" onclick="showView('admins')">Administradores</button>
        <button class="nav-link" id="nav-calendario" onclick="showView('calendario')">Calendario</button>
      </nav>
      <div class="user-profile">
        <div class="avatar" id="sidebarAvatar">A</div>
        <div class="user-info">
          <div class="user-name" id="sidebarName">Admin</div>
          <div class="user-role">Administrador</div>
        </div>
        <button class="logout-btn" onclick="logout()" title="Cerrar sesión"
          style="font-size: 0.9rem; font-weight: 500;">Salir</button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

      <!--  VISTA: EQUIPO TÉCNICO  -->
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
          <input type="text" id="searchGimnastas" class="search-input" placeholder="Buscar por nombre, DNI…"
            oninput="buscarUsuarios('gimnasta')" />
          <select class="filter-select" id="filterEstadoGimnastas" onchange="buscarUsuarios('gimnasta')">
            <option value="">Todos los estados</option>
            <option value="1">Activas</option>
            <option value="0">Inactivas</option>
          </select>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>DNI</th>
                <th>Categoría</th>
                <th>Grupo / Clase</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tbodyGimnastas"></tbody>
          </table>
        </div>
        <div class="pagination" id="pagGimnastas"></div>
      </div>

      <!--  VISTA: ADMINISTRADORES  -->
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
            <thead>
              <tr>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tbodyAdmins"></tbody>
          </table>
        </div>
      </div>

      <!--  VISTA: CALENDARIO  -->
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


      <!--  VISTA: MENSAJES  -->
      <div id="view-mensajes" class="view">
        <header class="header">
          <div>
            <h1 class="page-title">Supervisión de Mensajes</h1>
            <p class="page-subtitle">Historial completo de comunicaciones del club</p>
          </div>
        </header>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start;">
          <!-- Lista de mensajes -->
          <div class="table-wrap" style="height: 600px; overflow-y: auto;">
            <div id="mensajeLista" class="mensaje-lista">
              <div class="loading-state">
                <div class="loading-spinner"></div>
              </div>
            </div>
          </div>

          <!-- Detalle del mensaje -->
          <div id="mensajeDetalle" class="perfil-card"
            style="min-height: 400px; padding: 2.5rem; display: none; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--blush); box-shadow: var(--shadow-soft);">
            <div id="mensajeContenido">
              <h3 id="detAsunto"
                style="font-family:'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--burgundy); margin-bottom: 0.5rem;">
                Asunto</h3>
              <div
                style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-size: 0.9rem; color: var(--muted); border-bottom: 1px solid var(--blush); padding-bottom: 1rem;">
                <div>
                  <span id="detEmisor" style="display: block;"></span>
                  <span id="detReceptor" style="display: block;"></span>
                </div>
                <span id="detFecha"></span>
              </div>
              <div id="detTexto" style="line-height: 1.6; white-space: pre-wrap; color: var(--text);"></div>

              <hr style="border: none; border-top: 1px solid var(--blush); margin-top: 2rem; margin-bottom: 2rem;">

              <h4 style="font-size: 1rem; color: var(--burgundy); margin-bottom: 1rem;">Responder mensaje</h4>
              <textarea id="resContenido" class="form-textarea" placeholder="Escribe tu respuesta aquí..."
                style="margin-bottom: 1rem;"></textarea>
              <button class="btn-primary" onclick="enviarRespuesta()">Enviar Respuesta</button>
            </div>
          </div>

          <div id="mensajePlaceholder" class="perfil-card"
            style="height: 400px; display: flex; align-items: center; justify-content: center; color: var(--muted); font-style: italic; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--blush);">
            Selecciona un mensaje del historial para verlo detalladamente
          </div>
        </div>
      </div>

    </main>

    <!--  MODAL PERFIL ENTRENADORA  -->
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
          <div class="modal-row"><span class="modal-label">Usuario</span><span class="modal-value"
              id="mpUsername">–</span></div>
          <div class="modal-row"><span class="modal-label">Contraseña</span><span class="modal-value"
              id="mpPasswordTemporal">–</span></div>
          <div class="modal-row"><span class="modal-label">Email</span><span class="modal-value" id="mpEmail">–</span>
          </div>
          <div class="modal-row"><span class="modal-label">Teléfono</span><span class="modal-value"
              id="mpTelefono">–</span></div>
          <div class="modal-row"><span class="modal-label">DNI</span><span class="modal-value" id="mpDni">–</span></div>
          <div class="modal-row"><span class="modal-label">Club</span><span class="modal-value" id="mpClub">–</span>
          </div>
        </div>

        <div class="modal-section">
          <div class="modal-section-title">Experiencia</div>
          <div class="modal-row"><span class="modal-label">Años de experiencia</span><span class="modal-value"
              id="mpAnios">–</span></div>
          <div class="modal-row"><span class="modal-label">Horas semanales</span><span class="modal-value"
              id="mpHoras">–</span></div>
          <div class="modal-row"><span class="modal-label">Estado</span><span class="modal-value" id="mpEstado">–</span>
          </div>
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

    <!--  MODAL GESTIÓN GRUPO  -->
    <div class="modal-overlay" id="modalGrupo" onclick="cerrarModal('modalGrupo', event)">
      <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 700px;">
        <div class="modal-header" style="margin-bottom:1.5rem; justify-content: space-between; align-items: flex-start;">
          <div>
            <h2 class="modal-name" id="mgTitle">Grupo</h2>
            <div class="modal-subtitle" id="mgCategoria">Categoría</div>
          </div>
          <button class="btn-outline" onclick="imprimirListaConjunto(grupoActualActivo?.id)" style="white-space:nowrap">
            Imprimir lista PDF
          </button>
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
          <div class="form-section-title full" style="margin-bottom: 0.5rem">Gimnastas asignadas (<span
              id="mgTotal">0</span>)</div>
          <div class="table-wrap" style="box-shadow:none; border:1px solid var(--blush)">
            <table>
              <thead style="background:var(--off-white)">
                <tr>
                  <th>Nombre Completo</th>
                  <th>Licencia</th>
                  <th style="width: 80px;">Acción</th>
                </tr>
              </thead>
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

    <!--  MODAL CREAR / EDITAR USUARIO  -->
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
            <div class="form-group" style="display:none">
              <label class="form-label" for="formPassword">Contraseña <span id="pwRequired">*</span></label>
              <input class="form-input" id="formPassword" type="password" placeholder="Mín. 8 car., mayús. y números" />
            </div>
            <div class="form-group" id="tempPasswordGroup" style="display:none">
              <label class="form-label">Contraseña autogenerada / temporal</label>
              <input class="form-input" id="formTempPassword" type="text" readonly
                style="background:var(--cream); cursor:not-allowed;" />
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
                <textarea class="form-textarea" id="formBiografia"
                  placeholder="Describe la trayectoria y experiencia de la entrenadora…"></textarea>
              </div>
            </div>

            <!-- Campos Gimnasta -->
            <div id="fieldsGimnasta" style="display:none">
              <div class="form-section-title full">Perfil Gimnasta</div>
              <div class="form-group">
                <label class="form-label" for="formGimnastaCat">Categoría <span
                    style="color:var(--error)">*</span></label>
                <select class="form-select" id="formGimnastaCat" onchange="actualizarSelectConjuntos()"
                  required></select>
              </div>
              <div class="form-group">
                <label class="form-label" for="formGimnastaConj">Grupo / Clase</label>
                <select class="form-select" id="formGimnastaConj">
                  <option value="">Selecciona una categoría primero</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="formGimnastaNacimiento">Fecha de Nacimiento *</label>
                <input class="form-input" id="formGimnastaNacimiento" type="date" onchange="checkGimnastaAge()" />
              </div>
              <div class="form-group">
                <label class="form-label" for="formTelefonoContacto">Teléfono de Contacto (Gimnasta)</label>
                <input class="form-input" id="formTelefonoContacto" type="text" maxlength="20"
                  placeholder="Teléfono de contacto" />
              </div>

              <!-- Tutor legal (sección condicional) -->
              <div id="sectionTutorLegal" style="display:none" class="form-section-title full">Datos del Tutor Legal
              </div>
              <div id="fieldsTutorLegal" style="display:none; contents">
                <div class="form-group">
                  <label class="form-label" for="formTutorNombre">Nombre del Tutor *</label>
                  <input class="form-input" id="formTutorNombre" type="text" />
                </div>
                <div class="form-group">
                  <label class="form-label" for="formTutorApellidos">Apellidos del Tutor *</label>
                  <input class="form-input" id="formTutorApellidos" type="text" />
                </div>
                <div class="form-group">
                  <label class="form-label" for="formTutorEmail">Email del Tutor *</label>
                  <input class="form-input" id="formTutorEmail" type="email" />
                </div>
                <div class="form-group">
                  <label class="form-label" for="formTutorRelacion">Relación / Parentesco *</label>
                  <input class="form-input" id="formTutorRelacion" type="text"
                    placeholder="Ej. Madre, Padre, Tutor legal" />
                </div>
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
            <button type="button" class="btn-outline"
              onclick="document.getElementById('modalForm').classList.remove('open')">Cancelar</button>
            <button type="submit" class="btn-primary" id="btnSubmit">Guardar</button>
          </div>
        </form>
      </div>
    </div>
    <!-- MODAL NUEVA COMPETICION -->
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
            <div class="form-group">
              <label class="form-label" for="compFecha">Fecha *</label>
              <input class="form-input" id="compFecha" type="date" required />
            </div>
            <div class="form-group">
              <label class="form-label" for="compHora">Hora</label>
              <input class="form-input" id="compHora" type="time" />
            </div>
            <div class="form-group full">
              <label class="form-label" for="compEntrenadoras">Entrenadoras asignadas</label>
              <select class="form-select" id="compEntrenadoras" multiple style="height: 100px;">
                <option value="">Cargando entrenadoras...</option>
              </select>
              <small style="color:var(--muted)">Mantén Ctrl (Windows) o Cmd (Mac) para seleccionar varias.</small>
            </div>
            <div class="form-group full">
              <label class="form-label" for="compConjuntos">Grupos / Conjuntos asignados</label>
              <select class="form-select" id="compConjuntos" multiple style="height: 100px;">
                <option value="">Cargando grupos...</option>
              </select>
              <small style="color:var(--muted)">Mantén Ctrl (Windows) o Cmd (Mac) para seleccionar varios.</small>
            </div>
            <div class="form-group full">
              <label class="form-label" for="compGimnastas">Gimnastas asignadas</label>
              <select class="form-select" id="compGimnastas" multiple style="height: 100px;">
                <option value="">Cargando gimnastas...</option>
              </select>
              <small style="color:var(--muted)">Mantén Ctrl (Windows) o Cmd (Mac) para seleccionar varias.</small>
            </div>
            <div class="form-group full">
              <label class="form-label" for="compDireccion">📍 Ubicación / Dirección</label>
              <input class="form-input" id="compDireccion" type="text"
                placeholder="Escribe la dirección del recinto..." />
              <input type="hidden" id="compLat" />
              <input type="hidden" id="compLng" />
              @if(config('services.google_maps.key') && config('services.google_maps.key') !== 'vuestra_maps_key_aca')
                <small style="color: #2e7d32;">✅ Autocompletar de Google Places activo. Selecciona una sugerencia para
                  guardar coordenadas precisas.</small>
              @else
                <small style="color: var(--muted);">ℹ️ Escribe la dirección manualmente. Para activar el autocompletar,
                  configura <code>GOOGLE_MAPS_API_KEY</code> en el <code>.env</code>.</small>
              @endif
            </div>
            <div id="compMapPreview"
              style="height: 200px; margin-top: -0.5rem; margin-bottom: 0.5rem; border-radius: 12px; border: 1px solid var(--blush); overflow: hidden; display: none;">
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-outline"
              onclick="document.getElementById('modalCompeticion').classList.remove('open')">Cancelar</button>
            <button type="submit" class="btn-primary" id="btnSubmitComp">Guardar Competición</button>
          </div>
        </form>
      </div>
    </div>

  </div>

  </div>

  <script>
    /* ***************
     * Configuración
     * ***************** */
    const API = '/api';
    const token = localStorage.getItem('rytmia_token');
    const user = JSON.parse(localStorage.getItem('rytmia_user') || '{}');

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
    document.getElementById('sidebarName').textContent = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
    document.getElementById('sidebarAvatar').textContent = (user.nombre?.[0] ?? 'A').toUpperCase();

    /* ***************************
     * Navegación entre vistas
     * **************************** */
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }

    function showView(name) {
      document.querySelector('.sidebar').classList.remove('open'); // Cerrar en móviles si estaba abierto

      document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      document.getElementById('view-' + name).classList.add('active');
      document.getElementById('nav-' + name).classList.add('active');

      if (name === 'equipo') cargarEntrenadoras();
      if (name === 'grupos') cargarGrupos();
      if (name === 'gimnastas') cargarTablaUsuarios('gimnasta', 1);
      if (name === 'mensajes') cargarMensajes();
      if (name === 'admins') cargarTablaUsuarios('administrador', 1);
      if (name === 'calendario') initCalendar();
    }

    /* *************************
     * Mensajería (Supervisión)
     * ************************* */
    async function cargarMensajes() {
      const lista = document.getElementById('mensajeLista');
      lista.innerHTML = '<div class="loading-state"><div class="loading-spinner"></div><p>Cargando historial...</p></div>';
      try {
        const data = await apiFetch('/mensajes');
        if (!data || !data.length) {
          lista.innerHTML = '<div class="empty-state"><p class="empty-desc">No se han registrado mensajes en el sistema.</p></div>';
          return;
        }

        lista.innerHTML = data.map(m => `
        <div class="mensaje-item" onclick='verMensaje(${JSON.stringify(m)})'>
          <span class="msg-emisor">De: ${m.emisor?.nombre} ${m.emisor?.apellidos ?? ''} 
            <span class="msg-badge">${m.emisor?.rol === 'entrenadora' ? 'Entrenadora' : 'Padre/Tut'}</span>
          </span>
          <span class="msg-asunto">${m.asunto || 'Sin asunto'}</span>
          <span class="msg-snippet">${m.contenido}</span>
          <span class="msg-fecha">${new Date(m.created_at).toLocaleString()}</span>
        </div>
      `).join('');
      } catch (e) {
        lista.innerHTML = '<div class="loading-state"><p style="color:var(--error)">Error al cargar el historial.</p></div>';
      }
    }

    let selectedMsg = null;
    function verMensaje(m) {
      selectedMsg = m;
      document.getElementById('mensajePlaceholder').style.display = 'none';
      const detail = document.getElementById('mensajeDetalle');
      detail.style.display = 'block';

      document.getElementById('detAsunto').textContent = m.asunto || 'Sin asunto';
      document.getElementById('detEmisor').innerHTML = `<strong>Emisor:</strong> ${m.emisor?.nombre} ${m.emisor?.apellidos ?? ''} (${m.emisor?.rol})`;
      document.getElementById('detReceptor').innerHTML = `<strong>Receptor:</strong> ${m.receptor?.nombre} ${m.receptor?.apellidos ?? ''} (${m.receptor?.rol})`;
      document.getElementById('detFecha').textContent = new Date(m.created_at).toLocaleString();
      document.getElementById('detTexto').textContent = m.contenido;
      document.getElementById('resContenido').value = '';

      // Resaltar en la lista
      document.querySelectorAll('.mensaje-item').forEach(el => el.classList.remove('active'));
      event.currentTarget.classList.add('active');
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
            // El admin responde al emisor original
            receptor_id: selectedMsg.emisor_id,
            asunto: `RE: ${selectedMsg.asunto || 'Mensaje'}`,
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

    /* ***************************************
     * Helpers fetch
     * *************************************** */
    async function apiFetch(path, opts = {}) {
      const res = await fetch(API + path, {
        ...opts,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`,
          ...(opts.headers ?? {}),
        },
      });
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    }

    /* ***************************************
     * EQUIPO TÉCNICO — tarjetas entrenadoras
     * *************************************** */
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
          <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 1rem; border-top: 1px solid var(--blush); border-bottom: 1px solid var(--blush); padding: 0.5rem 0; width: 100%;">
            <div>Usuario: <code>${u.username ?? '–'}</code></div>
            <div>Contraseña: <code>${u.password_temporal ?? '–'}</code></div>
          </div>
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
            <button class="btn-outline" onclick='abrirFormEditar(${JSON.stringify(u)})'>Editar</button>
            <button class="btn-danger"  onclick='eliminarUsuario(${u.id}, "la entrenadora")'>Eliminar</button>
          </div>
        </div>
      `).join('');

      } catch (err) {
        grid.innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><div class="empty-title">Error al cargar</div><div class="empty-desc">${err.message ?? 'Inténtalo de nuevo.'}</div></div>`;
      }
    }

    function abrirPerfilEntrenadora(u) {
      document.getElementById('mpAvatar').textContent = (u.nombre?.[0] ?? '?').toUpperCase();
      document.getElementById('mpName').textContent = `${u.nombre} ${u.apellidos ?? ''}`;
      document.getElementById('mpTitulacion').textContent = u.entrenador?.titulacion ?? 'Entrenadora';
      document.getElementById('mpUsername').textContent = u.username ?? '–';
      document.getElementById('mpPasswordTemporal').textContent = u.password_temporal ?? '–';
      document.getElementById('mpEmail').textContent = u.email ?? '–';
      document.getElementById('mpTelefono').textContent = u.telefono ?? '–';
      document.getElementById('mpDni').textContent = u.dni ?? '–';
      document.getElementById('mpClub').textContent = u.entrenador?.club?.nombre ?? '–';
      document.getElementById('mpAnios').textContent = `${u.entrenador?.anios_experiencia ?? 0} años`;
      document.getElementById('mpHoras').textContent = `${u.entrenador?.horas_semanales ?? 0} h/semana`;
      document.getElementById('mpEstado').innerHTML = `<span class="badge badge-${u.entrenador?.estado ?? 'activa'}">${u.entrenador?.estado ?? 'activa'}</span>`;

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

    /* **********************************
     * TABLA USUARIOS (gimnastas / admins)
     * *********************************** */
    let paginaActual = { gimnasta: 1, administrador: 1 };

    async function cargarTablaUsuarios(rol, pagina = 1) {
      paginaActual[rol] = pagina;
      const search = document.getElementById('searchGimnastas')?.value ?? '';
      const activo = document.getElementById('filterEstadoGimnastas')?.value ?? '';
      const tbodyId = rol === 'gimnasta' ? 'tbodyGimnastas' : 'tbodyAdmins';
      const pagId = rol === 'gimnasta' ? 'pagGimnastas' : null;

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
            <td><strong>${u.nombre} ${u.apellidos ?? ''}</strong></td>`;
            if (rol === 'gimnasta') {
              cols += `
            <td><code>${u.username ?? '–'}</code></td>
            <td><code>${u.password_temporal ?? '–'}</code></td>
            <td>${u.dni ?? '–'}</td>
            <td><span class="badge" style="background:var(--cream); color:var(--burgundy); border:1px solid var(--blush)">${u.gimnasta?.categoria?.nombre ?? 'Sin calc'}</span></td>
            <td>${u.gimnasta?.conjunto?.nombre ?? '<small style="color:var(--muted)">Sin Asignar</small>'}</td>`;
            } else {
              cols += `
            <td>${u.dni ?? '–'}</td>
            <td>${u.email ?? '–'}</td>`;
            }
            cols += `
            <td>${u.telefono ?? '–'}</td>
            <td><span class="badge ${u.activo ? 'badge-activa' : 'badge-inactiva'}">${u.activo ? 'Activo' : 'Inactivo'}</span></td>
            <td>
              <div class="td-actions">
                <button class="btn-outline" onclick='abrirFormEditar(${JSON.stringify(u)})'>Editar</button>
                <button class="btn-danger"  onclick='eliminarUsuario(${u.id}, "el usuario")'>Eliminar</button>
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

    /* ********************************
     * CRUD — Formulario crear / editar
     * ****************************** */
    function checkGimnastaAge() {
      const dobInput = document.getElementById('formGimnastaNacimiento');
      const secTutorTitle = document.getElementById('sectionTutorLegal');
      const secTutorFields = document.getElementById('fieldsTutorLegal');

      const tNombre = document.getElementById('formTutorNombre');
      const tApellidos = document.getElementById('formTutorApellidos');
      const tEmail = document.getElementById('formTutorEmail');
      const tRelacion = document.getElementById('formTutorRelacion');

      if (!dobInput || !dobInput.value) {
        if (secTutorTitle) secTutorTitle.style.display = 'none';
        if (secTutorFields) secTutorFields.style.display = 'none';
        if (tNombre) tNombre.required = false;
        if (tApellidos) tApellidos.required = false;
        if (tEmail) tEmail.required = false;
        if (tRelacion) tRelacion.required = false;
        return;
      }

      const birthDate = new Date(dobInput.value);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }

      if (age < 18) {
        if (secTutorTitle) secTutorTitle.style.display = 'block';
        if (secTutorFields) secTutorFields.style.display = 'contents';
        if (tNombre) tNombre.required = true;
        if (tApellidos) tApellidos.required = true;
        if (tEmail) tEmail.required = true;
        if (tRelacion) tRelacion.required = true;
      } else {
        if (secTutorTitle) secTutorTitle.style.display = 'none';
        if (secTutorFields) secTutorFields.style.display = 'none';
        if (tNombre) { tNombre.required = false; tNombre.value = ''; }
        if (tApellidos) { tApellidos.required = false; tApellidos.value = ''; }
        if (tEmail) { tEmail.required = false; tEmail.value = ''; }
        if (tRelacion) { tRelacion.required = false; tRelacion.value = ''; }
      }
    }

    /* ********************************
     * CRUD — Formulario crear / editar
     **********************************/
    function abrirFormUsuario(rol) {
      document.getElementById('userForm').reset();
      document.getElementById('formMode').value = 'crear';
      document.getElementById('formUserId').value = '';
      document.getElementById('formRol').value = rol;
      document.getElementById('formTitle').textContent = `Nueva ${rol === 'entrenadora' ? 'Entrenadora' : rol === 'gimnasta' ? 'Gimnasta' : 'Administradora'}`;
      document.getElementById('pwRequired').style.display = 'none';
      document.getElementById('formPassword').required = false;
      document.getElementById('activoField').style.display = 'none';
      document.getElementById('tempPasswordGroup').style.display = 'none';

      document.getElementById('fieldsEntrenadora').style.display = rol === 'entrenadora' ? 'contents' : 'none';
      document.getElementById('fieldsGimnasta').style.display = rol === 'gimnasta' ? 'contents' : 'none';
      if (rol === 'gimnasta') {
        document.getElementById('formGimnastaCat').required = true;
        document.getElementById('formGimnastaNacimiento').required = true;
        prepararFormGimnasta();
        checkGimnastaAge();
      } else {
        document.getElementById('formGimnastaCat').required = false;
        document.getElementById('formGimnastaNacimiento').required = false;
      }

      limpiarAlerta();
      document.getElementById('modalForm').classList.add('open');
    }

    function abrirFormEditar(u) {
      document.getElementById('userForm').reset();
      document.getElementById('formMode').value = 'editar';
      document.getElementById('formUserId').value = u.id;
      document.getElementById('formRol').value = u.rol;
      document.getElementById('formTitle').textContent = `Editar — ${u.nombre} ${u.apellidos ?? ''}`;
      document.getElementById('formNombre').value = u.nombre ?? '';
      document.getElementById('formApellidos').value = u.apellidos ?? '';
      document.getElementById('formDni').value = u.dni ?? '';
      document.getElementById('formEmail').value = u.email ?? '';
      document.getElementById('formTelefono').value = u.telefono ?? '';
      document.getElementById('formPassword').required = false;
      document.getElementById('pwRequired').style.display = 'none';
      document.getElementById('activoField').style.display = 'block';
      document.getElementById('formActivo').value = u.activo ? '1' : '0';

      if (u.password_temporal) {
        document.getElementById('tempPasswordGroup').style.display = 'block';
        document.getElementById('formTempPassword').value = u.password_temporal;
      } else {
        document.getElementById('tempPasswordGroup').style.display = 'none';
      }

      const esEntrenadora = u.rol === 'entrenadora';
      const esGimnasta = u.rol === 'gimnasta';

      document.getElementById('fieldsEntrenadora').style.display = esEntrenadora ? 'contents' : 'none';
      document.getElementById('fieldsGimnasta').style.display = esGimnasta ? 'contents' : 'none';
      document.getElementById('formGimnastaCat').required = esGimnasta;

      if (esGimnasta) {
        document.getElementById('formGimnastaNacimiento').required = true;
        document.getElementById('formGimnastaNacimiento').value = u.gimnasta?.fecha_nacimiento ? u.gimnasta.fecha_nacimiento.substring(0, 10) : '';
        prepararFormGimnasta(u.gimnasta?.categoria?.id, u.gimnasta?.conjunto?.id);
        document.getElementById('formTelefonoContacto').value = u.gimnasta?.telefono_contacto ?? '';

        checkGimnastaAge();
        if (u.gimnasta?.tutor_legal) {
          document.getElementById('formTutorNombre').value = u.gimnasta.tutor_legal.nombre ?? '';
          document.getElementById('formTutorApellidos').value = u.gimnasta.tutor_legal.apellidos ?? '';
          document.getElementById('formTutorEmail').value = u.gimnasta.tutor_legal.email ?? '';
          document.getElementById('formTutorRelacion').value = u.gimnasta.tutor_legal.relacion ?? '';
        }
      } else {
        document.getElementById('formGimnastaNacimiento').required = false;
      }

      if (esEntrenadora && u.entrenador) {
        document.getElementById('formTitulacion').value = u.entrenador.titulacion ?? '';
        document.getElementById('formAniosExp').value = u.entrenador.anios_experiencia ?? 0;
        document.getElementById('formHorasSem').value = u.entrenador.horas_semanales ?? 0;
        document.getElementById('formEstado').value = u.entrenador.estado ?? 'activa';
        document.getElementById('formBiografia').value = u.entrenador.biografia ?? '';
      }
      limpiarAlerta();
      document.getElementById('modalForm').classList.add('open');
    }

    async function submitUsuario(e) {
      e.preventDefault();
      const btn = document.getElementById('btnSubmit');
      const modo = document.getElementById('formMode').value;
      const id = document.getElementById('formUserId').value;
      const rol = document.getElementById('formRol').value;

      btn.disabled = true;
      btn.textContent = 'Guardando…';
      limpiarAlerta();

      const payload = {
        nombre: document.getElementById('formNombre').value.trim(),
        apellidos: document.getElementById('formApellidos').value.trim(),
        dni: document.getElementById('formDni').value.trim().toUpperCase(),
        email: document.getElementById('formEmail').value.trim() || undefined,
        telefono: document.getElementById('formTelefono').value.trim() || undefined,
        rol,
      };

      const pw = document.getElementById('formPassword').value;
      if (pw) payload.password = pw;

      if (modo === 'editar') {
        payload.activo = document.getElementById('formActivo').value === '1';
      }

      if (rol === 'entrenadora') {
        payload.titulacion = document.getElementById('formTitulacion').value.trim() || undefined;
        payload.anios_experiencia = parseInt(document.getElementById('formAniosExp').value) || 0;
        payload.horas_semanales = parseInt(document.getElementById('formHorasSem').value) || 0;
        payload.estado = document.getElementById('formEstado').value;
        payload.biografia = document.getElementById('formBiografia').value.trim() || undefined;
        payload.club_id = 1;
      }

      if (rol === 'gimnasta') {
        payload.categoria_id = document.getElementById('formGimnastaCat').value;
        payload.conjunto_id = document.getElementById('formGimnastaConj').value || null;
        payload.fecha_nacimiento = document.getElementById('formGimnastaNacimiento').value || undefined;
        payload.telefono_contacto = document.getElementById('formTelefonoContacto').value.trim() || undefined;
        payload.club_id = 1; // Asumimos club maestra 1

        // Verificar si es menor para incluir los campos del tutor legal
        if (payload.fecha_nacimiento) {
          const birthDate = new Date(payload.fecha_nacimiento);
          const today = new Date();
          let age = today.getFullYear() - birthDate.getFullYear();
          const m = today.getMonth() - birthDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
          }
          if (age < 18) {
            payload.tutor_nombre = document.getElementById('formTutorNombre').value.trim();
            payload.tutor_apellidos = document.getElementById('formTutorApellidos').value.trim();
            payload.tutor_email = document.getElementById('formTutorEmail').value.trim();
            payload.tutor_relacion = document.getElementById('formTutorRelacion').value.trim();
          }
        }
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
        if (vActiva === 'view-equipo') cargarEntrenadoras();
        if (vActiva === 'view-gimnastas') cargarTablaUsuarios('gimnasta', paginaActual.gimnasta);
        if (vActiva === 'view-admins') cargarTablaUsuarios('administrador', paginaActual.administrador);
      } catch (err) {
        alert(err.message ?? 'No se pudo eliminar el usuario.');
      }
    }

    /* *********************
     * Alertas formulario
     * ********************* */
    function mostrarAlerta(msg, tipo) {
      const el = document.getElementById('formAlert');
      el.textContent = msg;
      el.className = `alert-banner alert-${tipo}`;
    }
    function limpiarAlerta() {
      const el = document.getElementById('formAlert');
      el.className = 'alert-banner';
      el.textContent = '';
    }

    /* *************
     * Modal helper
     * ************** */
    function cerrarModal(id, event) {
      if (!event || event.target.classList.contains('modal-overlay')) {
        document.getElementById(id).classList.remove('open');
      }
    }

    /* *********
     * Logout
     * ********* */
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

    /* ***************
     * GRUPOS Y CLASES
     * *************** */
    let grupoActualActivo = null;
    let gimnastasDisponibles = [];

    async function cargarGrupos() {
      const grid = document.getElementById('gruposGrid');
      grid.innerHTML = `<div class="loading-state"><div class="loading-spinner"></div><p>Cargando grupos…</p></div>`;

      try {
        const data = await apiFetch('/conjuntos');
        const lista = data.data ?? [];

        if (!lista.length) {
          grid.innerHTML = `<div class="empty-state"><div class="empty-title">Sin Grupos</div><div class="empty-desc">Aún no hay grupos creados.</div></div>`;
          return;
        }

        grid.innerHTML = lista.map(g => `
        <div class="team-card">
          <div class="card-avatar" style="font-size:1.5rem">G</div>
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
            <button class="btn-outline" onclick='imprimirListaConjunto(${g.id})' title="Imprimir lista PDF">PDF</button>
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

        // Filtro: 
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

    /* ***********************************
     * Selectores dependientes (Gimnastas)
     * ********************************** */

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

    /* *********************
     * Modal Competiciones
     ********************** */
    async function abrirFormCompeticion() {
      document.getElementById('competicionForm').reset();
      document.getElementById('compLat').value = '';
      document.getElementById('compLng').value = '';
      document.getElementById('compMapPreview').style.display = 'none';
      document.getElementById('compAlert').className = 'alert-banner';
      document.getElementById('compAlert').textContent = '';

      document.getElementById('modalCompeticion').classList.add('open');

      const selEnt = document.getElementById('compEntrenadoras');
      const selConj = document.getElementById('compConjuntos');
      const selGim = document.getElementById('compGimnastas');

      selEnt.innerHTML = '<option value="">Cargando entrenadoras...</option>';
      selConj.innerHTML = '<option value="">Cargando grupos...</option>';
      selGim.innerHTML = '<option value="">Cargando gimnastas...</option>';

      try {
        const [resEnt, resGim, resConj] = await Promise.all([
          apiFetch('/usuarios?rol=entrenadora&per_page=1000'),
          apiFetch('/usuarios?rol=gimnasta&per_page=1000'),
          apiFetch('/conjuntos')
        ]);

        selEnt.innerHTML = '';
        (resEnt.data || []).forEach(u => {
          selEnt.innerHTML += `<option value="${u.entrenador?.id || u.id}">${u.nombre} ${u.apellidos ?? ''}</option>`;
        });

        selConj.innerHTML = '';
        (resConj.data || resConj || []).forEach(c => {
          selConj.innerHTML += `<option value="${c.id}">${c.nombre} (${c.categoria?.nombre ?? '-'})</option>`;
        });

        selGim.innerHTML = '';
        (resGim.data || []).forEach(u => {
          selGim.innerHTML += `<option value="${u.gimnasta?.id || u.id}">${u.nombre} ${u.apellidos ?? ''} (Cat: ${u.gimnasta?.categoria?.nombre ?? '-'})</option>`;
        });
      } catch (err) {
        selEnt.innerHTML = '<option value="">Error cargando entrenadoras</option>';
        selConj.innerHTML = '<option value="">Error cargando grupos</option>';
        selGim.innerHTML = '<option value="">Error cargando gimnastas</option>';
      }

      // Inicializa el autocompletar de Google Places en el campo de dirección
      function initPlacesAutocomplete() {
        const dirInput = document.getElementById('compDireccion');
        const latInput = document.getElementById('compLat');
        const lngInput = document.getElementById('compLng');
        const mapPreview = document.getElementById('compMapPreview');

        // Evita registrar el autocompletar dos veces si el modal se abre varias veces
        if (dirInput._autocompleteInit) return;
        dirInput._autocompleteInit = true;

        const autocomplete = new google.maps.places.Autocomplete(dirInput, {
          types: ['establishment', 'geocode'],
          fields: ['formatted_address', 'geometry', 'name']
        });

        // Cuando el usuario elige una dirección de la lista
        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace();
          if (!place.geometry) return;

          const lat = place.geometry.location.lat();
          const lng = place.geometry.location.lng();
          latInput.value = lat;
          lngInput.value = lng;
          dirInput.value = place.formatted_address || place.name;

          // Muestra un mini-mapa con la ubicación seleccionada
          mapPreview.style.display = 'block';
          const previewMap = new google.maps.Map(mapPreview, {
            zoom: 15,
            center: { lat, lng },
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,
            fullscreenControl: false
          });
          new google.maps.Marker({ position: { lat, lng }, map: previewMap, title: place.name });
        });

        // Si el usuario escribe manualmente, limpia las coordenadas y oculta el mapa
        dirInput.addEventListener('input', () => {
          latInput.value = '';
          lngInput.value = '';
          mapPreview.style.display = 'none';
        });
      }

      // Espera a que Maps esté listo antes de inicializar el autocompletar
      if (typeof onGoogleMapsReady === 'function') {
        onGoogleMapsReady(initPlacesAutocomplete);
      } else if (typeof google !== 'undefined') {
        initPlacesAutocomplete();
      }
    }

    async function guardarCompeticion(e) {
      e.preventDefault();
      const btn = document.getElementById('btnSubmitComp');
      btn.disabled = true;
      btn.textContent = 'Guardando...';

      const selectEntrenadoras = document.getElementById('compEntrenadoras');
      const entrenadoras = Array.from(selectEntrenadoras.selectedOptions).map(o => parseInt(o.value)).filter(v => !isNaN(v));

      const selectConjuntos = document.getElementById('compConjuntos');
      const conjuntos = Array.from(selectConjuntos.selectedOptions).map(o => parseInt(o.value)).filter(v => !isNaN(v));

      const selectGimnastas = document.getElementById('compGimnastas');
      const gimnastas = Array.from(selectGimnastas.selectedOptions).map(o => parseInt(o.value)).filter(v => !isNaN(v));

      const payload = {
        nombre: document.getElementById('compNombre').value.trim(),
        fecha: document.getElementById('compFecha').value,
        hora: document.getElementById('compHora').value || null,
        direccion: document.getElementById('compDireccion').value.trim() || null,
        lat: document.getElementById('compLat').value ? parseFloat(document.getElementById('compLat').value) : null,
        lng: document.getElementById('compLng').value ? parseFloat(document.getElementById('compLng').value) : null,
        entrenadoras: entrenadoras,
        conjuntos: conjuntos,
        gimnastas: gimnastas
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

    // IMPRIMIR LISTA PDF
  async function imprimirListaConjunto(conjuntoId) {
    if (!conjuntoId) return;
    const url = `http://localhost:5000/pdf/conjunto/${conjuntoId}?token=${encodeURIComponent(token)}`;
  window.open(url, '_blank');
}
  </script>
</body>

</html>