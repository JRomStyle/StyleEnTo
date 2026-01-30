<?php
class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';
            if (Auth::login($correo, $password)) {
                $this->redirect('/dashboard');
                return;
            }
            $this->render('auth/login', ['error' => 'Credenciales inválidas']);
            return;
        }
        $this->render('auth/login');
    }
    public function logout()
    {
        Auth::logout();
        $this->redirect('/login');
    }
}

