<template>
  <v-app>
    <v-main>
    
    
   <recherche></recherche>
   <!-- <Switch></Switch> -->
<!-- <essai></essai> -->
<Footer></Footer>
     
    </v-main>
  </v-app>
</template>

<script>

import Recherche from './components/Recherche.vue'; // Importez le nouveau composant
import Footer from './components/Footer.vue';
// import Switch from './components/ToggleSwitch.vue';
// import Switch from './components/Switch.vue';

export default {
  components: {
    
    Recherche, // Enregistrez le nouveau composant
    Footer,
    // Switch
    
  },
};
</script>

<style>
.header {
    text-align: center;
    margin-top: 20px;
    margin-bottom: 30px;
}

.header h1 {
    font-family: 'Georgia', serif;
    font-weight: bold;
    font-size: 24px;
    margin: 0;
}

.header p {
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    margin: 5px 0;
}

.highlight {
    font-weight: bold;
    margin-left: 5px;
}
</style>