<?php

class EstacionController {
    
    private $model;
    
    public function __construct() {
        $this->model = new EstacionModel();
    }
    
    // Vista landing
    public function landing() {
        $this->render('landing', [
            'titulo' => 'App Estación Meteorológica',
            'css' => 'landing'  
        ]);
    }
    
    // Vista panel de estaciones
    public function panel() {
        $this->render('panel', [
            'titulo' => 'Panel de Estaciones',
            'css' => 'panel',
            'apiEstaciones' => $this->model->getApiEstaciones()
        ]);
    }
    
    // Vista detalle de estación
    public function detalle($chipid) {
        $this->render('detalle', [
            'titulo' => 'Detalles',
            'chipid' => $chipid,
            'css' => 'detalle',
            'apiDetalle' => $this->model->getApiDetalle()
        ]);
    }
    
    // Motor de plantillas simple
    private function render($vista, $datos = []) {
        extract($datos);
        include dirname(__DIR__) . "/templates/header.php";
        include dirname(__DIR__) . "/views/$vista.php";
        include dirname(__DIR__) . "/templates/footer.php";
    }
}
?>