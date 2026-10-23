<?php
$user = $_SESSION['auth0_user'] ?? [];
$userEmail = $_SESSION['auth0_user_email'] ?? ($user['email'] ?? 'correo@dominio.com');
$currentPage = 'materias';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDTUTOR | Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7f6; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e5e7eb; padding: 1rem 1rem 2rem; }
        .brand-box { display: flex; align-items: center; gap: 12px; padding: 0.75rem 0.5rem 1rem; margin-bottom: 1rem; font-weight: 800; font-size: 1.4rem; }
        .brand-dot { width: 28px; height: 28px; border-radius: 10px; background: linear-gradient(135deg, #0a7f66, #1ebd92); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
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
        .main-panel { min-height: 100vh; background: #f9fafb; }
        .topbar { background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .user-menu { display: flex; align-items: center; gap: 12px; font-weight: 600; }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0d7d63, #1ac38f); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
        .content { padding: 2rem 1.5rem; }
        .table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; box-shadow: 0 8px 16px rgba(17,24,39,0.03); }
        .action-group { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .switch-toggle {
            position: relative;
            width: 52px;
            height: 28px;
            display: inline-block;
        }
        .switch-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .switch-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #d1d5db;
            border-radius: 999px;
            transition: 0.25s ease;
        }
        .switch-slider::before {
            content: "";
            position: absolute;
            height: 20px;
            width: 20px;
            left: 4px;
            top: 4px;
            background: white;
            border-radius: 50%;
            transition: 0.25s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .switch-toggle input:checked + .switch-slider {
            background: linear-gradient(135deg, #0d7d63, #1ac38f);
        }
        .switch-toggle input:checked + .switch-slider::before {
            transform: translateX(24px);
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
                        <span><?php echo e($userEmail); ?></span>
                        <div class="user-avatar"><?php echo strtoupper(substr($userEmail, 0, 1)); ?></div>
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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Materias</h2>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createMateriaModal">
                            Crear materia
                        </button>
                    </div>

                    <div class="table-card p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Área</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($materias)): ?>
                                        <?php foreach ($materias as $materia): ?>
                                            <?php
                                                $materiaId = (int)($materia['id_materia'] ?? 0);
                                                $modalId = 'editMateriaModal_' . $materiaId;
                                            ?>
                                            <tr>
                                                <td><?php echo e($materia['codigo']); ?></td>
                                                <td><?php echo e($materia['nombre']); ?></td>
                                                <td><?php echo e($materia['descripcion'] ?? '-'); ?></td>
                                                <td><?php echo e($materia['area'] ?? '-'); ?></td>
                                                <td>
                                                    <?php echo !empty($materia['activa']) ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>'; ?>
                                                </td>
                                                <td>
                                                    <div class="action-group">
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                                            Actualizar
                                                        </button>

                                                        <form method="post" action="<?php echo base_url('/materias'); ?>" class="d-inline-flex align-items-center gap-2 m-0">
                                                            <input type="hidden" name="action" value="toggle_status">
                                                            <input type="hidden" name="id_materia" value="<?php echo $materiaId; ?>">
                                                            <label class="switch-toggle" title="<?php echo !empty($materia['activa']) ? 'Desactivar' : 'Activar'; ?>">
                                                                <input type="checkbox" name="activa" value="1" <?php echo !empty($materia['activa']) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                                <span class="switch-slider"></span>
                                                            </label>
                                                        </form>

                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMateriaModal_<?php echo $materiaId; ?>">
                                                            Eliminar
                                                        </button>

                                                        <div class="modal fade" id="deleteMateriaModal_<?php echo $materiaId; ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content border-0 shadow-lg">
                                                                    <div class="modal-header border-0 pb-0">
                                                                        <h5 class="modal-title fw-bold text-danger">Eliminar materia</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body text-center py-4">
                                                                        <div class="mb-3">
                                                                            <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                                                                <span style="font-size: 1.8rem;">⚠️</span>
                                                                            </div>
                                                                        </div>
                                                                        <p class="mb-0 fs-6 text-secondary">
                                                                            ¿Deseas eliminar la materia <strong><?php echo e($materia['nombre']); ?></strong>?
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer border-0 pt-0">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                        <form method="post" action="<?php echo base_url('/materias'); ?>" class="d-inline m-0">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id_materia" value="<?php echo $materiaId; ?>">
                                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form method="post" action="<?php echo base_url('/materias'); ?>">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="id_materia" value="<?php echo $materiaId; ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Actualizar materia</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Código</label>
                                                                        <input type="text" class="form-control" name="codigo" value="<?php echo e($materia['codigo']); ?>" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Nombre</label>
                                                                        <input type="text" class="form-control" name="nombre" value="<?php echo e($materia['nombre']); ?>" required>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label">Descripción</label>
                                                                        <textarea class="form-control" name="descripcion" rows="4"><?php echo e($materia['descripcion'] ?? ''); ?></textarea>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Área</label>
                                                                        <select class="form-select" name="area" required>
                                                                            <option value="">Selecciona un área</option>
                                                                            <option value="Ciencias Exactas" <?php echo ((string)($materia['area'] ?? '') === 'Ciencias Exactas') ? 'selected' : ''; ?>>Ciencias Exactas</option>
                                                                            <option value="Ciencias" <?php echo ((string)($materia['area'] ?? '') === 'Ciencias') ? 'selected' : ''; ?>>Ciencias</option>
                                                                            <option value="Humanidades" <?php echo ((string)($materia['area'] ?? '') === 'Humanidades') ? 'selected' : ''; ?>>Humanidades</option>
                                                                            <option value="Idiomas" <?php echo ((string)($materia['area'] ?? '') === 'Idiomas') ? 'selected' : ''; ?>>Idiomas</option>
                                                                            <option value="Tecnología" <?php echo ((string)($materia['area'] ?? '') === 'Tecnología') ? 'selected' : ''; ?>>Tecnología</option>
                                                                            <option value="Administración" <?php echo ((string)($materia['area'] ?? '') === 'Administración') ? 'selected' : ''; ?>>Administración</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay materias registradas aún.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="createMateriaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="<?php echo base_url('/materias'); ?>">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear materia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" name="codigo" placeholder="Ej: MAT-301" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej: Álgebra Lineal" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="4" placeholder="Describe la materia..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Área</label>
                                <select class="form-select" name="area" required>
                                    <option value="">Selecciona un área</option>
                                    <option value="Ciencias Exactas">Ciencias Exactas</option>
                                    <option value="Ciencias">Ciencias</option>
                                    <option value="Humanidades">Humanidades</option>
                                    <option value="Idiomas">Idiomas</option>
                                    <option value="Tecnología">Tecnología</option>
                                    <option value="Administración">Administración</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar materia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const refreshPage = function () {
                const url = new URL(window.location.href);
                url.searchParams.set('t', Date.now().toString());
                window.location.replace(url.toString());
            };

            document.querySelectorAll('form').forEach(function (form) {
                const action = form.querySelector('input[name="action"]');
                if (!action) {
                    return;
                }

                if (['delete', 'toggle_status', 'create', 'update'].includes(action.value)) {
                    form.addEventListener('submit', function () {
                        setTimeout(refreshPage, 200);
                    });
                }
            });
        });
    </script>
</body>
</html>
