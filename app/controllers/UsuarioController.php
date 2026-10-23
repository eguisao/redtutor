<?php

namespace App\Controllers;

use App\Models\Auth0UserModel;

class UsuarioController
{
    private static function currentUserRole(): string
    {
        $storedRole = $_SESSION['auth0_user_role'] ?? '';
        if (is_string($storedRole) && trim($storedRole) !== '') {
            return strtolower(trim($storedRole));
        }

        $user = $_SESSION['auth0_user'] ?? [];
        $candidates = [];

        $flattenRoleCandidates = function ($value, &$items) use (&$flattenRoleCandidates): void {
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $normalizedKey = strtolower((string) $key);
                    if (in_array($normalizedKey, ['role', 'roles', 'rol', 'tipo_usuario', 'tipousuario', 'userrole', 'user_role', 'perfil'], true)) {
                        $items[] = $item;
                    }

                    if (is_array($item)) {
                        $flattenRoleCandidates($item, $items);
                    }
                }
                return;
            }

            if (is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
                $items[] = $value;
            }
        };

        if (is_array($user)) {
            foreach (array_keys($user) as $key) {
                $normalizedKey = strtolower((string) $key);
                if (in_array($normalizedKey, ['role', 'roles', 'rol', 'tipo_usuario', 'tipousuario', 'userrole', 'user_role', 'perfil'], true)) {
                    $candidates[] = $user[$key];
                }
            }

            foreach (['app_metadata', 'user_metadata', 'metadata'] as $section) {
                if (isset($user[$section]) && is_array($user[$section])) {
                    $flattenRoleCandidates($user[$section], $candidates);
                }
            }

            $flattenRoleCandidates($user, $candidates);
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                foreach ($candidate as $item) {
                    $role = trim((string) $item);
                    if ($role !== '') {
                        return strtolower($role);
                    }
                }
                continue;
            }

            $role = trim((string) $candidate);
            if ($role !== '') {
                return strtolower($role);
            }
        }

        return '';
    }

    private static function canManageUsers(): bool
    {
        $role = self::currentUserRole();
        return in_array($role, ['admin', 'administrator', 'administrador'], true);
    }

    public static function index()
    {
        require_auth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::handleAction();
        }

        $usuarios = Auth0UserModel::getAllUsers();
        require __DIR__ . '/../views/usuarios.php';
    }

    private static function handleAction(): void
    {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'toggle_block') {
                $userId = $_POST['user_id'] ?? '';
                $currentUserId = $_SESSION['auth0_user_id'] ?? ($_SESSION['auth0_user']['sub'] ?? '');
                $blocked = !empty($_POST['blocked']) && $_POST['blocked'] === '1';

                if (trim((string) $currentUserId) !== '' && trim((string) $userId) === trim((string) $currentUserId)) {
                    $_SESSION['flash_error'] = 'No puedes bloquear tu propio usuario.';
                    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    header('Location: ' . base_url('/usuarios'));
                    exit;
                }

                Auth0UserModel::toggleUserStatus($userId, $blocked);
                $_SESSION['flash_success'] = 'Estado del usuario actualizado correctamente.';
            }

            if ($action === 'update_user') {
                $userId = $_POST['user_id'] ?? '';
                $payload = [
                    'name' => $_POST['name'] ?? '',
                    'nickname' => $_POST['nickname'] ?? '',
                    'blocked' => isset($_POST['blocked']) && $_POST['blocked'] === '1',
                ];

                $existingMetadata = [];
                if (!empty($_POST['user_metadata_json'])) {
                    $existingMetadata = Auth0UserModel::parseJsonMetadata($_POST['user_metadata_json']);
                }

                $metadata = is_array($existingMetadata) ? $existingMetadata : [];

                $fechaNacimiento = trim((string) ($_POST['fecha_nacimiento'] ?? ''));
                $numeroDocumento = trim((string) ($_POST['numero_documento'] ?? ''));
                $tipoUsuario = trim((string) ($_POST['tipo_usuario'] ?? ''));
                $tipoDocumento = trim((string) ($_POST['tipo_documento'] ?? ''));

                if ($fechaNacimiento !== '') {
                    $metadata['fecha_nacimiento'] = $fechaNacimiento;
                } else {
                    unset($metadata['fecha_nacimiento']);
                }

                if ($numeroDocumento !== '') {
                    $metadata['numero_documento'] = (string) $numeroDocumento;
                } else {
                    unset($metadata['numero_documento']);
                }

                if ($tipoUsuario !== '') {
                    $metadata['tipo_usuario'] = $tipoUsuario;
                } else {
                    unset($metadata['tipo_usuario']);
                }

                if ($tipoDocumento !== '') {
                    $metadata['tipo_documento'] = $tipoDocumento;
                } else {
                    unset($metadata['tipo_documento']);
                }

                if (!empty($metadata)) {
                    $payload['user_metadata'] = $metadata;
                }

                Auth0UserModel::updateUser($userId, $payload);
                $_SESSION['flash_success'] = 'Usuario actualizado correctamente en Auth0.';
            }

            if ($action === 'delete_user') {
                $userId = $_POST['user_id'] ?? '';
                $currentUserId = $_SESSION['auth0_user_id'] ?? ($_SESSION['auth0_user']['sub'] ?? '');

                if (trim((string) $currentUserId) !== '' && trim((string) $userId) === trim((string) $currentUserId)) {
                    $_SESSION['flash_error'] = 'No puedes eliminar tu propio usuario.';
                    $redirectUrl = base_url('/usuarios') . '?t=' . time();
                    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                Auth0UserModel::deleteUser($userId);
                $_SESSION['flash_success'] = 'Usuario eliminado correctamente.';
            }
        } catch (\InvalidArgumentException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        } catch (\RuntimeException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        $redirectUrl = base_url('/usuarios') . '?t=' . time();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Location: ' . $redirectUrl);
        exit;
    }
}
