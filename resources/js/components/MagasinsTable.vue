<template>
    <div class="container p-4">
        
     <!-- Barre de recherche avec icône -->
     <div class="search-container ml-4">
      <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input 
          type="text" 
          v-model="searchQuery" 
          @input="filterMagasins" 
          placeholder="Rechercher un magasin..."
          class="search-input"
        />
      </div>
      <ul v-if="filteredMagasins.length && searchQuery" class="autocomplete-list">
        <li 
          v-for="suggestion in filteredMagasins" 
          :key="suggestion.id" 
          @click="selectSuggestion(suggestion)" 
          class="autocomplete-item"
        >
          {{ suggestion.nom }}
        </li>
      </ul>
    </div>

    <!-- Tableau des magasins -->
    <div class="table-container card p-4">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ENTREPRISE</th>
            <th>ACTIVITE</th>
            <th>ADRESSE</th>
            <th>TELEPHONE</th>
            <th>IMAGE</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="magasin in displayedMagasins" :key="magasin.id">
            <td>{{ magasin.nom }}</td>
            <td>{{ magasin.ACTIVITE }}</td>
            <td>{{ magasin.adresse }}</td>
            <td>{{ magasin.tel }}</td>
            <td>
              <div class="image-upload-container">
                <div v-if="magasin.image">
                  <img
                    :src="magasin.image"
                    alt="Image du magasin"
                    class="magasin-image"
                    @click="triggerFileInput(magasin.id)"
                  />
                </div>
                <a href="#" v-else @click.prevent="triggerFileInput(magasin.id)" class="image-upload-link">
                  Inserer
                </a>
                <input
                  type="file"
                  :id="'file-input-' + magasin.id"
                  style="display: none"
                  @change="handleFileUpload($event, magasin.id)"
                />
              </div>
            </td>
            <td>
              <div class="actions-container">
                <button class="btn associer-btn" @click="openAssignCategoryModal(magasin)">Associer</button>
                <button class="btn voir-btn" @click="openShowCategoriesModal(magasin)">Voir</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination seulement quand aucune recherche n'est faite -->
      <v-pagination
        v-if="!searchQuery || !selectedMagasin"
        v-model="currentPage"
        :length="totalPages"
        :total-visible="5"
        size="small"
        class="mt-1"
      />
    </div>
      <transition name="modal-fade">
            <div
                v-if="showAssignCategoryModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center"
                style="z-index: 9999"
            >
                <div class="bg-white p-6 rounded-lg shadow-xl w-80">
                    <!-- Menu déroulant pour la catégorie principale -->
                    <div class="relative mb-4">
                        <button
                            @click="toggleDropdown('mainCategory')"
                            class="border border-gray-300 p-2 w-full rounded custom-input text-left flex justify-between items-center"
                        >
                            {{ selectedMainCategoryName || "Catégorie" }}
                            <span
                                ><i class="fas fa-chevron-down icon-small"></i
                            ></span>
                            <!-- Réduire la taille avec icon-small -->
                        </button>
                        <ul
                            v-if="dropdownOpen === 'mainCategory'"
                            class="absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-auto"
                        >
                            <li
                                v-for="category in mainCategories"
                                :key="category.id"
                                @click="selectMainCategory(category)"
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                            >
                                {{ category.name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Menu déroulant pour la sous-catégorie -->
                    <div class="relative mb-4">
                        <button
                            @click="toggleDropdown('subCategory')"
                            class="border border-gray-300 p-2 w-full rounded custom-input text-left flex justify-between items-center"
                        >
                            {{ selectedSubCategoryName || "Sous catégorie" }}
                            <span
                                ><i class="fas fa-chevron-down icon-small"></i
                            ></span>
                            <!-- Réduire la taille avec icon-small -->
                        </button>
                        <ul
                            v-if="dropdownOpen === 'subCategory'"
                            class="absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-auto"
                        >
                            <li
                                @click="
                                    selectSubCategory({
                                        id: 'all',
                                        name: 'Tout',
                                    })
                                "
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                            >
                                Tout
                            </li>
                            <li
                                v-for="category in subCategories"
                                :key="category.id"
                                @click="selectSubCategory(category)"
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                            >
                                {{ category.name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Menu déroulant pour la sous-sous-catégorie -->
                    <div class="relative mb-4">
                        <button
                            @click="toggleDropdown('subSubCategory')"
                            class="border border-gray-300 p-2 w-full rounded custom-input text-left flex justify-between items-center"
                        >
                            {{
                                selectedSubSubCategoryName ||
                                "Sous sous catégorie"
                            }}
                            <span
                                ><i class="fas fa-chevron-down icon-small"></i
                            ></span>
                            <!-- Réduire la taille avec icon-small -->
                        </button>
                        <ul
                            v-if="dropdownOpen === 'subSubCategory'"
                            class="absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-auto"
                        >
                            <li
                                @click="
                                    selectSubSubCategory({
                                        id: 'all',
                                        name: 'Tout',
                                    })
                                "
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                            >
                                Tout
                            </li>
                            <li
                                v-for="category in subSubCategories"
                                :key="category.id"
                                @click="selectSubSubCategory(category)"
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                            >
                                {{ category.name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex justify-center mt-auto">
                        <button
                            @click="assignCategoryToMagasin"
                            class="bouton text-white px-2 py-1 rounded"
                        >
                            Affecter
                        </button>
                        <button
                            @click="closeAssignCategoryModal"
                            class="bouton text-white px-2 py-1 rounded ml-2"
                        >
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Modal pour Afficher et Supprimer les Catégories -->
        <transition
            name="modal-fade"
            class="mt-5"
            style="overflow: hidden !important"
        >
            <div
                v-if="showShowCategoriesModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center mt-5"
                style="z-index: 9999"
            >
                <div
                    class="bg-white p-6 rounded-lg shadow-xl modal-large flex flex-col justify-center items-center"
                >
                    <!-- Conteneur du tableau -->
                    <div
                        class="table-container w-full"
                        style="max-height: 400px; overflow-y: auto"
                    >
                        <!-- Tableau des catégories avec les styles de lignes ajoutés -->
                        <table class="w-full table-auto mb-4">
                            <thead class="sticky top-0 bg-white">
                                <tr>
                                    <th class="text-left">
                                        Catégorie Principale
                                    </th>
                                    <th class="text-left">Sous-catégorie</th>
                                    <th class="text-left">
                                        Sous-sous-catégorie
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        category, index
                                    ) in selectedMagasinCategories"
                                    :key="category.category_id"
                                    :class="{
                                        'border-t-4 border-gray-400':
                                            isFirstOccurrence(
                                                index,
                                                'main_category_id'
                                            ),
                                        'border-t border-gray-300':
                                            !isFirstOccurrence(
                                                index,
                                                'main_category_id'
                                            ) && category.sub,
                                        'border-t border-gray-200':
                                            category.sub_sub,
                                    }"
                                >
                                    <!-- Colonne pour la catégorie principale -->
                                    <td>
                                        {{ category.main }}
                                        <!-- Icône "trash" uniquement pour la première occurrence de la catégorie principale -->
                                        <button
                                            v-if="
                                                isFirstOccurrence(
                                                    index,
                                                    'main_category_id'
                                                )
                                            "
                                            @click="
                                                removeMainCategory(
                                                    category.main_category_id
                                                )
                                            "
                                            class="text-red-500 hover:text-red-700 ml-2"
                                        >
                                            <i
                                                class="fas fa-times-circle"
                                                style="color: red"
                                            ></i>
                                        </button>
                                    </td>

                                    <!-- Colonne pour la sous-catégorie -->
                                    <td>
                                        {{ category.sub }}
                                        <!-- Icône "trash" uniquement pour la première occurrence de la sous-catégorie -->
                                        <button
                                            v-if="
                                                isFirstOccurrence(
                                                    index,
                                                    'subcategory_id'
                                                )
                                            "
                                            @click="
                                                removeSubCategory(
                                                    category.subcategory_id
                                                )
                                            "
                                            class="text-red-500 hover:text-red-700 ml-2"
                                        >
                                            <i
                                                class="fas fa-times-circle"
                                                style="color: red"
                                            ></i>
                                        </button>
                                    </td>

                                    <!-- Colonne pour la sous-sous-catégorie -->
                                    <td>
                                        {{ category.sub_sub }}
                                        <!-- Icône "trash" pour la sous-sous-catégorie (aucun changement nécessaire ici) -->
                                        <button
                                            v-if="category.sub_sub"
                                            @click="
                                                removeSubSubCategory(
                                                    category.category_id
                                                )
                                            "
                                            class="text-red-500 hover:text-red-700 ml-2"
                                        >
                                            <i
                                                class="fas fa-times-circle"
                                                style="color: red"
                                            ></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bouton pour fermer la modale -->
                    <div class="flex justify-end">
                        <button
                            @click="closeShowCategoriesModal"
                            class="hover:bg-gray-600 text-white px-3 py-1 mt-2 rounded"
                            style="background-color: #4c7dfb"
                        >
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
  </template>
<script>
import axios from "axios";

export default {
    data() {
        return {
            magasins: [],
            filteredMagasins: [], // Magasins filtrés pour autocomplétion
            searchQuery: '', 
            mainCategories: [],
            subCategories: [],
            subSubCategories: [],
            dropdownOpen: null, // Gestion de l'ouverture des dropdowns
            selectedMagasin: null,
            selectedMagasinForModal: null,
            selectedMainCategoryId: "",
            selectedSubCategoryId: "",
            selectedSubSubCategoryId: "",
            selectedMainCategoryName: "",
            selectedSubCategoryName: "",
            selectedSubSubCategoryName: "",
            selectedMagasinCategories: [],
            showAssignCategoryModal: false,
            showShowCategoriesModal: false,
            currentPage: 1, // Page actuelle
            itemsPerPage: 5, // Nombre d'éléments par page
        };
    },
    mounted() {
        this.fetchMagasins();
        this.fetchMainCategories();
    },
    computed: {
        // Magasins paginés à afficher
        paginatedMagasins() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = this.currentPage * this.itemsPerPage;
            return this.magasins.slice(start, end);
        },
        
        // Calcul du nombre total de pages
        totalPages() {
            return Math.ceil(this.magasins.length / this.itemsPerPage);
        },
        displayedMagasins() {
      // Si un magasin est sélectionné, on affiche uniquement celui-ci
      if (this.selectedMagasinForModal) {
        return [this.selectedMagasinForModal];
      }
      // Sinon, on retourne les magasins filtrés en fonction de la recherche
      const filtered = this.searchQuery ? this.filteredMagasins : this.magasins;
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return filtered.slice(start, start + this.itemsPerPage);
    },
    totalPages() {
      const count = this.searchQuery ? this.filteredMagasins.length : this.magasins.length;
      return Math.ceil(count / this.itemsPerPage);
    }
    },
    methods: {
       // Filtrer les magasins en fonction de la recherche
    filterMagasins() {
      const query = this.searchQuery.toLowerCase();
      this.filteredMagasins = this.magasins.filter(magasin => {
        return magasin.nom.toLowerCase().includes(query) || magasin.ACTIVITE.toLowerCase().includes(query);
      });
      this.selectedMagasinForModal = null; // Réinitialiser la sélection
    },
    // Sélection d'une suggestion d'autocomplétion
    selectSuggestion(suggestion) {
      this.searchQuery = suggestion.nom;
      this.selectedMagasinForModal = suggestion; // Afficher uniquement le magasin sélectionné
      this.filteredMagasins = [];
    },
        fetchMagasins() {
            axios
                .get("/api/magasins")
                .then((response) => {
                    this.magasins = response.data;
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la récupération des magasins:",
                        error
                    );
                });
        },
        uploadImage(magasinId) {
            // Ouvre la boîte de dialogue pour sélectionner une image
            const inputElement = document.getElementById(
                "file-input-" + magasinId
            );
            inputElement.click();
        },
        triggerFileInput(magasinId) {
            const fileInput = document.getElementById(
                "file-input-" + magasinId
            );
            if (fileInput) {
                fileInput.click(); // Simuler un clic sur l'input file caché
            } else {
                console.error(
                    `Impossible de trouver l'input pour le magasin ${magasinId}`
                );
            }
        },

        // Méthode pour gérer le fichier uploadé
        handleFileUpload(event, magasinId) {
            const file = event.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append("image", file);
                formData.append("magasin_id", magasinId);

                // Requête API pour uploader l'image
                axios
                    .post(`/api/magasins/${magasinId}/upload-image`, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        console.log(
                            "Image uploadée avec succès:",
                            response.data
                        );
                        // Mettre à jour l'image du magasin dans l'interface
                        this.updateMagasinImage(
                            magasinId,
                            response.data.imageUrl
                        );
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de l'upload de l'image:",
                            error
                        );
                    });
            }
        },
        isFirstOccurrence(index, type) {
            // `type` peut être 'main_category_id' ou 'subcategory_id'
            return (
                this.selectedMagasinCategories.findIndex(
                    (category) =>
                        category[type] ===
                        this.selectedMagasinCategories[index][type]
                ) === index
            );
        },
        // Mettre à jour l'image locale du magasin après l'upload
        updateMagasinImage(magasinId, newImageUrl) {
            const magasin = this.paginatedMagasins.find(
                (m) => m.id === magasinId
            );
            if (magasin) {
                magasin.image = newImageUrl; // Mettre à jour l'image du magasin
            }
        },
        fetchMainCategories() {
            axios
                .get("/api/categories")
                .then((response) => {
                    this.mainCategories = response.data;
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la récupération des catégories principales:",
                        error
                    );
                });
        },
        fetchSubCategories() {
            if (this.selectedMainCategoryId) {
                axios
                    .get(
                        `/api/categories/${this.selectedMainCategoryId}/subcategories`
                    )
                    .then((response) => {
                        this.subCategories = response.data;
                        this.subSubCategories = []; // Réinitialiser les sous-sous-catégories
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de la récupération des sous-catégories:",
                            error
                        );
                    });
            }
        },
        fetchSubSubCategories() {
            if (this.selectedSubCategoryId) {
                axios
                    .get(
                        `/api/categories/${this.selectedSubCategoryId}/subsubcategories`
                    )
                    .then((response) => {
                        this.subSubCategories = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de la récupération des sous-sous-catégories:",
                            error
                        );
                    });
            }
        },
        toggleDropdown(dropdownType) {
            this.dropdownOpen =
                this.dropdownOpen === dropdownType ? null : dropdownType;
        },
        selectMainCategory(category) {
            this.selectedMainCategoryId = category.id;
            this.selectedMainCategoryName = category.name;
            this.dropdownOpen = null;
            this.fetchSubCategories();
        },
        selectSubCategory(category) {
            this.selectedSubCategoryId = category.id;
            this.selectedSubCategoryName = category.name;
            this.dropdownOpen = null;
            this.fetchSubSubCategories();
        },
        selectSubSubCategory(category) {
            this.selectedSubSubCategoryId = category.id;
            this.selectedSubSubCategoryName = category.name;
            this.dropdownOpen = null;
        },
        assignCategoryToMagasin() {
            const apiUrl = `/api/magasins/${this.selectedMagasin.id}/categories`;
            let categoryIds = [];

            if (this.selectedSubCategoryId === "all") {
                categoryIds = this.subCategories.map((category) => category.id);
            } else if (this.selectedSubSubCategoryId === "all") {
                categoryIds = this.subSubCategories.map(
                    (category) => category.id
                );
            } else {
                categoryIds = [this.selectedSubSubCategoryId];
            }

            axios
                .post(apiUrl, { category_ids: categoryIds })
                .then((response) => {
                    console.log(
                        "Catégories affectées avec succès:",
                        response.data
                    );
                    this.closeAssignCategoryModal();
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de l'affectation de la catégorie:",
                        error.response.data
                    );
                });
        },
        openAssignCategoryModal(magasin) {
            this.selectedMagasin = magasin;
            this.showAssignCategoryModal = true;
        },
        closeAssignCategoryModal() {
            this.showAssignCategoryModal = false;
            this.selectedMainCategoryId = "";
            this.selectedSubCategoryId = "";
            this.selectedSubSubCategoryId = "";
        },
        removeSubCategory(subCategoryId) {
            if (!subCategoryId) {
                console.error("ID de la sous-catégorie non trouvé.");
                return;
            }

            const apiUrl = `/api/magasins/${this.selectedMagasin.id}/categories/${subCategoryId}/remove-subcategory`;
            axios
                .delete(apiUrl)
                .then((response) => {
                    console.log(
                        "Sous-catégorie supprimée avec succès:",
                        response.data
                    );
                    // Mettre à jour la liste des catégories après suppression
                    this.selectedMagasinCategories =
                        this.selectedMagasinCategories.filter(
                            (cat) => cat.subcategory_id !== subCategoryId
                        );
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la suppression de la sous-catégorie:",
                        error
                    );
                    alert(
                        "Erreur lors de la suppression de la sous-catégorie."
                    );
                });
        },
        removeSubSubCategory(subSubCategoryId) {
            if (!subSubCategoryId) {
                console.error("ID de la sous-sous-catégorie non trouvé.");
                return;
            }

            const apiUrl = `/api/magasins/${this.selectedMagasin.id}/categories/${subSubCategoryId}/remove-subsub-category`;
            axios
                .delete(apiUrl)
                .then((response) => {
                    console.log(
                        "Sous-sous-catégorie supprimée avec succès:",
                        response.data
                    );
                    // Mettre à jour la liste des catégories après suppression
                    this.selectedMagasinCategories =
                        this.selectedMagasinCategories.filter(
                            (cat) => cat.category_id !== subSubCategoryId
                        );
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la suppression de la sous-sous-catégorie:",
                        error
                    );
                    alert(
                        "Erreur lors de la suppression de la sous-sous-catégorie."
                    );
                });
        },
        removeMainCategory(mainCategoryId) {
            if (!mainCategoryId) {
                console.error("ID de la catégorie principale non trouvé.");
                return;
            }

            const apiUrl = `/api/magasins/${this.selectedMagasin.id}/categories/${mainCategoryId}/remove-main-category`;
            axios
                .delete(apiUrl)
                .then((response) => {
                    console.log(
                        "Catégorie principale supprimée avec succès:",
                        response.data
                    );
                    // Mettre à jour la liste des catégories après suppression
                    this.selectedMagasinCategories =
                        this.selectedMagasinCategories.filter(
                            (cat) => cat.main_category_id !== mainCategoryId
                        );
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la suppression de la catégorie principale:",
                        error
                    );
                    alert(
                        "Erreur lors de la suppression de la catégorie principale."
                    );
                });
        },
        openShowCategoriesModal(magasin) {
            this.selectedMagasin = magasin;
            axios
                .get(`/api/magasins/${magasin.id}/categories`)
                .then((response) => {
                    this.selectedMagasinCategories = response.data;
                    this.showShowCategoriesModal = true;
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la récupération des catégories affectées:",
                        error
                    );
                });
        },
        closeShowCategoriesModal() {
            this.showShowCategoriesModal = false;
            this.selectedMagasinCategories = [];
        },
    },
};
</script>

<style scoped>
.container {
    max-width: 1200px;
    margin: auto;
}

button {
    transition: background-color 0.3s ease;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter,
.modal-fade-leave-to {
    opacity: 0;
}

.bg-white {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
}

/* Bordure épaisse pour séparer les catégories principales */
.border-t-4 {
    border-top-width: 4px;
}

.border-gray-400 {
    border-color: #cbd5e0;
}

/* Bordure normale pour les sous-catégories */
.border-t {
    border-top-width: 1px;
}

.border-gray-300 {
    border-color: #e2e8f0;
}

/* Bordure légère pour les sous-sous-catégories */
.border-gray-200 {
    border-color: #edf2f7;
}

/* Optionnel: ajuster le style pour le conteneur */
.table-container {
    max-height: 600px; /* Limite la hauteur de la table pour rendre le body défilant */
    overflow-y: auto; /* Ajoute un défilement vertical */
}

.w-80 {
    width: 20rem; /* Fixed width */
}

.h-80 {
    height: 20rem; /* Fixed height */
}

.bouton {
    background-color: #4c7dfb;
}
.icon-small {
    font-size: 12px; /* Ajustez la taille selon vos besoins */
}
.image-upload-container {
    display: flex;
    align-items: center;
    gap: 10px; /* Espace entre l'image et le bouton */
}

.magasin-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.image-upload-btn {
    padding: 5px 10px;
    background-color: #4c7dfb;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.image-upload-btn:hover {
    background-color: #4c7dfb;
}
.modal-large {
    width: 80%;
    height: 80%;
    max-width: 1200px; /* Pour limiter la largeur maximale si nécessaire */
    max-height: 90vh; /* Pour limiter la hauteur maximale à 90% de la vue */
    overflow: auto;
}




.custom-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.custom-table th, .custom-table td {
  padding: 15px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.custom-table thead th {
  background-color: #f4f4f4;
  color: #333;
  font-weight: bold;
  border-top: 2px solid #e2e2e2;
}

.custom-table tr:hover {
  background-color: #f9f9f9;
  transition: background-color 0.2s;
}

.custom-table tbody td {
  font-size: 0.9rem;
  color: #555;
}

.actions-container {
  display: flex;
  justify-content: space-around;
  gap: 10px;
}

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  color: white;
  background-color: #4c7dfb;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #365bb5;
}

.associer-btn {
  background-color: #4c7dfb;
}

.voir-btn {
  background-color: #4c7dfb;
}

.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
}

.pagination-container button {
  padding: 8px 16px;
  border: none;
  background-color: #4c7dfb;
  color: #fff;
  border-radius: 5px;
  cursor: pointer;
}

.pagination-container button:disabled {
  background-color: #ddd;
  cursor: not-allowed;
}

/* Pagination styling */
.pagination-container span {
  font-size: 1.1rem;
  color: #333;
}

/* Image upload styles (inchangées) */
.image-container {
  display: flex;
  align-items: center;
}
.store-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
  cursor: pointer;
}
.image-insert-link {
  color: #4c7dfb;
  cursor: pointer;
  text-decoration: underline;
}
/* Style pour la barre de recherche */
.search-container {
  position: relative;
  width: 300px;
}

.search-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 10px 15px 10px 35px; /* Espace pour l'icône */
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  
  transition: border-color 0.3s ease;
}

.search-input:focus {
  border-color: #4c7dfb; /* Change la couleur de la bordure au focus */
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 15px;
  font-size: 16px;
  color: #999;
}
.autocomplete-list {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
}

.autocomplete-item {
  padding: 10px;
  cursor: pointer;
}

.autocomplete-item:hover {
  background-color: #f4f4f4;
}

/* Tableau et boutons inchangés */

</style>
