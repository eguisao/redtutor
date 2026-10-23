<?php
$brand = 'REDTUTOR';
$authError = $_SESSION['auth_error'] ?? null;
unset($_SESSION['auth_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDTUTOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-900: #0a7f66;
            --green-700: #0d7d63;
            --green-500: #19a57d;
            --soft: #f3f5f3;
            --bg: #f7f7f7;
            --dark: #1f2329;
            --muted: #5d6470;
            --card: #f3f8f6;
            --shadow: rgba(17, 24, 39, 0.08);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            width: 100%;
            height: 100%;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            overflow: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: rgba(255,255,255,0.9);
            border-bottom: 1px solid rgba(17,24,39,0.06);
            padding: 12px 0;
            flex-shrink: 0;
        }

        .container-custom {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.65rem;
            letter-spacing: -0.04em;
            color: var(--dark);
        }

        .brand-mark {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--green-900), var(--green-500));
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .navlinks {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .navlinks a {
            text-decoration: none;
            color: var(--dark);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .btn-redtutor {
            background: linear-gradient(90deg, var(--green-900), var(--green-500));
            color: white !important;
            border: none;
            border-radius: 14px;
            padding: 0.72rem 1.3rem;
            font-weight: 700;
            box-shadow: 0 10px 18px rgba(13,125,99,0.18);
        }

        .page-shell {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px 0 14px;
        }

        .home-card {
            width: min(1180px, calc(100% - 24px));
            background: rgba(255,255,255,0.35);
            border-radius: 30px;
            padding: 18px 28px 8px;
            box-shadow: 0 18px 35px var(--shadow);
        }

        .hero-row {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 18px;
            align-items: center;
            min-height: 520px;
        }

        .hero-copy {
            padding-left: 8px;
        }

        .hero-copy h1 {
            margin: 0;
            font-size: clamp(2.7rem, 4vw, 4rem);
            line-height: 0.95;
            letter-spacing: -0.07em;
            font-weight: 800;
            color: var(--dark);
        }

        .hero-copy h1 .green {
            color: var(--green-700);
        }

        .hero-copy p {
            margin: 18px 0 26px;
            max-width: 470px;
            color: var(--muted);
            font-size: 1.04rem;
            line-height: 1.6;
        }

        .cta-row {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .cta-row a {
            text-decoration: none;
        }

        .btn-search {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 210px;
            background: linear-gradient(90deg, var(--green-900), var(--green-500));
            color: #fff;
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(13,125,99,0.12);
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            font-weight: 700;
            text-decoration: none;
        }

        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 350px;
        }

        .hero-visual img {
            width: min(75%, 720px);
            height: auto;
            display: block;
        }

        .how-section {
            margin-top: 12px;
            padding-top: 6px;
            text-align: center;
        }

        .how-title {
            font-size: clamp(1.9rem, 2.3vw, 2.8rem);
            font-weight: 800;
            margin: 0 0 18px;
            letter-spacing: -0.05em;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            gap: 18px;
            margin-top: 16px;
        }

        .step-card {
            background: rgba(255,255,255,0.35);
            border-radius: 18px;
            padding: 16px 12px 18px;
            text-align: center;
            min-height: 175px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .icon-badge {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #dff5ec;
            color: var(--green-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .step-card h3 {
            margin: 0 0 8px;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .step-card p {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.5;
            max-width: 220px;
        }

        .community-box {
            margin: 22px auto 0;
            background: rgba(228,247,237,0.7);
            border-radius: 22px;
            padding: 18px 18px 14px;
            max-width: 760px;
            text-align: center;
        }

        .community-box h4 {
            margin: 0;
            font-size: clamp(1.3rem, 2vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .community-box p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.96rem;
        }

        .toast-alert {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 9999;
            max-width: 420px;
            background: #fff5f5;
            color: #7a1f1f;
            border: 1px solid #f3c2c2;
            border-left: 5px solid #d63b3b;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 12px 28px rgba(123, 31, 31, 0.12);
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .toast-alert .icon {
            font-size: 1.2rem;
            line-height: 1;
        }

        @media (max-width: 980px) {
            html, body {
                overflow: auto;
            }

            .page-shell {
                padding-top: 10px;
            }

            .home-card {
                padding: 18px 18px 20px;
            }

            .hero-row {
                grid-template-columns: 1fr;
                text-align: center;
                min-height: auto;
            }

            .hero-copy {
                padding-left: 0;
            }

            .hero-copy p {
                margin-left: auto;
                margin-right: auto;
            }

            .cta-row {
                justify-content: center;
            }

            .steps-grid {
                grid-template-columns: 1fr;
            }

            .navlinks {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container-custom d-flex justify-content-between align-items-center">
            <div class="brand">
                <span class="brand-mark">R</span>
                <span>RedTutor</span>
            </div>

            <nav class="navlinks">
                <a href="#">Inicio</a>
                <a href="#">Como funciona</a>
                <a href="#">Sobre nosotros</a>
                <a href="<?php echo base_url('/login'); ?>" class="btn-redtutor">Iniciar sesión</a>
            </nav>
        </div>
    </header>

    <?php if (!empty($authError)): ?>
        <div class="toast-alert" role="alert" aria-live="assertive">
            <span class="icon">⚠️</span>
            <span><?php echo e($authError); ?></span>
        </div>
    <?php endif; ?>

    <main class="page-shell">
        <div class="home-card">
            <div class="hero-row">
                <div class="hero-copy">
                    <h1>Aprende.<br><span class="green">Comparte.</span><br>Conecta.</h1>
                    <p>
                        RedTutor conecta estudiantes y tutores voluntarios que necesitan apoyo académico,
                        con profesionales, docentes y compañeros dispuestos a compartir conocimiento.
                    </p>

                    <div class="cta-row">
                        <a href="<?php echo base_url('/login'); ?>" class="btn-search">Buscar una tutoría</a>
                        <a href="<?php echo base_url('/login'); ?>" class="btn-link">Quiero ser tutor</a>
                    </div>
                </div>

                <div class="hero-visual">
                    <img src="<?php echo base_url('public/images/logoredtutor.png'); ?>" alt="RedTutor ilustración">
                </div>
            </div>

            <section class="how-section">
                <h2 class="how-title">¿Cómo funciona?</h2>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="icon-badge">⌕</div>
                        <h3>1. Encuentra</h3>
                        <p>Busca la materia que necesitas y encuentra a la persona ideal para ayudarte.</p>
                    </div>
                    <div class="step-card">
                        <div class="icon-badge">◫</div>
                        <h3>2. Agenda</h3>
                        <p>Selecciona la fecha y horario que mejor se adapte a tus necesidades.</p>
                    </div>
                    <div class="step-card">
                        <div class="icon-badge">✓</div>
                        <h3>3. Confirma</h3>
                        <p>Confirma la tutoría y recibe apoyo en tiempo real con confianza y seguridad.</p>
                    </div>
                </div>

                <div class="community-box">
                    <h4>Una comunidad que enseña y aprende junto a ti</h4>
                    <p>Más de 100 estudiantes y tutores conectados para compartir conocimiento y fortalecer el aprendizaje.</p>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
