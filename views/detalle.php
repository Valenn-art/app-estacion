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
                <div class="grafico-container">
                    <canvas id="graficoTemperatura"></canvas>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/humedad.png" alt="Humedad" class="dato-icon">
                <h3>Humedad</h3>
                <p class="dato-valor"><span id="humedad">--</span>%</p>
                <p class="dato-extra">Máx: <span id="max-humedad">--</span>%</p>
                <div class="grafico-container">
                    <canvas id="graficoHumedad"></canvas>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/viento.png" alt="Viento" class="dato-icon">
                <h3>Viento</h3>
                <p class="dato-valor"><span id="viento">--</span> km/h</p>
                <p class="dato-extra"><span id="veleta">---</span></p>
                <p class="dato-extra">Máx: <span id="max-viento">--</span> km/h</p>
                <div class="grafico-container">
                    <canvas id="graficoViento"></canvas>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/presion.png" alt="Presión" class="dato-icon">
                <h3>Presión Atmosférica</h3>
                <p class="dato-valor"><span id="presion">--</span> hPa</p>
                <div class="grafico-container">
                    <canvas id="graficoPresion"></canvas>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/fuego.png" alt="Índice de Fuego" class="dato-icon">
                <h3>Índice de Fuego (FWI)</h3>
                <p class="dato-valor"><span id="fwi">--</span></p>
                <p class="dato-extra">Sistema Canadiense</p>
                <div class="grafico-container">
                    <canvas id="graficoFWI"></canvas>
                </div>
            </div>
            
            <div class="dato-card">
                <img src="<?= BASE_URL ?>assets/icons/sensacion.png" alt="Sensación Térmica" class="dato-icon">
                <h3>Sensación Térmica</h3>
                <p class="dato-valor"><span id="sensacion">--</span>°C</p>
            </div>
        </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chipid = '<?= $chipid ?>';
const API_URL = '<?= $apiDetalle ?>?chipid=' + chipid + '&cant=20';

let graficos = {};
let intervalId = null;

console.log('Chipid:', chipid);
console.log('API_URL:', API_URL);

document.addEventListener('DOMContentLoaded', async () => {
    await cargarDetalle();
    
    intervalId = setInterval(async () => {
        console.log('Actualizando datos automáticamente...');
        await cargarDetalle();
    }, 60000); // 60000 ms = 1 minuto
});

window.addEventListener('beforeunload', () => {
    if (intervalId) {
        clearInterval(intervalId);
    }
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
        
        if (!Array.isArray(datos) || datos.length === 0) {
            throw new Error('No hay datos disponibles');
        }
        
        const estacion = datos[0];
        
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
        
        const ahora = new Date();
        
        crearGraficos(datos);
        
        contenido.style.display = 'block';
        
    } catch (err) {
        console.error('Error completo:', err);
        loading.style.display = 'none';
        error.style.display = 'block';
        error.textContent = ' Error: ' + err.message;
    }
}

function crearGraficos(datos) {
    const datosOrdenados = [...datos].reverse();
    
    const etiquetas = datosOrdenados.map(d => {
        const fecha = new Date(d.fecha);
        return fecha.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });
    });
    
    const temperaturas = datosOrdenados.map(d => parseFloat(d.temperatura) || 0);
    const humedades = datosOrdenados.map(d => parseFloat(d.humedad) || 0);
    const vientos = datosOrdenados.map(d => parseFloat(d.viento) || 0);
    const presiones = datosOrdenados.map(d => parseFloat(d.presion) || 0);
    const fwis = datosOrdenados.map(d => parseFloat(d.fwi) || 0);
    
    // Gráfico Temperatura
    crearOActualizarGrafico('graficoTemperatura', {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperaturas,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    
    // Gráfico Humedad
    crearOActualizarGrafico('graficoHumedad', {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Humedad (%)',
                data: humedades,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
    
    crearOActualizarGrafico('graficoViento', {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Viento (km/h)',
                data: vientos,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    crearOActualizarGrafico('graficoPresion', {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Presión (hPa)',
                data: presiones,
                borderColor: 'rgb(153, 102, 255)',
                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    
    crearOActualizarGrafico('graficoFWI', {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'FWI',
                data: fwis,
                backgroundColor: fwis.map(valor => {
                    if (valor < 5) return 'rgba(75, 192, 192, 0.7)';
                    if (valor < 10) return 'rgba(255, 206, 86, 0.7)';
                    if (valor < 20) return 'rgba(255, 159, 64, 0.7)';
                    return 'rgba(255, 99, 132, 0.7)';
                }),
                borderColor: fwis.map(valor => {
                    if (valor < 5) return 'rgb(75, 192, 192)';
                    if (valor < 10) return 'rgb(255, 206, 86)';
                    if (valor < 20) return 'rgb(255, 159, 64)';
                    return 'rgb(255, 99, 132)';
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function crearOActualizarGrafico(canvasId, config) {
    const ctx = document.getElementById(canvasId);
    
    if (!ctx) {
        console.error('Canvas no encontrado:', canvasId);
        return;
    }
    
    if (graficos[canvasId]) {
        graficos[canvasId].data = config.data;
        graficos[canvasId].update('none'); 
    } else {
        graficos[canvasId] = new Chart(ctx, config);
    }
}
</script>