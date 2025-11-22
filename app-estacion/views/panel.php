<div class="panel-container">
    <h1>Estaciones Meteorológicas Disponibles</h1>
    
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando estaciones...</p>
    </div>
    
    <div id="error" class="error" style="display: none;">
        <img src="<?= BASE_URL ?>assets/icons/error.png" alt="Error" class="icon-inline"> Error al cargar las estaciones. Por favor, intenta nuevamente.
    </div>
    
    <ul id="lista-estaciones" class="lista-estaciones">
    </ul>
</div>

<template id="template-estacion">
    <li class="estacion-item">
        <a href="#" class="estacion-btn">
            <div class="estacion-header">
                <h3 class="estacion-apodo">---</h3>
                <span class="estacion-badge">
                    <span class="badge-icon"><img src="<?= BASE_URL ?>assets/icons/ojo.png" alt="Visitas" class="icon-inline"></span>
                    <span class="num-visitas">0</span>
                </span>
            </div>
            <p class="estacion-ubicacion">---</p>
            <div class="estacion-footer">
                <span class="estacion-estado activo">
                    <span class="dot"></span> Activa
                </span>
            </div>
        </a>
    </li>
</template>

<script>
// API URL
const API_URL = '<?= $apiEstaciones ?>';

console.log('API_URL:', API_URL); // Debug

// Cargar estaciones al inicio
document.addEventListener('DOMContentLoaded', async () => {
    await cargarEstaciones();
});

async function cargarEstaciones() {
    const loading = document.getElementById('loading');
    const error = document.getElementById('error');
    const lista = document.getElementById('lista-estaciones');
    
    try {
        console.log('Intentando fetch a:', API_URL); // Debug
        
        const response = await fetch(API_URL);
        
        console.log('Response status:', response.status); // Debug
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const estaciones = await response.json();
        console.log('Estaciones recibidas:', estaciones); // Debug
        
        loading.style.display = 'none';
        
        if (!estaciones || estaciones.length === 0) {
            throw new Error('No hay estaciones disponibles');
        }
        
        estaciones.forEach((estacion, index) => {
            setTimeout(() => {
                agregarEstacion(estacion);
            }, index * 100);
        });
        
    } catch (err) {
        console.error('Error completo:', err);
        loading.style.display = 'none';
        error.style.display = 'block';
        error.innerHTML = '<img src="<?= BASE_URL ?>assets/icons/error.png" alt="Error" class="icon-inline"> Error: ' + err.message;
    }
}

function agregarEstacion(estacion) {
    const template = document.getElementById('template-estacion');
    const clone = template.content.cloneNode(true);
    
    const apodo = estacion.apodo || 'Sin nombre';
    const ubicacion = estacion.ubicacion || 'Ubicación desconocida';
    const visitas = estacion.visitas || '0';
    const diasInactivo = parseInt(estacion.dias_inactivo || 0);
    
    clone.querySelector('.estacion-apodo').textContent = apodo;
    clone.querySelector('.estacion-ubicacion').textContent = ubicacion;
    clone.querySelector('.num-visitas').textContent = visitas;
    
    const estadoElement = clone.querySelector('.estacion-estado');
    if (diasInactivo > 0) {
        estadoElement.innerHTML = `<span class="dot"></span> Inactiva ${diasInactivo} días`;
        estadoElement.classList.remove('activo');
        estadoElement.classList.add('inactivo');
    }
    
    const link = clone.querySelector('.estacion-btn');
    link.href = '<?= BASE_URL ?>detalle/' + estacion.chipid;
    
    document.getElementById('lista-estaciones').appendChild(clone);
}
</script>