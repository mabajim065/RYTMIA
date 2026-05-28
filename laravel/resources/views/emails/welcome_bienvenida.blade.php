<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida a Rytmia</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #FDF6F0;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #FDF6F0;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .main-card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(107,26,58,.08);
            border: 1px solid #F2D5DF;
        }
        .header {
            background: linear-gradient(135deg, #6B1A3A 0%, #C45C7E 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            color: #2A1520;
            line-height: 1.6;
        }
        .content h2 {
            margin-top: 0;
            font-size: 20px;
            color: #6B1A3A;
            font-weight: 600;
        }
        .role-badge {
            display: inline-block;
            background-color: #F2D5DF;
            color: #6B1A3A;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            text-transform: capitalize;
        }
        .credentials-box {
            width: 100%;
            background-color: #FAF6F1;
            border-radius: 10px;
            border: 1px solid #F2D5DF;
            margin: 24px 0;
            overflow: hidden;
        }
        .credentials-box .box-header {
            background-color: #F2D5DF;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            color: #6B1A3A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
        }
        .credentials-table td {
            padding: 12px 16px;
            font-size: 15px;
            border-bottom: 1px solid #F2D5DF;
        }
        .credentials-table tr:last-child td {
            border-bottom: none;
        }
        .credentials-table td.label {
            font-weight: 600;
            color: #6B1A3A;
            width: 160px;
        }
        .credentials-table code {
            background-color: #FFF0F5;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #6B1A3A;
            border: 1px solid #F2D5DF;
        }
        .warning-box {
            background-color: #FFF8FA;
            border-left: 4px solid #C45C7E;
            padding: 14px 16px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 13.5px;
            color: #553E45;
        }
        .warning-box strong {
            color: #6B1A3A;
            display: block;
            margin-bottom: 4px;
        }
        .btn-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 32px;
        }
        .btn {
            background-color: #6B1A3A;
            color: #ffffff !important;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(107, 26, 58, 0.2);
        }
        .footer {
            text-align: center;
            padding: 24px 30px;
            font-size: 13px;
            color: #9B7080;
            border-top: 1px solid #F2D5DF;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main-card" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td>
                    <!-- Header -->
                    <div class="header">
                        <img src="https://raw.githubusercontent.com/mabajim065/RYTMIA/desarrollo/laravel/public/logo.jpg" alt="Logo Rytmia" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 8px;">
                        <h1>Rytmia</h1>
                        <p>Tu espacio de entrenamiento</p>
                    </div>

                    <!-- Content -->
                    <div class="content">
                        <h2>¡Hola, {{ $user->nombre }}!</h2>

                        <span class="role-badge">
                            @if($user->rol === 'entrenadora')
                                Entrenadora
                            @elseif($user->rol === 'administrador')
                                Administrador/a
                            @else
                                {{ ucfirst($user->rol) }}
                            @endif
                        </span>

                        <p>El administrador ha creado tu cuenta en la plataforma <strong>Rytmia</strong>. A partir de ahora podrás acceder con las credenciales que encontrarás a continuación.</p>

                        @if($user->rol === 'entrenadora')
                            <p>Como <strong>entrenadora</strong>, tendrás acceso a la gestión de tus gimnastas, conjuntos, competiciones y comunicación con el club.</p>
                        @elseif($user->rol === 'administrador')
                            <p>Como <strong>administrador/a</strong>, tendrás acceso completo a la gestión de usuarios, clubes, competiciones y configuración de la plataforma.</p>
                        @endif

                        <!-- Credentials box -->
                        <div class="credentials-box">
                            <div class="box-header">🔑 Tus credenciales de acceso</div>
                            <table class="credentials-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="label">Correo:</td>
                                    <td><code>{{ $user->email }}</code></td>
                                </tr>
                                <tr>
                                    <td class="label">Usuario:</td>
                                    <td><code>{{ $user->username }}</code></td>
                                </tr>
                                <tr>
                                    <td class="label">Contraseña:</td>
                                    <td><code>{{ $user->password_temporal }}</code></td>
                                </tr>
                            </table>
                        </div>

                        <div class="warning-box">
                            <strong>⚠️ Importante</strong>
                            Por seguridad, te recomendamos cambiar tu contraseña la primera vez que accedas a la plataforma. Esta contraseña es temporal y ha sido generada automáticamente.
                        </div>

                        <div class="btn-container">
                            <a href="{{ url('/') }}" class="btn">Iniciar Sesión en Rytmia</a>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p>Este es un correo automático enviado por <strong>Rytmia</strong>.</p>
                        <p>Si no esperabas este correo, por favor ignóralo o contacta con el administrador.</p>
                        <p>&copy; {{ date('Y') }} Rytmia. Todos los derechos reservados.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
