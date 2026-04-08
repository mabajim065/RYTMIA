<template>
  <div class="asignacion-card">
    <h3 class="card-title">Asignar a Conjunto</h3>
    
    <div class="form-group">
      <select v-model="conjuntoSeleccionado" :disabled="cargando" class="rytmia-select">
        <option value="" disabled>Selecciona un conjunto...</option>
        <option v-for="conjunto in conjuntosDisponibles" :key="conjunto.id" :value="conjunto.id">
          {{ conjunto.nombre }} ({{ conjunto.categoria.nombre }})
        </option>
      </select>
    </div>

    <button @click="asignarGrupo" :disabled="!conjuntoSeleccionado || cargando" class="rytmia-btn">
      <span v-if="cargando">Asignando...</span>
      <span v-else>Guardar Asignación</span>
    </button>

    <div v-if="mensajeExito" class="feedback success">{{ mensajeExito }}</div>
    <div v-if="mensajeError" class="feedback error">{{ mensajeError }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  gimnastaId: {
    type: Number,
    required: true
  },
  conjuntosDisponibles: {
    type: Array,
    required: true
  }
});

const conjuntoSeleccionado = ref('');
const cargando = ref(false);
const mensajeExito = ref('');
const mensajeError = ref('');

const asignarGrupo = async () => {
  cargando.value = true;
  mensajeExito.value = '';
  mensajeError.value = '';

  try {
    const token = localStorage.getItem('rytmia_token');

    const response = await fetch(`/api/gimnastas/${props.gimnastaId}/asignar-conjunto`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        conjunto_id: conjuntoSeleccionado.value
      })
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Error al asignar el conjunto.');
    }

    mensajeExito.value = '¡Gimnasta asignada correctamente al conjunto!';
  } catch (error) {
    mensajeError.value = error.message;
  } finally {
    cargando.value = false;
  }
};
</script>

<style scoped>
.asignacion-card {
  background-color: var(--white, #ffffff);
  border-radius: var(--radius-md, 14px);
  padding: 1.5rem;
  box-shadow: var(--shadow-soft, 0 10px 30px rgba(107,26,58,.08));
  border: 1px solid var(--blush, #F2D5DF);
  max-width: 400px;
}

.card-title {
  color: var(--burgundy, #6B1A3A);
  margin-bottom: 1rem;
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.rytmia-select {
  width: 100%;
  padding: 0.75rem;
  border-radius: 8px;
  border: 1px solid var(--muted, #9B7080);
  background-color: var(--off-white, #FAF6F1);
  color: var(--text, #2A1520);
  font-family: 'DM Sans', sans-serif;
  outline: none;
}

.rytmia-select:focus {
  border-color: var(--rose, #C45C7E);
}

.rytmia-btn {
  width: 100%;
  background-color: var(--burgundy, #6B1A3A);
  color: #ffffff;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.rytmia-btn:hover:not(:disabled) {
  background-color: var(--rose, #C45C7E);
}

.rytmia-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.feedback {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 0.9rem;
}

.success {
  background-color: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.error {
  background-color: #ffebee;
  color: #c62828;
  border: 1px solid #ffcdd2;
}
</style>