<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida a Rytmia - Tutor Legal</title>
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
        .credentials-table {
            width: 100%;
            background-color: #FAF6F1;
            border-radius: 8px;
            border: 1px solid #F2D5DF;
            margin: 24px 0;
            padding: 16px;
        }
        .credentials-table td {
            padding: 8px 12px;
            font-size: 15px;
        }
        .credentials-table td.label {
            font-weight: 600;
            color: #6B1A3A;
            width: 150px;
        }
        .rgpd-box {
            background-color: #FFF8FA;
            border-left: 4px solid #C45C7E;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
            font-size: 13.5px;
            color: #553E45;
        }
        .rgpd-box strong {
            color: #6B1A3A;
            display: block;
            margin-bottom: 6px;
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
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #C45C7E;
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
                        <p>Espacio del Tutor Legal</p>
                    </div>

                    <!-- Content -->
                    <div class="content">
                        <h2>Estimado/a {{ $tutor->nombre }} {{ $tutor->apellidos }},</h2>
                        
                        <p>Le escribimos para informarle de que la gimnasta menor de edad <strong>{{ $user->nombre }} {{ $user->apellidos }}</strong>, bajo su tutela legal, ha sido dada de alta en nuestra plataforma Rytmia.</p>
                        
                        <p>Como tutor/a legal, le proporcionamos las credenciales de acceso a su cuenta para que pueda supervisar y gestionar el uso de la plataforma. La menor no ha recibido este correo.</p>

                        <table class="credentials-table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="label">Usuario de la menor:</td>
                                <td><code>{{ $user->username }}</code></td>
                            </tr>
                            <tr>
                                <td class="label">Contraseña temporal:</td>
                                <td><code>{{ $user->password_temporal }}</code></td>
                            </tr>
                        </table>

                        <!-- Aviso RGPD -->
                        <div class="rgpd-box">
                            <strong>Aviso de Protección de Datos (RGPD)</strong>
                            De conformidad con el Reglamento General de Protección de Datos (RGPD) y la LOPDGDD, le informamos de que tratamos los datos de la gimnasta menor y del tutor legal con la exclusiva finalidad de gestionar su participación deportiva y actividades en el club. Al ser tutor legal, recae sobre usted la responsabilidad de supervisar la cuenta de la menor. Puede ejercer sus derechos de acceso, rectificación, supresión y limitación escribiendo a la dirección del administrador de su club.
                        </div>

                        <div class="btn-container">
                            <a href="{{ url('/') }}" class="btn">Acceder a Rytmia</a>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p>Este es un correo automático enviado por <strong>Rytmia</strong>.</p>
                        <p>&copy; {{ date('Y') }} Rytmia. Todos los derechos reservados.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
