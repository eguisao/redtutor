<?php

namespace App\Controllers;

use App\Models\MateriaModel;

class MateriaController
{
    public static function index()
    {
        require_auth();

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $materias = MateriaModel::getAll();
        require __DIR__ . '/../views/materias.php';
    }

    public static function handleAction()
    {
        require_auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('/materias'));
            exit;
        }

        $action = $_POST['action'] ?? 'update';
        $id = (int)($_POST['id_materia'] ?? 0);

        if ($action === 'toggle_status') {
            if ($id <= 0) {
                $_SESSION['flash_error'] = 'No se pudo identificar la materia.';
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Location: ' . base_url('/materias'));
                exit;
            }

            $activa = isset($_POST['activa']) ? (int)$_POST['activa'] : 0;
            $updated = MateriaModel::toggleStatus($id, $activa);

            if ($updated) {
                $_SESSION['flash_success'] = $activa ? 'La materia fue activada correctamente.' : 'La materia fue desactivada correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo cambiar el estado de la materia.';
            }

            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Location: ' . base_url('/materias'));
            exit;
        }

        if ($action === 'delete') {
            if ($id <= 0) {
                $_SESSION['flash_error'] = 'No se pudo identificar la materia.';
                $redirectUrl = base_url('/materias') . '?t=' . time();
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Pragma: no-cache');
                header('Expires: 0');
                header('Location: ' . $redirectUrl);
                exit;
            }

            $deleted = MateriaModel::delete($id);

            if ($deleted) {
                $_SESSION['flash_success'] = 'La materia fue eliminada correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo eliminar la materia.';
            }

            $redirectUrl = base_url('/materias') . '?t=' . time();
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $codigo = trim((string)($_POST['codigo'] ?? ''));
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        $area = trim((string)($_POST['area'] ?? ''));

        if ($codigo === '' || $nombre === '') {
            $_SESSION['flash_error'] = 'El código y el nombre de la materia son obligatorios.';
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Location: ' . base_url('/materias'));
            exit;
        }

        if ($action === 'create') {
            $created = MateriaModel::create([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'area' => $area,
                'activa' => 1,
            ]);

            if ($created) {
                $_SESSION['flash_success'] = 'La materia fue creada correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo crear la materia.';
            }

            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Location: ' . base_url('/materias'));
            exit;
        }

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'No se pudo identificar la materia.';
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Location: ' . base_url('/materias'));
            exit;
        }

        $updated = MateriaModel::update($id, [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'area' => $area,
        ]);

        if ($updated) {
            $_SESSION['flash_success'] = 'La materia fue actualizada correctamente.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar la materia.';
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Location: ' . base_url('/materias'));
        exit;
    }
}
