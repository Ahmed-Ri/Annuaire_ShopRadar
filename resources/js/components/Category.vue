<template>
    <div class="container mx-auto mb-5" style="padding-left: 47px; padding-right: 47px;">
        <div class="overflow-x-auto shadow-lg sm:rounded-lg custom-table-container">
            <table class="table-categories">
                <thead>
                    <tr>
                        <th>CATEGORIES</th>
                        <th>PHOTO</th>
                        <th>SOUS-CATEGORIES</th>
                        <th>PHOTO</th>
                        <th>SOUS-SOUS-CATEGORIES</th>
                        <th>PHOTO</th>
                    </tr>
                </thead>
            </table>
            <!-- Conteneur pour le body avec scroll -->
            <div class="scroll-container">
                <table class="table-categories">
                    <tbody>
                        <tr
                            v-for="(row, rowIndex) in getFlattenedCategories()"
                            :key="rowIndex"
                            :style="{ backgroundColor: getCategoryColor(row.category.id, 'category') }"
                        >
                            <!-- Main Category Section -->
                            <td>{{ row.category.name }}</td>
                            <!-- Main Category Image Section -->
                            <td>
                                <div v-if="isFirstOccurrence(rowIndex, 'category')" class="image-button-container">
                                    <!-- Rendre l'image cliquable ou "Inserer" -->
                                    <div v-if="row.category.image">
                                        <img
                                            :src="row.category.image"
                                            alt="Image de la catégorie"
                                            class="category-image mr-2"
                                            style="height: 40px; width: 40px"
                                            @click="triggerFileInput(row.category.id, 'main')"
                                        />
                                    </div>
                                    <a v-else href="#" @click.prevent="triggerFileInput(row.category.id, 'main')" class="image-upload-link">
                                        Inserer
                                    </a>
                                    <input
                                        type="file"
                                        :id="'file-input-main-' + row.category.id"
                                        style="display: none"
                                        @change="handleFileUpload($event, row.category.id, 'main')"
                                    />
                                </div>
                            </td>

                            <!-- Sub-category -->
                            <td :style="{ backgroundColor: getCategoryColor(row.subCategory.id, 'subCategory') }">
                                {{ row.subCategory.name }}
                            </td>
                            <!-- Sub-category Image Section -->
                            <td>
                                <div v-if="isFirstOccurrence(rowIndex, 'subCategory')" class="image-button-container">
                                    <!-- Rendre l'image cliquable ou "Inserer" -->
                                    <div v-if="row.subCategory.image">
                                        <img
                                            :src="row.subCategory.image"
                                            alt="Image de la sous-catégorie"
                                            class="category-image mr-2"
                                            style="height: 40px; width: 40px"
                                            @click="triggerFileInput(row.subCategory.id, 'sub')"
                                        />
                                    </div>
                                    <a v-else href="#" @click.prevent="triggerFileInput(row.subCategory.id, 'sub')" class="image-upload-link">
                                        Inserer
                                    </a>
                                    <input
                                        type="file"
                                        :id="'file-input-sub-' + row.subCategory.id"
                                        style="display: none"
                                        @change="handleFileUpload($event, row.subCategory.id, 'sub')"
                                    />
                                </div>
                            </td>

                            <!-- Sub-sub-category -->
                            <td>{{ row.subSubCategory.name }}</td>

                            <!-- Sub-sub-category Image Section -->
                            <td>
                                <div class="image-button-container">
                                    <!-- Rendre l'image cliquable ou "Inserer" -->
                                    <div v-if="row.subSubCategory.image">
                                        <img
                                            :src="row.subSubCategory.image"
                                            alt="Image de la sous-sous-catégorie"
                                            class="category-image mr-2"
                                            style="height: 40px; width: 40px"
                                            @click="triggerFileInput(row.subSubCategory.id, 'sub-sub')"
                                        />
                                    </div>
                                    <a v-else href="#" @click.prevent="triggerFileInput(row.subSubCategory.id, 'sub-sub')" class="image-upload-link">
                                        Inserer
                                    </a>
                                    <input
                                        type="file"
                                        :id="'file-input-' + row.subSubCategory.id"
                                        style="display: none"
                                        @change="handleFileUpload($event, row.subSubCategory.id, 'sub-sub')"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            categories: [],
            predefinedColors: ['#FFCDD2', '#E1BEE7', '#BBDEFB', '#C8E6C9', '#FFF9C4', '#FFCC80'], // Palette de couleurs prédéfinie
        };
    },
    mounted() {
        this.fetchCategories();
    },
    methods: {
        fetchCategories() {
            axios
                .get("api/categories")
                .then((response) => {
                    this.categories = response.data;
                })
                .catch((error) => {
                    console.error("Error fetching categories:", error);
                });
        },
        triggerFileInput(categoryId, type = "sub-sub") {
        let inputElementId;
        if (type === "main") {
            inputElementId = "file-input-main-" + categoryId;
        } else if (type === "sub") {
            inputElementId = "file-input-sub-" + categoryId;
        } else {
            inputElementId = "file-input-" + categoryId;
        }
        const inputElement = document.getElementById(inputElementId);
        if (inputElement) {
            inputElement.click(); // Simuler le clic
        } else {
            console.error(`Impossible de trouver l'input pour la catégorie ${categoryId}`);
        }
    },

    handleFileUpload(event, categoryId, type = "sub-sub") {
        const file = event.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append("image", file);
            formData.append("category_id", categoryId);

            // Requête API pour uploader l'image
            axios.post(`/api/categories/${categoryId}/upload-image`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                const newImagePath = response.data.path;
                this.updateCategoryImage(categoryId, newImagePath, type);
            })
            .catch((error) => {
                console.error("Erreur lors du téléchargement de l'image :", error);
            });
        }
    },

    updateCategoryImage(categoryId, newImagePath, type = "sub-sub") {
        this.categories.forEach((category) => {
            if (type === "main" && category.id === categoryId) {
                this.$set(category, "image", newImagePath);
            }
            category.children.forEach((subCategory) => {
                if (type === "sub" && subCategory.id === categoryId) {
                    this.$set(subCategory, "image", newImagePath);
                }
                subCategory.children.forEach((subSubCategory) => {
                    if (type === "sub-sub" && subSubCategory.id === categoryId) {
                        this.$set(subSubCategory, "image", newImagePath);
                    }
                });
            });
        });
    },

        getFlattenedCategories() {
            const rows = [];
            this.categories.forEach((category) => {
                category.children.forEach((subCategory) => {
                    if (subCategory.children.length > 0) {
                        subCategory.children.forEach((subSubCategory) => {
                            rows.push({
                                category: category,
                                subCategory: subCategory,
                                subSubCategory: subSubCategory,
                            });
                        });
                    } else {
                        rows.push({
                            category: category,
                            subCategory: subCategory,
                            subSubCategory: { name: "N/A", image: null }, // Placeholder
                        });
                    }
                });
            });
            return rows;
        },

        isFirstOccurrence(index, type) {
            const rows = this.getFlattenedCategories();

            if (type === "category") {
                return index === 0 || rows[index].category.id !== rows[index - 1].category.id;
            }

            if (type === "subCategory") {
                return index === 0 || rows[index].subCategory.id !== rows[index - 1].subCategory.id;
            }

            return false;
        },

        // Récupérer une couleur en fonction de l'ID
        getCategoryColor(id, type) {
            if (type === 'category') {
                const colorIndex = id % this.predefinedColors.length; // Calcul de l'index
                return this.predefinedColors[colorIndex]; // Retourner une couleur prédéfinie
            }

            if (type === 'subCategory') {
                const colorIndex = id % this.predefinedColors.length; // Même logique pour les sous-catégories
                return this.predefinedColors[(colorIndex + 1) % this.predefinedColors.length]; // Décaler la couleur des sous-catégories
            }

            return '#FFFFFF'; // Retourner blanc par défaut
        },
    },
};
</script>

<style scoped>
.table-categories {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    text-align: left;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table-categories thead th {
    background-color: #4c7dfb; /* Bleu pour l'en-tête */
    color: white;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 2;
}

.table-categories th,
.table-categories td {
    padding: 12px 20px;
}

.table-categories tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.table-categories tbody tr:hover {
    background-color: #f3f4f6;
}

/* Conteneur de défilement */
.scroll-container {
    max-height: 400px;
    overflow-y: auto;
}

/* Bouton d'image */
.image-button-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.magasin-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

/* Lien pour les boutons */
.image-upload-link {
    color: #4c7dfb; /* Bleu pour les liens */
    text-decoration: underline; /* Souligné */
    cursor: pointer;
}

.image-upload-link:hover {
    color: #357ae8; /* Changement de couleur au survol */
}
</style>
