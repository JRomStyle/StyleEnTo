<?php
class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        $this->render('clientes/index', ['clientes' => $clientes]);
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'] ?? 'natural';
            $nombre = trim($_POST['nombre'] ?? '');
            $documento = trim($_POST['documento'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $regimen = $_POST['regimen'] ?? null;
            Cliente::create($tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen);
            $this->redirect('/clientes');
            return;
        }
        $this->render('clientes/create');
    }
    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $tipo = $_POST['tipo'] ?? 'natural';
            $nombre = trim($_POST['nombre'] ?? '');
            $documento = trim($_POST['documento'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $regimen = $_POST['regimen'] ?? null;
            Cliente::update($id, $tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen);
            $this->redirect('/clientes');
            return;
        }
        $id = (int)($_GET['id'] ?? 0);
        $cliente = Cliente::find($id);
        $this->render('clientes/edit', ['cliente' => $cliente]);
    }
    public function delete()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo '403';
            return;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            Cliente::delete($id);
        }
        $this->redirect('/clientes');
    }
}

