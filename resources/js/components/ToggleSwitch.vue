<template>
  <div class="switch-container">
    <button
      class="switch-button"
      :class="{ active: currentView === 'list' }"
      @click="switchView('list')"
    >
      <i class="fas fa-list"></i> Liste
    </button>
    <button
      class="switch-button"
      :class="{ active: currentView === 'graph' }"
      @click="switchView('graph')"
    >
      <i class="fas fa-map"></i> Carte
    </button>
  </div>
</template>

<script>
export default {
  props: {
    currentView: {
      type: String,
      required: true,
    },
  },
  methods: {
    switchView(view) {
      this.$emit("switch-view", view);
    },
  },
};
</script>

<style scoped>
.switch-container {
  display: flex;
  border: 1px solid #000000;
  border-radius: 25px;
  overflow: hidden;
}

.switch-button {
  flex: 1;
  padding: 7px 7px;
  background-color: #ffffff;
  border: none;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #000000;
}

.switch-button.active {
  background-color: #4C7DFB;
  color: rgb(255, 255, 255);
}

.switch-button i {
  margin-right: 5px;
  
}
</style>
