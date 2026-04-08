import './bootstrap';
import { createApp } from 'vue';

// Importamos el futuro componente del Punto 10
import AsignarGimnasta from './components/AsignarGimnasta.vue';

// Creamos la instancia de la aplicación Vue
const app = createApp({});

// Registramos el componente globalmente para usarlo en cualquier vista Blade
app.component('asignar-gimnasta', AsignarGimnasta);

// Montamos la aplicación en el div con id="app"
app.mount('#app');