<?php
class VencimientoController extends Controller
{
    public function index()
    {
        $vencimientos = Vencimiento::allUpcoming();
        $clientes = Cliente::all();
        $this->render('vencimientos/index', ['vencimientos' => $vencimientos, 'clientes' => $clientes]);
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente_id = (int)($_POST['cliente_id'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $fecha_limite = $_POST['fecha_limite'] ?? date('Y-m-d');
            $estado = $_POST['estado'] ?? 'pendiente';
            if ($cliente_id && $descripcion) {
                Vencimiento::create($cliente_id, $descripcion, $fecha_limite, $estado);
            }
        }
        $this->redirect('/vencimientos');
    }
    public function pagar()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            Vencimiento::markPagado($id);
        }
        $this->redirect('/vencimientos');
    }
}

