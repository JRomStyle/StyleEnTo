<?php
class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::all();
        $clientes = Cliente::all();
        $this->render('documentos/index', ['documentos' => $documentos, 'clientes' => $clientes]);
    }
    public function upload()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $allowed = $config['security']['allowed_extensions'];
        $max = (int)$config['security']['max_upload_size'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente_id = (int)($_POST['cliente_id'] ?? 0);
            $tipo_documento = $_POST['tipo_documento'] ?? 'renta';
            if (!isset($_FILES['archivo']) || !$cliente_id) {
                $this->redirect('/documentos');
                return;
            }
            $file = $_FILES['archivo'];
            if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > $max) {
                $this->redirect('/documentos');
                return;
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $this->redirect('/documentos');
                return;
            }
            $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $finalName = $safeName . '_' . uniqid() . '.' . $ext;
            $destDir = $config['app']['upload_dir'];
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0777, true);
            }
            $destPath = $destDir . '/' . $finalName;
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                Documento::create($cliente_id, $tipo_documento, $finalName, date('Y-m-d H:i:s'));
            }
        }
        $this->redirect('/documentos');
    }
}

