<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 40px 30px;
        }

        .content p {
            color: #333;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background: #667eea;
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }

        .button:hover {
            background: #5568d3;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Restablecer Contraseña</h1>
        </div>

        <div class="content">
            <p>Hola,</p>

            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Si fuiste tú, haz clic en el
                siguiente botón:</p>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">
                    Restablecer Contraseña
                </a>
            </div>

            <p>Este enlace expirará en <strong>{{ $count }} minutos</strong> por seguridad.</p>

            <div class="warning">
                <strong>⚠️ Importante:</strong> Si no solicitaste restablecer tu contraseña, ignora este correo. Tu
                cuenta está segura.
            </div>

            <p style="color: #666; font-size: 13px; margin-top: 30px;">
                Si tienes problemas con el botón, copia y pega este enlace en tu navegador:<br>
                <a href="{{ $url }}" style="color: #667eea; word-break: break-all;">{{ $url }}</a>
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            <p>Este es un correo automático, por favor no respondas.</p>
        </div>
    </div>
</body>

</html>
