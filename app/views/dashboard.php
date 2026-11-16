<?php
$user = $_SESSION['auth0_user'] ?? [];
$email = $_SESSION['auth0_user_email'] ?? ($user['email'] ?? 'correo@dominio.com');
$userRole = $_SESSION['auth0_user_role'] ?? ($_SESSION['auth0_user_roles'][0] ?? '');
if (!is_string($userRole)) {
    $userRole = trim((string) $userRole);
}
$currentPage = 'dashboard';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDTUTOR | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7f6;
            color: #1f2937;
        }
        .sidebar {
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 1rem 1rem 2rem;
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 0.5rem 1rem;
            margin-bottom: 1rem;
            font-weight: 800;
            font-size: 1.4rem;
        }
        .brand-dot {
            width: 28px;
            height: 28px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0a7f66, #1ebd92);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.7rem 0.75rem;
            border-radius: 12px;
            color: #1f2937;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            font-size: 1.05rem;
            line-height: 1.3;
        }
        .nav-link.active, .nav-link:hover {
            background: #dff5ee;
            color: #0d7d63;
            border-color: #dff5ee;
        }
        .nav-section {
            margin-top: 1rem;
        }
        .nav-label {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: #4b5563;
            padding: 0.5rem 0.7rem 0.4rem;
            margin-bottom: 0.15rem;
        }
        .main-panel {
            min-height: 100vh;
            background: #f9fafb;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d7d63, #1ac38f);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .content {
            padding: 2rem 1.5rem;
        }
        .welcome-box {
            background: linear-gradient(135deg, #ffffff, #f1fff7);
            border: 1px solid #dff5ea;
            border-radius: 20px;
            padding: 2rem;
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .welcome-box h1 {
            font-size: clamp(2rem, 3vw, 3rem);
            font-weight: 800;
            margin-bottom: 0.6rem;
        }
        .muted {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-md-3 col-lg-2 sidebar">
                <div class="brand-box">
                    <span class="brand-dot">R</span>
                    <span>REDTUTOR</span>
                </div>

                <nav class="nav flex-column gap-2">
                    <div class="nav-section">
                        <div class="nav-label">General</div>
                        <a href="<?php echo base_url('/dashboard'); ?>" class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">Inicio</a>
                        <a href="#" class="nav-link">Notificaciones</a>
                        <a href="#" class="nav-link">Historial</a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-label">Academia</div>
                        <a href="<?php echo base_url('/materias'); ?>" class="nav-link <?php echo $currentPage === 'materias' ? 'active' : ''; ?>">Materias</a>
                        <a href="#" class="nav-link">Tutorías</a>
                        <a href="#" class="nav-link">Calendario</a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-label">Administración</div>
                        <a href="<?php echo base_url('/usuarios'); ?>" class="nav-link <?php echo $currentPage === 'usuarios' ? 'active' : ''; ?>">Usuarios Auth0</a>
                    </div>
                </nav>
            </aside>

            <main class="col main-panel">
                <header class="topbar">
                    <div></div>
                    <div class="user-menu">
                        <span><?php echo e($email); ?></span>
                        <div class="user-avatar"><?php echo strtoupper(substr($email, 0, 1)); ?></div>
                        <a href="<?php echo base_url('/logout'); ?>" class="btn btn-outline-secondary btn-sm">Cerrar sesión</a>
                    </div>
                </header>

                <div class="content">
                    <?php if ($flashSuccess): ?>
                        <div class="alert alert-success"><?php echo e($flashSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($flashError): ?>
                        <div class="alert alert-danger"><?php echo e($flashError); ?></div>
                    <?php endif; ?>

                    <div class="alert alert-info mt-0 mb-3">
                        <strong>Tipo de usuario:</strong> <?php echo e($userRole !== '' ? ucfirst($userRole) : 'Sin rol detectado'); ?>
                    </div>

                    <div class="welcome-box">
                        <div>
                            <h1>Bienvenido a <span style="color:#0d7d63;">REDTUTOR</span></h1>
                            <p class="muted mb-0">Usuario autenticado: <?php echo e($email); ?></p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
