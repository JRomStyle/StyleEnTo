<?php
class UsuarioController extends Controller
{
    public function index()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo '403';
            return;
        }
        $usuarios = Usuario::all();
        $this->render('usuarios/index', ['usuarios' => $usuarios]);
    }
    public function crear()
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo '403';
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';
            $rol = $_POST['rol'] ?? 'asistente';
            if ($rol === 'admin') {
                Usuario::createAdmin($nombre, $correo, $password);
            } else {
                Usuario::createAssistant($nombre, $correo, $password);
            }
        }
        $this->redirect('/usuarios');
    }
}

