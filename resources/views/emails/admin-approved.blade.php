<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Admin Disetujui - OOPEDIA</title>
    <style>
        body {
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            color: #1e293b;
            font-size: 20px;
            margin-top: 0;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            background-color: #dcfce7;
            color: #166534;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
            transition: background-color 0.2s;
        }
        .footer {
            padding: 30px;
            background-color: #f1f5f9;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OOPEDIA</h1>
        </div>
        <div class="content">
            <div class="badge">Akses Disetujui</div>
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Kabar gembira! Permohonan akun admin Anda telah <strong>berhasil disetujui</strong> oleh Superadmin.</p>
            <p>Kini Anda memiliki akses penuh ke dashboard manajemen OOPEDIA untuk mengelola materi, mahasiswa, dan memantau perkembangan pembelajaran.</p>
            
            <div class="button-container">
                <a href="{{ URL::to('/admin/dashboard') }}" class="button">Masuk ke Dashboard</a>
            </div>
            
            <p style="margin-top: 30px;">Jika tombol di atas tidak berfungsi, silakan salin dan tempel tautan berikut ke browser Anda:</p>
            <p style="font-size: 12px; color: #94a3b8; word-break: break-all;">{{ URL::to('/admin/dashboard') }}</p>
        </div>
        <div class="footer">
            <p><strong>OOPEDIA.com</strong> - Platform Pembelajaran Interaktif</p>
            <p>&copy; {{ date('Y') }} Tim OOPEDIA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
