<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Competición</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            margin-top: 0;
            font-size: 20px;
            color: #1e293b;
            font-weight: 600;
        }
        .details-card {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }
        .detail-row {
            margin-bottom: 12px;
            font-size: 15px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #475569;
            width: 100px;
            display: inline-block;
        }
        .detail-value {
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 6px;
            background-color: #e0e7ff;
            color: #4338ca;
        }
        .btn-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15);
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #4338ca;
        }
        .footer {
            text-align: center;
            padding: 24px 30px;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
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
                        <img src="{{ url('/logo.jpg') }}" alt="Logo Rytmia" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 8px;">
                        <h1>Rytmia</h1>
                        <p>Nueva convocatoria para competición</p>
                    </div>

                    <!-- Content -->
                    <div class="content">
                        <h2>¡Hola, {{ $user->nombre }}!</h2>
                        
                        @if($user->esEntrenadora())
                            <p>Te informamos de que has sido convocada como <strong>entrenadora</strong> para una nueva competición con nuestro club. A continuación, tienes los detalles del evento:</p>
                        @else
                            <p>Te informamos de que has sido seleccionada para <strong>participar como gimnasta</strong> en una nueva competición con nuestro club. A continuación, tienes los detalles del evento:</p>
                        @endif
                        
                        <div class="details-card">
                            <div class="detail-row">
                                <span class="detail-label">Nombre:</span>
                                <span class="detail-value"><strong>{{ $competicion->nombre }}</strong></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Fecha:</span>
                                <span class="detail-value">{{ $competicion->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if($competicion->lugar)
                            <div class="detail-row">
                                <span class="detail-label">Lugar:</span>
                                <span class="detail-value">{{ $competicion->lugar }}</span>
                            </div>
                            @endif
                            @if($competicion->direccion)
                            <div class="detail-row">
                                <span class="detail-label">Dirección:</span>
                                <span class="detail-value">{{ $competicion->direccion }}</span>
                            </div>
                            @endif
                            <div class="detail-row">
                                <span class="detail-label">Categoría/Tipo:</span>
                                <span class="detail-value"><span class="badge">{{ $competicion->tipo }}</span></span>
                            </div>
                        </div>

                        <p>Por favor, asegúrate de revisar el horario de salida y la planificación en tu área personal.</p>

                        <div class="btn-container">
                            @if($user->esEntrenadora())
                                <a href="{{ url('/dashboard/entrenadora') }}" class="btn">Ver en el Dashboard</a>
                            @else
                                <a href="{{ url('/dashboard/gimnasta') }}" class="btn">Ver en el Dashboard</a>
                            @endif
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
