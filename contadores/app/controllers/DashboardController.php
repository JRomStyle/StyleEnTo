<?php
class DashboardController extends Controller
{
    public function index()
    {
        $clientes = Cliente::count();
        $docPendientes = Documento::countPendientes();
        $vencimientosProximos = Vencimiento::countProximos7Dias();
        $alertasFiscales = $vencimientosProximos;
        $this->render('dashboard/index', [
            'clientes' => $clientes,
            'docPendientes' => $docPendientes,
            'vencimientosProximos' => $vencimientosProximos,
            'alertasFiscales' => $alertasFiscales
        ]);
    }
}

