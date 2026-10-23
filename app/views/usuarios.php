<?php
$user = $_SESSION['auth0_user'] ?? [];
$currentUserId = $_SESSION['auth0_user_id'] ?? ($user['sub'] ?? '');
$userEmail = $_SESSION['auth0_user_email'] ?? ($user['email'] ?? 'correo@dominio.com');
$currentPage = 'usuarios';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDTUTOR | Usuarios Auth0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7f6; }
        .sidebar { min-height: 100vh; background: #fff; border-right: 1px solid #e5e7eb; padding: 1rem 1rem 2rem; }
        .brand-box { display:flex; align-items:center; gap:12px; padding:0.75rem 0.5rem 1rem; font-weight:800; font-size:1.4rem; }
        .brand-dot { width:28px; height:28px; border-radius:10px; background:linear-gradient(135deg,#0a7f66,#1ebd92); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; }
        .nav-section { margin-top: 1rem; }
        .nav-label { font-size: 0.72rem; font-weight: 800; letter-spacing: 0.10em; text-transform: uppercase; color:#4b5563; padding: 0.5rem 0.7rem 0.4rem; margin-bottom: 0.15rem; }
        .nav-link { display:flex; align-items:center; gap:10px; padding:0.7rem 0.75rem; border-radius:12px; color:#1f2937; font-weight:600; text-decoration:none; border:1px solid transparent; font-size:1.05rem; line-height:1.3; }
        .nav-link.active, .nav-link:hover { background:#dff5ee; color:#0d7d63; border-color:#dff5ee; }
        .main-panel { min-height:100vh; background:#f9fafb; }
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:1rem 1.5rem; display:flex; justify-content:space-between; align-items:center; }
        .user-menu { display:flex; align-items:center; gap:12px; font-weight:600; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#0d7d63,#1ac38f); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.9rem; }
        .content { padding:2rem 1.5rem; }
        .table-card { background:#fff; border:1px solid #e5e7eb; border-radius:18px; box-shadow:0 8px 16px rgba(17,24,39,0.03); }
        .metadata-box { max-width: 260px; white-space: pre-wrap; word-break: break-word; font-size: 0.75rem; margin: 0; }
        .status-pill { min-width: 110px; }
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
                        <h2 class="mb-0">Usuarios Auth0</h2>
                    </div>

                    <div class="table-card p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID Auth0</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Fecha de registro</th>
                                        <th>Estado</th>
                                        <th>Metadata</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($usuarios)): ?>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <?php
                                                $usuarioId = $usuario['user_id'] ?? '';
                                                $name = $usuario['name'] ?? ($usuario['nickname'] ?? '-');
                                                $email = $usuario['email'] ?? '-';
                                                $createdAt = $usuario['created_at'] ?? null;
                                                $blocked = !empty($usuario['blocked']);
                                                $isCurrentUser = trim((string) $currentUserId) !== '' && trim((string) $currentUserId) === trim((string) $usuarioId);
                                                $userMetadata = is_array($usuario['user_metadata'] ?? null) ? $usuario['user_metadata'] : [];
                                                $appMetadata = is_array($usuario['app_metadata'] ?? null) ? $usuario['app_metadata'] : [];
                                                $mergedMetadata = array_merge($userMetadata, $appMetadata, is_array($usuario['metadata'] ?? null) ? $usuario['metadata'] : []);
                                                $metaText = App\Models\Auth0UserModel::prettyMetadata($mergedMetadata);
                                                $fechaNacimiento = App\Models\Auth0UserModel::findMetadataValue($usuario, ['fecha_nacimiento', 'fechaNacimiento', 'birthdate', 'fecha_de_nacimiento'], '');
                                                $numeroDocumento = App\Models\Auth0UserModel::findMetadataValue($usuario, ['numero_documento', 'numeroDocumento', 'numero_de_documento', 'cedula', 'cedulanumero', 'documento', 'document_number'], '');
                                                $tipoUsuario = App\Models\Auth0UserModel::findMetadataValue($usuario, ['tipo_usuario', 'rol', 'role', 'roles', 'perfil'], '');
                                                $tipoDocumento = App\Models\Auth0UserModel::findMetadataValue($usuario, ['tipo_documento', 'tipoDocumento', 'document_type'], '');

                                                $fechaNacimientoInput = '';
                                                if ($fechaNacimiento !== '') {
                                                    $fechaDate = DateTime::createFromFormat('Y-m-d', $fechaNacimiento) ?: DateTime::createFromFormat('d/m/Y', $fechaNacimiento);
                                                    if ($fechaDate) {
                                                        $fechaNacimientoInput = $fechaDate->format('Y-m-d');
                                                    } else {
                                                        $fechaNacimientoInput = $fechaNacimiento;
                                                    }
                                                }

                                                $tipoUsuarioNormalizado = strtolower(trim((string) $tipoUsuario));
                                                $tipoDocumentoNormalizado = strtoupper(trim((string) $tipoDocumento));
                                            ?>
                                            <tr>
                                                <td><small class="text-muted"><?php echo e($usuarioId); ?></small></td>
                                                <td><?php echo e($name); ?></td>
                                                <td><?php echo e($email); ?></td>
                                                <td><?php echo e($tipoUsuario !== '' ? $tipoUsuario : 'Sin rol'); ?></td>
                                                <td><?php echo e($createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : '-'); ?></td>
                                                <td>
                                                    <span class="badge status-pill <?php echo $blocked ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?php echo $blocked ? 'Bloqueado' : 'Activo'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <pre class="metadata-box"><?php echo e($metaText); ?></pre>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <form method="post" action="<?php echo base_url('/usuarios'); ?>" class="d-inline-block m-0" data-toggle-block-form="1">
                                                            <input type="hidden" name="action" value="toggle_block">
                                                            <input type="hidden" name="user_id" value="<?php echo e($usuarioId); ?>">
                                                            <input type="hidden" name="blocked" value="<?php echo $blocked ? '0' : '1'; ?>">
                                                            <?php if ($isCurrentUser): ?>
                                                                <button type="button" class="btn btn-sm btn-secondary" disabled title="No puedes bloquear tu propio usuario">
                                                                    <?php echo $blocked ? 'Desbloquear' : 'Bloquear'; ?>
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="submit" class="btn btn-sm <?php echo $blocked ? 'btn-success' : 'btn-warning'; ?>">
                                                                    <?php echo $blocked ? 'Desbloquear' : 'Bloquear'; ?>
                                                                </button>
                                                            <?php endif; ?>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal_<?php echo md5((string) $usuarioId); ?>">
                                                            Actualizar
                                                        </button>

                                                        <?php if ($isCurrentUser): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled title="No puedes eliminar tu propio usuario">
                                                                Eliminar
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal_<?php echo md5((string) $usuarioId); ?>">
                                                                Eliminar
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="deleteUserModal_<?php echo md5((string) $usuarioId); ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-danger">Eliminar usuario</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body text-center py-4">
                                                            <div class="mb-3">
                                                                <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                                                    <span style="font-size:1.7rem;">⚠️</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 fs-6 text-secondary">
                                                                ¿Deseas eliminar al usuario <strong><?php echo e($email); ?></strong>?
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form method="post" action="<?php echo base_url('/usuarios'); ?>" class="d-inline m-0" data-delete-user-form="1">
                                                                <input type="hidden" name="action" value="delete_user">
                                                                <input type="hidden" name="user_id" value="<?php echo e($usuarioId); ?>">
                                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="editUserModal_<?php echo md5((string) $usuarioId); ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form method="post" action="<?php echo base_url('/usuarios'); ?>" data-update-user-form="1">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Actualizar usuario</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="action" value="update_user">
                                                                <input type="hidden" name="user_id" value="<?php echo e($usuarioId); ?>">

                                                                <div class="row g-3">
                                                                    <div class="col-md-12">
                                                                        <label class="form-label">ID Auth0</label>
                                                                        <input class="form-control" type="text" value="<?php echo e($usuarioId); ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Nombre</label>
                                                                        <input class="form-control" type="text" name="name" value="<?php echo e($usuario['name'] ?? ''); ?>">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Nickname</label>
                                                                        <input class="form-control" type="text" name="nickname" value="<?php echo e($usuario['nickname'] ?? ''); ?>">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label class="form-label">Correo</label>
                                                                        <input class="form-control" type="email" name="email" value="<?php echo e($usuario['email'] ?? ''); ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label class="form-label">Estado</label>
                                                                        <select class="form-select" name="blocked">
                                                                            <option value="0" <?php echo $blocked ? '' : 'selected'; ?>>Activo</option>
                                                                            <option value="1" <?php echo $blocked ? 'selected' : ''; ?>>Bloqueado</option>
                                                                        </select>
                                                                    </div>
                                                                    <input type="hidden" name="user_metadata_json" value="<?php echo e(json_encode($userMetadata)); ?>">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Fecha de nacimiento</label>
                                                                        <input class="form-control" type="date" name="fecha_nacimiento" value="<?php echo e($fechaNacimientoInput); ?>">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Número de documento</label>
                                                                        <input class="form-control" type="text" name="numero_documento" value="<?php echo e((string) $numeroDocumento); ?>">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Tipo de usuario</label>
                                                                        <select class="form-select" name="tipo_usuario">
                                                                            <option value="">Seleccione</option>
                                                                            <option value="Tutor" <?php echo ($tipoUsuarioNormalizado === 'tutor') ? 'selected' : ''; ?>>Tutor</option>
                                                                            <option value="Docente" <?php echo ($tipoUsuarioNormalizado === 'docente') ? 'selected' : ''; ?>>Docente</option>
                                                                            <option value="Estudiante" <?php echo ($tipoUsuarioNormalizado === 'estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Tipo de documento</label>
                                                                        <select class="form-select" name="tipo_documento">
                                                                            <option value="">Seleccione</option>
                                                                            <option value="CC" <?php echo ($tipoDocumentoNormalizado === 'CC') ? 'selected' : ''; ?>>CC</option>
                                                                            <option value="TI" <?php echo ($tipoDocumentoNormalizado === 'TI') ? 'selected' : ''; ?>>TI</option>
                                                                            <option value="PASSPORT" <?php echo ($tipoDocumentoNormalizado === 'PASSPORT') ? 'selected' : ''; ?>>PASSPORT</option>
                                                                            <option value="REGISTRO CIVIL" <?php echo ($tipoDocumentoNormalizado === 'REGISTRO CIVIL') ? 'selected' : ''; ?>>REGISTRO CIVIL</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label class="form-label">app_metadata (solo lectura)</label>
                                                                        <textarea class="form-control" rows="5" readonly><?php echo e(json_encode($usuario['app_metadata'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay usuarios en Auth0.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const RELOAD_DELAY_MS = 10000;

            const showWaitingAlert = function () {
                return Swal.fire({
                    title: 'Procesando cambio',
                    text: 'Espere un momento, estamos realizando el cambio en Auth0.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    timer: RELOAD_DELAY_MS,
                    timerProgressBar: true,
                    didOpen: function () {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.minHeight = '180px';
                        }
                    }
                });
            };

            const reloadAfterDelay = function () {
                const url = new URL(window.location.href);
                url.searchParams.set('t', Date.now().toString());

                showWaitingAlert();

                setTimeout(function () {
                    Swal.close();
                    window.location.href = url.toString();
                }, RELOAD_DELAY_MS);
            };

            const attachDeferredSubmit = function (selector) {
                document.querySelectorAll(selector).forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const submitUrl = form.getAttribute('action') || window.location.href;
                        const formData = new FormData(form);

                        reloadAfterDelay();

                        fetch(submitUrl, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).catch(function () {
                            // Reintentará la actualización en la siguiente carga si falla la solicitud.
                        });
                    });
                });
            };

            attachDeferredSubmit('form[data-toggle-block-form="1"]');
            attachDeferredSubmit('form[data-update-user-form="1"]');
            attachDeferredSubmit('form[data-delete-user-form="1"]');
        });
    </script>
</body>
</html>
