<?php

class EstacionController {
    
    private $model;
    
    public function __construct() {
        $this->model = new EstacionModel();
    }
    
    public function landing() {
        $this->render('landing', [
            'titulo' => 'App Estación Meteorológica',
            'css' => 'landing'  
        ]);
    }
    
    public function panel() {
        $this->render('panel', [
            'titulo' => 'Panel de Estaciones',
            'css' => 'panel',
            'apiEstaciones' => $this->model->getApiEstaciones()
        ]);
    }
    
    public function detalle($chipid) {
        if (!SessionManager::estaLogueado()) {
            SessionManager::guardarDestino(BASE_URL . 'detalle/' . $chipid);
            
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        $this->render('detalle', [
            'titulo' => 'Detalles',
            'chipid' => $chipid,
            'css' => 'detalle',
            'apiDetalle' => $this->model->getApiDetalle()
        ]);
    }
    
    private function render($vista, $datos = []) {
        extract($datos);
        include dirname(__DIR__) . "/templates/header.php";
        include dirname(__DIR__) . "/views/$vista.php";
        include dirname(__DIR__) . "/templates/footer.php";
    }
}
?>