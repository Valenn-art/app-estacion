<div class="detalle-container">
    <a href="<?= BASE_URL ?>panel" class="btn-volver">Volver</a>
    
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando datos de la estación...</p>
    </div>
    
    <div id="error" class="error" style="display: none;">
         Error al cargar los datos. Por favor, intenta nuevamente.
    </div>
    
    <div id="detalle-contenido" style="display: none;">
        <div class="estacion-header-info">
            <h2 id="estacion-nombre">---</h2>
            <p id="estacion-ubicacion"><img src="<?= BASE_URL ?>assets/icons/ubicacion.png" alt="Ubicación" class="icon-inline"> ---</p>
            <p id="estacion-fecha"><img src="<?= BASE_URL ?>assets/icons/fecha.png" alt="estacion-fecha" class="icon-inline"> ---</p>
        </div>
        
        <div class="datos-grid">
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/temperatura.png" alt="Temperatura" class="dato-icon">
                <h3>Temperatura</h3>
                <p class="dato-valor"><span id="temperatura">--</span>°C</p>
                <div class="dato-minmax">
                    <span><img src="<?= BASE_URL ?>assets/icons/max.png" alt="Máximo" class="icon-small"> <span id="temp-max">--</span>°C</span>
                    <span><img src="<?= BASE_URL ?>assets/icons/min.png" alt="Mínimo" class="icon-small"> <span id="temp-min">--</span>°C</span>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/humedad.png" alt="Humedad" class="dato-icon">
                <h3>Humedad</h3>
                <p class="dato-valor"><span id="humedad">--</span>%</p>
                <p class="dato-extra">Máx: <span id="max-humedad">--</span>%</p>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/viento.png" alt="Viento" class="dato-icon">
                <h3>Viento</h3>
                <p class="dato-valor"><span id="viento">--</span> km/h</p>
                <p class="dato-extra"><span id="veleta">---</span></p>
                <p class="dato-extra">Máx: <span id="max-viento">--</span> km/h</p>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/fuego.png" alt="Índice de Fuego" class="dato-icon">
                <h3>Índice de Fuego (FWI)</h3>
                <p class="dato-valor"><span id="fwi">--</span></p>
                <p class="dato-extra">Sistema Canadiense</p>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/sensacion.png" alt="Sensación Térmica" class="dato-icon">
                <h3>Sensación Térmica</h3>
                <p class="dato-valor"><span id="sensacion">--</span>°C</p>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/presion.png" alt="Presión" class="dato-icon">
                <h3>Presión</h3>
                <p class="dato-valor"><span id="presion">--</span> hPa</p>
            </div>
        </div>
    </div>
</div>

<script>
const chipid = '<?= $chipid ?>';
const API_URL = '<?= $apiDetalle ?>?chipid=' + chipid + '&cant=1';

console.log('Chipid:', chipid);
console.log('API_URL:', API_URL);

document.addEventListener('DOMContentLoaded', async () => {
    await cargarDetalle();
});

async function cargarDetalle() {
    const loading = document.getElementById('loading');
    const error = document.getElementById('error');
    const contenido = document.getElementById('detalle-contenido');
    
    try {
        console.log('Haciendo fetch a:', API_URL);
        
        const response = await fetch(API_URL);
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const datos = await response.json();
        console.log('Datos recibidos:', datos);
        
        loading.style.display = 'none';
        
        // La API devuelve un array, tomamos el primer elemento
        const estacion = Array.isArray(datos) ? datos[0] : datos;
        
        if (!estacion) {
            throw new Error('No hay datos disponibles');
        }
        
        // Llenar datos
        document.getElementById('estacion-nombre').textContent = estacion.estacion || 'Sin nombre';
        document.getElementById('estacion-ubicacion').innerHTML = '<img src="<?= BASE_URL ?>assets/icons/ubicacion.png" alt="Ubicación" class="icon-inline"> ' + (estacion.ubicacion || 'Ubicación desconocida');
        document.getElementById('estacion-fecha').innerHTML = '<img src="<?= BASE_URL ?>assets/icons/fecha.png" alt="Hora" class="icon-inline"> ' + (estacion.fecha || '---');
        
        document.getElementById('temperatura').textContent = estacion.temperatura || '--';
        document.getElementById('temp-max').textContent = estacion.tempmax || '--';
        document.getElementById('temp-min').textContent = estacion.tempmin || '--';
        
        document.getElementById('humedad').textContent = estacion.humedad || '--';
        document.getElementById('max-humedad').textContent = estacion.maxhumedad || '--';
        
        document.getElementById('viento').textContent = estacion.viento || '--';
        document.getElementById('max-viento').textContent = estacion.maxviento || '--';
        document.getElementById('veleta').textContent = estacion.veleta || 'Indeterminado';
        
        document.getElementById('fwi').textContent = estacion.fwi || '--';
        document.getElementById('sensacion').textContent = estacion.sensacion || '--';
        document.getElementById('presion').textContent = estacion.presion || '--';
        
        contenido.style.display = 'block';
        
    } catch (err) {
        console.error('Error completo:', err);
        loading.style.display = 'none';
        error.style.display = 'block';
        error.textContent = ' Error: ' + err.message;
    }
}
</script>