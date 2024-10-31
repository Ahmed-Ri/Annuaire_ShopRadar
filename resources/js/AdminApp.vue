<template>
  <v-app>
    <v-main>
     <magasins-table></magasins-table>
     <!-- <recherche></recherche> -->
     <category></category>
    </v-main>
  </v-app>
</template>

<script>
import MagasinsTable from './components/MagasinsTable.vue';
import Recherche from './components/Recherche.vue'; 
import Category from './components/Category.vue';


export default {
  components: {
    MagasinsTable,
    Recherche, 
    Category
    
  },
};
</script>
