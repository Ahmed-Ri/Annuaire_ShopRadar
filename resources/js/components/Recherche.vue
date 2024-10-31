<template>
    <div :class="{ 'app-background': !searchStarted }" class="content">
        <div v-if="!searchStarted" class="header">
            <h1>La marketplace du commerce Lyonnais</h1>
            <p>Cherchez parmi des milliers de magasins</p>
            <!-- Switch Voir Magasins / Voir Boutiques -->
            <div class="toggle-switch">
                <a
                    :class="{ active: viewModes === 'magasins' }"
                    @click="
                        navigate('magasins', 'https://magasins.shopradar.app')
                    "
                    class="toggle-option"
                >
                    Voir Magasins
                </a>
                <a
                    :class="{ active: viewModes === 'boutiques' }"
                    @click="
                        navigate('boutiques', 'https://produits.shopradar.app')
                    "
                    class="toggle-option"
                >
                    Voir Produits
                </a>
            </div>
        </div>
        <div
            style="margin-left: 30px !important; margin-right: 30px !important"
        >
            <div class="flex">
                <!-- NavBar (affichée en mode hamburger sur mobile et côte à côte sur grand écran) -->
                <div
                    :class="{ 'open-nav': navOpen }"
                    class="NavBar-container mx-auto pt-1 flex flex-col items-center"
                >
                    <div
                        class="NavBar flex justify-center space-x-8 w-full max-w-4xl"
                    >
                        <!-- Menu déroulant pour les catégories -->
                        <div
                            class="relative responsive-item categorie-bar"
                            style="font-family: Georgia, serif; width: 27%"
                        >
                            <div class="categorie relative">
                                <button
                                    ref="dropdownButton"
                                    @click="toggleDropdown"
                                    class="border border-gray-300 p-2 text-gray-600 w-full rounded custom-input text-left flex items-center bg-white"
                                >
                                    <span class="mr-5">
                                        <i
                                            class="fas fa-bars icon-small text-gray-400"
                                        ></i>
                                    </span>
                                    {{
                                        selectedDropdownCategoryName ||
                                        "Catégorie"
                                    }}
                                    <span
                                        class="ml-auto transition-transform duration-300"
                                        :class="{
                                            'rotate-180': dropdownOpen,
                                            'rotate-0': !dropdownOpen,
                                        }"
                                    >
                                        <i
                                            class="fas fa-chevron-down icon-small"
                                        ></i>
                                    </span>
                                </button>

                                <!-- Menu déroulant -->
                                <ul
                                    v-if="dropdownOpen"
                                    ref="dropdown"
                                    class="absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-auto"
                                >
                                    <li
                                        v-for="(category, index) in categories"
                                        :key="category.id"
                                        @click="
                                            selectDropdownCategory(category)
                                        "
                                        :class="{
                                            'border-b border-gray-200':
                                                index < categories.length - 1,
                                        }"
                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                                    >
                                        {{ category.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Barre de recherche -->
                        <div
                            class="relative responsive-item recherche-bar ml-2"
                            style="font-family: Georgia, serif; width: 35%"
                        >
                            <div class="recherche-bar relative">
                                <span
                                    class="absolute inset-y-0 left-3 flex items-center text-gray-200"
                                >
                                    <i class="fas fa-search icon-small"></i>
                                </span>

                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    @input="debouncedFilterCategories"
                                    @focus="showDropdown = true"
                                    @blur="hideDropdown"
                                    @keydown.enter.prevent="searchWithKeyword"
                                    id="search"
                                    class="border border-gray-300 p-2 w-full pl-10 pr-10 rounded custom-input"
                                    placeholder="Rechercher dans des magasins à proximité"
                                />
                            </div>

                            <!-- Dropdown des résultats de la recherche -->
                            <ul
                                v-if="
                                    showDropdown &&
                                    filteredCategories.length > 0
                                "
                                class="recherche-bar absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-y-auto"
                                style="max-height: 400px"
                            >
                                <li
                                    v-for="(
                                        category, index
                                    ) in filteredCategories"
                                    :key="category.id"
                                    @mousedown="selectSearchCategory(category)"
                                    :class="{
                                        'border-b border-gray-200':
                                            index <
                                            filteredCategories.length - 1,
                                    }"
                                    class="flex items-center px-4 py-2 cursor-pointer hover:bg-gray-100"
                                >
                                    <!-- Icon at the beginning -->
                                    <span
                                        class="flex-shrink-0 bg-gray-200 rounded-full p-2 mr-3"
                                    >
                                        <i
                                            class="fas fa-search text-gray-500"
                                        ></i>
                                    </span>

                                    <!-- Text with dynamic category hierarchy -->
                                    <span>
                                        <span
                                            v-if="
                                                category.name.split(' dans ')
                                                    .length > 2
                                            "
                                            class="text-orange-600 ml-1 mr-1"
                                        >
                                            {{
                                                category.name.split(" dans ")[2]
                                            }}
                                        </span>
                                        <span
                                            v-if="
                                                category.name.split(' dans ')
                                                    .length > 2
                                            "
                                            class="text-gray-500 ml-1 mr-1"
                                            >dans</span
                                        >
                                        <span
                                            v-if="
                                                category.name.split(' dans ')
                                                    .length > 1
                                            "
                                            class="text-orange-800 font-semibold"
                                        >
                                            {{
                                                category.name.split(" dans ")[1]
                                            }}
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Barre de localisation -->
                        <div
                            class="relative responsive-item localisation-bar mb-1 ml-2"
                            style="font-family: Georgia, serif; width: 25%"
                        >
                            <div class="relative">
                                <i
                                    class="fas fa-map-marker-alt text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                ></i>
                                <input
                                    id="location-input"
                                    ref="locationInput"
                                    type="text"
                                    class="border border-gray-300 p-2 pl-10 w-full rounded custom-input text-gray-600"
                                    placeholder="Localisation"
                                />
                            </div>
                        </div>

                        <!-- Sélection du rayon -->
                        <div
                            class="relative responsive-item rayon-bar ml-2"
                            style="font-family: Georgia, serif; width: 12%"
                        >
                            <div class="relative">
                                <button
                                    ref="radiusButton"
                                    @click="toggleRadiusDropdown"
                                    class="border border-gray-300 p-2 w-full rounded custom-input text-left text-gray-600 flex justify-between items-center"
                                >
                                    <div>
                                        <i
                                            class="fas fa-bullseye text-gray-400 mr-2"
                                        ></i>
                                        {{ selectedRadiusName || "Rayon" }}
                                    </div>
                                    <span
                                        class="transition-transform duration-300"
                                        :class="
                                            radiusDropdownOpen
                                                ? 'rotate-180'
                                                : 'rotate-0'
                                        "
                                    >
                                        <i
                                            class="fas fa-chevron-down icon-small text-gray-600"
                                        ></i>
                                    </span>
                                </button>
                                <!-- Dropdown pour la sélection de rayon -->
                                <ul
                                    v-if="radiusDropdownOpen"
                                    ref="radiusDropdown"
                                    class="absolute left-0 w-full bg-white border border-gray-300 rounded shadow-lg z-10 max-h-60 overflow-auto"
                                >
                                    <li
                                        v-for="(radius, index) in radiusOptions"
                                        :key="index"
                                        @click="selectRadius(radius)"
                                        :class="{
                                            'border-b border-gray-200':
                                                index <
                                                radiusOptions.length - 1,
                                        }"
                                        class="px-4 py-2 text-gray-600 cursor-pointer hover:bg-gray-100 flex items-center"
                                    >
                                        <i
                                            class="fas fa-bullseye text-gray-400 mr-2"
                                        ></i>
                                        {{ radius.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Bouton Hamburger (visible uniquement sur les petits écrans) -->
                    <button @click="toggleNav" class="hamburger-btn">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </div>
            </div>
            <div
                v-if="searchCompleted && magasins.length === 0"
                class="py-2"
                style="text-align: left; padding-left: 20px"
            >
                <p
                    style="
                        color: #4c7dfb;
                        font-family: Georgia, 'Times New Roman', Times, serif;
                    "
                >
                    Aucun magasin trouvé pour cette recherche.
                </p>
            </div>
            <!-- Conteneur pour afficher le résumé de la recherche -->
            <div
                v-if="searchCompleted && magasins.length > 0"
                class="resume w-full max-w-4xl border border-gray-200 rounded-lg shadow p-2 mb-2"
                style="font-family: Georgia, serif"
            >
                <!-- En-tête avec titre et image -->
                <div class="flex items-center justify-between">
                    <!-- Texte à gauche -->
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <i
                                class="fas fa-search text-cyan-500 mr-2"
                                style="color: #4c7dfb"
                            ></i>
                            <p
                                class="text_resume text-lg font-semibold text-gray-900"
                            >
                                {{
                                    extractLastCategoryPart(
                                        selectedDropdownCategoryName ||
                                            searchQuery
                                    )
                                }}
                                <!-- Afficher la catégorie sélectionnée ou le mot-clé -->
                                <span class="text-md text-gray-500">à</span>
                                <span class="ml-2 capitalize">{{
                                    extractCity(selectedLocation)
                                }}</span>
                            </p>
                        </div>

                        <!-- Affichage du nombre de magasins trouvés -->
                        <div class="flex items-center">
                            <i
                                class="fas fa-store text-cyan-500 mr-1"
                                style="color: #4c7dfb"
                            ></i>
                            <p class="text-sm text-gray-600">
                                {{ magasins.length
                                }}<span class="ml-1 font-medium text-gray-800">
                                    Résultats à proximité</span
                                >
                            </p>
                        </div>
                    </div>

                    <!-- Image à droite -->
                    <img
                        v-if="
                            findSelectedSubSubCategory() ||
                            findSubSubCategoryByKeyword() ||
                            findSelectedMainCategory() ||
                            findSelectedMainCategoryByKeyword() ||
                            findSelectedSubCategoryByKeyword()
                        "
                        :src="
                            (
                                findSelectedSubSubCategory() ||
                                findSubSubCategoryByKeyword() ||
                                findSelectedMainCategory() ||
                                findSelectedMainCategoryByKeyword() ||
                                findSelectedSubCategoryByKeyword()
                            ).image
                        "
                        alt="Image de la catégorie"
                        class="imagees ml-2 rounded shadow-lg"
                    />
                </div>
            </div>

            <div
                v-if="searchCompleted && magasins.length > 0"
                class="mb-2"
                style="
                    width: 150px;
                    text-align: left;
                    position: sticky;
                    top: 0;
                    z-index: 30;
                "
            >
                <ToggleSwitch
                    :currentView="viewMode"
                    @switch-view="handleViewSwitch"
                />
            </div>

            <!-- Conteneur pour les cartes des magasins et la carte Google Maps -->
            <div
                class="flex-grow sm:flex justify-between w-full h-full max-w-4xl"
            >
                <div v-if="isLoading" class="loader-container">
                    <img
                        src="/public/images/radar.gif"
                        alt="Loading..."
                        class="radar-gif"
                    />
                </div>
                <!-- Affichage de la carte Google Maps uniquement si la vue "graph" est active -->
                <div
                    v-if="viewMode === 'graph'"
                    class="h-full order-2 sm:order-1"
                >
                    <div id="map" class="map-container w-full h-full"></div>
                </div>

                <div
                    class="overflow-y-auto w-full max-w-4xl mb-4 order-1 sm:order-2"
                    :class="[
                        'store-cards-container',
                        viewMode === 'list' ? 'w-full' : 'w-demi',
                    ]"
                >
                    <div class="store-cards-container relative z-20">
                        <div
                            v-if="paginatedMagasins.length > 0"
                            class="grid grid-cols-1 gap-4"
                        >
                            <div
                                v-for="magasin in paginatedMagasins"
                                :key="magasin.id"
                                class="rounded-lg custom-shadow p-2 justify-between items-start"
                            >
                                <div
                                    class="flex justify-between"
                                    style="height: 85px"
                                >
                                    <!-- Détails du magasin -->
                                    <div class="w-3/5 pr-4">
                                        <h3 class="text_nom font-semibold mb-1">
                                            {{ magasin.nom }}
                                        </h3>
                                        <p
                                            class="text_adresse text-xs text-gray-600 flex items-center"
                                        >
                                            <i
                                                class="fas fa-map-marker text-gray-400 mr-2"
                                                style="color: #4c7dfb"
                                            ></i>
                                            {{ magasin.adresse }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i
                                                class="fas fa-road mr-1"
                                                style="color: #4c7dfb"
                                            ></i>
                                            {{ magasin.distance.toFixed(2) }} km
                                        </p>
                                    </div>
                                    <div
                                        class="image-container w-2/5 flex justify-end"
                                    >
                                        <img
                                            v-if="magasin.image"
                                            :src="magasin.image"
                                            alt="Image du magasin"
                                            class="rounded-lg magasin-image"
                                        />
                                        <div
                                            v-else
                                            class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg"
                                        >
                                            <span class="text-gray-500"
                                                >Pas d'image</span
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="button-container flex space-x-4 mt-2"
                                >
                                    <button
                                        @click="
                                            magasin.showPhone =
                                                !magasin.showPhone
                                        "
                                        class="flex items-center px-4 py-2 bg-cyan-400 rounded-full text-xs shadow"
                                    >
                                        <i
                                            class="fas fa-phone-alt mr-2"
                                            style="color: #4c7dfb"
                                        ></i>
                                        {{
                                            magasin.showPhone
                                                ? magasin.tel
                                                : "Afficher le n°"
                                        }}
                                    </button>
                                    <a
                                        :href="`http://maps.google.com/?q=${magasin.adresse}`"
                                        target="_blank"
                                        class="flex items-center px-4 py-2 border border-cyan-400 text-cyan-400 rounded-full text-xs"
                                    >
                                        <i
                                            class="fas fa-location-arrow mr-2"
                                            style="color: #4c7dfb"
                                        ></i>
                                        Y aller
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Buttons -->
                        <div
                            v-if="totalPages > 1"
                            class="flex justify-center mt-4 space-x-4"
                        >
                            <!-- Bouton Précédent -->
                            <button
                                @click="previousPage"
                                :disabled="currentPage === 1"
                                class="p-2 bg-gray-300 text-black rounded-full disabled:opacity-50"
                            >
                                <!-- Icône de flèche gauche -->
                                <i class="fas fa-chevron-left fa-lg"></i>
                            </button>

                            <!-- Bouton Suivant -->
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="p-2 bg-gray-300 text-black rounded-full disabled:opacity-50"
                            >
                                <!-- Icône de flèche droite -->
                                <i class="fas fa-chevron-right fa-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import ToggleSwitch from "./ToggleSwitch.vue";

export default {
    data() {
        return {
            selectedDropdownCategory: null,
            dropdownOpen: false,
            navOpen: false,
            selectedDropdownCategoryName: "",
            currentPage: 1, // Page actuelle
            itemsPerPage: 4,
            searchQuery: "",
            allCategories: [],
            filteredCategories: [],
            selectedSearchCategory: null,
            selectedSearchCategoryName: "",
            selectedSearchCategoryId: "",
            selectedLocation: null,
            magasins: [],
            showDropdown: false,
            searchAttempted: false,
            isLoading: false,
            map: null,
            markers: [],
            userLat: null,
            userLng: null,
            searchCompleted: false,
            searchTriggered: false,
            dropdownSelection: false,
            searchStarted: false,
            viewMode: "graph",
            viewModes: "magasins",

            radiusDropdownOpen: false,
            selectedRadius: 5, // Valeur par défaut pour le rayon sélectionné
            selectedRadiusName: "5 km", // Nom affiché pour le rayon sélectionné
            showPhone: false,
            radiusOptions: [
                { name: "4 km", value: 4 },
                { name: "10 km", value: 10 },
                { name: "15 km", value: 15 },
                { name: "20 km", value: 20 },
                { name: "25 km", value: 25 },
            ],
        };
    },
    components: {
        ToggleSwitch, // Enregistrement du SwitchButton
    },
    computed: {
        paginatedMagasins() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.magasins.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.magasins.length / this.itemsPerPage);
        },
    },
    mounted() {
        this.fetchAllCategories();
        this.initAutocomplete(); // Initialise Google Places Autocomplete
        this.getUserLocation(); // Récupère la localisation de l'utilisateur
        // Ajouter l'écouteur pour détecter les clics externes
        document.addEventListener("click", this.handleClickOutside);
        document.addEventListener(
            "click",
            this.handleClickOutsideRadiusDropdown
        );
    },
    created() {
        this.debouncedFilterCategories = _.debounce(this.filterCategories, 10);
    },
    beforeDestroy() {
        // Supprimer l'écouteur pour éviter les fuites de mémoire
        document.removeEventListener("click", this.handleClickOutside);
    },
    beforeDestroy() {
        document.removeEventListener(
            "click",
            this.handleClickOutsideRadiusDropdown
        );
    },
    methods: {
        delay(ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
        handleViewSwitch(view) {
            this.viewMode = view;

            if (view === "graph") {
                this.$nextTick(() => {
                    this.initMap(); // Réinitialiser la carte lorsque l'utilisateur bascule vers 'Graph'
                });
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        setViewMode(mode) {
            this.viewModes = mode; // Mise à jour de l'état actif du switch
        },
        navigate(mode, url) {
            this.viewModes = mode;
            window.location.href = url;
        },
        toggleNav() {
            this.navOpen = !this.navOpen;
        },
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        toggleRadiusDropdown() {
            this.radiusDropdownOpen = !this.radiusDropdownOpen;
        },
        findSelectedMainCategory() {
            // Assurez-vous que vous avez une catégorie sélectionnée
            const selectedCategory =
                this.selectedSearchCategory || this.selectedDropdownCategory;
            if (!selectedCategory) return null;

            // Parcourez les catégories principales pour trouver celle correspondant à la sélection
            for (let category of this.categories) {
                // Si l'ID de la catégorie principale correspond à celui sélectionné, renvoyez cette catégorie
                if (category.id === selectedCategory.id) {
                    return category;
                }

                // Si la sélection correspond à une sous-catégorie ou sous-sous-catégorie, retournez la catégorie principale correspondante
                for (let child of category.children) {
                    if (child.id === selectedCategory.id) {
                        return category; // Retourner la catégorie principale
                    }

                    for (let subChild of child.children) {
                        if (subChild.id === selectedCategory.id) {
                            return category; // Retourner la catégorie principale
                        }
                    }
                }
            }

            // Retournez null si aucune catégorie principale n'est trouvée
            return null;
        },
        findSelectedMainCategoryByKeyword() {
            if (!this.searchQuery) return null; // Vérifier si la recherche est vide

            // Parcourir les catégories principales
            for (let category of this.categories) {
                // Vérifier si le nom de la catégorie principale correspond au mot-clé
                if (
                    category.name
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase())
                ) {
                    return category; // Retourner la catégorie principale si elle correspond au mot-clé
                }
            }

            return null; // Retourner null si aucune catégorie principale n'est trouvée
        },
        findSelectedSubSubCategory() {
            // Assurez-vous que vous avez une catégorie sélectionnée
            const selectedCategory =
                this.selectedSearchCategory || this.selectedDropdownCategory;
            if (!selectedCategory) return null;

            // Parcourez les catégories, sous-catégories, et sous-sous-catégories pour trouver la sélection
            for (let category of this.categories) {
                for (let child of category.children) {
                    for (let subChild of child.children) {
                        // Si le nom ou l'ID correspond à celui sélectionné, retournez la sous-sous-catégorie
                        if (subChild.id === selectedCategory.id) {
                            return subChild;
                        }
                    }
                }
            }

            // Retournez null si aucune sous-sous-catégorie n'est trouvée
            return null;
        },
        findSubSubCategoryByKeyword() {
            if (!this.searchQuery) return null;

            for (let category of this.categories) {
                for (let child of category.children) {
                    for (let subChild of child.children) {
                        // Si le nom de la sous-sous-catégorie correspond au mot-clé, on la retourne
                        if (
                            subChild.name
                                .toLowerCase()
                                .includes(this.searchQuery.toLowerCase())
                        ) {
                            return subChild;
                        }
                    }
                }
            }
            return null;
        },
        handleClickOutside(event) {
            // Vérifiez si le clic est à l'intérieur du menu déroulant ou du bouton
            const dropdown = this.$refs.dropdown;
            const button = this.$refs.dropdownButton;

            // Si l'élément cliqué n'est pas à l'intérieur du menu ou du bouton, fermer le menu
            if (
                dropdown &&
                button &&
                !dropdown.contains(event.target) &&
                !button.contains(event.target)
            ) {
                this.dropdownOpen = false;
            }
        },
        handleClickOutsideRadiusDropdown(event) {
            // Cibler le bouton et le menu déroulant de rayon
            const radiusButton = this.$refs.radiusButton;
            const radiusDropdown = this.$refs.radiusDropdown;

            // Si le clic ne se fait pas sur le bouton ou le menu, fermer le menu
            if (
                radiusDropdown &&
                radiusButton &&
                !radiusDropdown.contains(event.target) &&
                !radiusButton.contains(event.target)
            ) {
                this.radiusDropdownOpen = false;
            }
        },
        initMap() {
            // Initialiser la carte
            this.map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 48.8566, lng: 2.3522 }, // Position de départ (Paris)
                zoom: 11,
                disableDefaultUI: true,
                mapTypeId: "roadmap",
            });

            // Vérifier si la localisation de l'utilisateur est disponible
            if (this.userLat && this.userLng) {
                // Marquer la position actuelle de l'utilisateur
                const userPosition = { lat: this.userLat, lng: this.userLng };
                const userMarker = new google.maps.Marker({
                    position: userPosition,
                    map: this.map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE, // Utilisation d'une forme de cercle
                        scale: 3, // Taille du cercle
                        fillColor: "#007bff", // Couleur de remplissage (bleu)
                        fillOpacity: 4, // Opacité de remplissage
                        strokeWeight: 3, // Bordure du cercle
                        strokeColor: "#007bff", // Couleur de la bordure (blanc)
                    },
                    title: "Vous êtes ici",
                });

                // Centrer la carte sur la position actuelle
                this.map.setCenter(userPosition);

                // Dessiner un cercle représentant le rayon sélectionné
                const userRadiusCircle = new google.maps.Circle({
                    strokeColor: "#00ffff", // Couleur du contour
                    strokeOpacity: 0.1,
                    strokeWeight: 2,
                    fillColor: "#00ffff", // Couleur de remplissage
                    fillOpacity: 0.2,
                    map: this.map,
                    center: userPosition,
                    radius: this.selectedRadius * 1000, // Rayon en mètres
                });
            }

            // Mettre à jour les marqueurs des magasins

            this.updateMapMarkers();
        },
        clearSearch() {
            // Méthode pour effacer le contenu du champ de recherche
            this.searchQuery = "";
            this.showDropdown = false;
        },
        updateMapMarkers() {
            if (!this.map) {
                this.initMap(); // S'assurer que la carte est initialisée
            }

            // Effacer les anciens marqueurs
            this.markers.forEach((marker) => marker.setMap(null));
            this.markers = [];

            const geocoder = new google.maps.Geocoder();

            // Filtrer les magasins selon le rayon sélectionné après le géocodage
            const magasinsFiltrés = [];

            // Processus asynchrone de géocodage
            const geocodingPromises = this.magasins.map((magasin) => {
                return new Promise((resolve) => {
                    geocoder.geocode(
                        { address: magasin.adresse },
                        (results, status) => {
                            if (status === "OK" && results[0]) {
                                const position = results[0].geometry.location;

                                // Calculer la distance entre la position de l'utilisateur et le magasin
                                const distance = this.calculateDistance(
                                    this.userLat,
                                    this.userLng,
                                    position.lat(),
                                    position.lng()
                                );

                                // Si le magasin est dans le rayon sélectionné, on l'ajoute aux magasins filtrés
                                if (distance <= this.selectedRadius) {
                                    magasin.distance = distance;

                                    // Assignation des coordonnées lat/lng au magasin
                                    magasin.lat = position.lat();
                                    magasin.lng = position.lng();

                                    magasinsFiltrés.push(magasin);

                                    // Créer le marqueur pour chaque magasin
                                    const marker = new google.maps.Marker({
                                        map: this.map,
                                        position: position,
                                        title: magasin.nom,
                                    });

                                    // Ajouter le marqueur à la carte et à la liste des marqueurs
                                    this.markers.push(marker);
                                }

                                resolve();
                            } else {
                                console.error(
                                    `Erreur de géocodage pour ${magasin.adresse}: ${status}`
                                );
                                resolve(); // Résoudre même en cas d'erreur pour continuer le géocodage des autres magasins
                            }
                        }
                    );
                });
            });

            // Une fois que toutes les adresses ont été géocodées et que les marqueurs sont placés
            Promise.all(geocodingPromises).then(() => {
                // Si au moins un magasin est trouvé, centrer la carte sur le premier magasin trouvé
                if (magasinsFiltrés.length > 0) {
                    const firstMagasin = magasinsFiltrés[0];

                    // Vérifier que les coordonnées sont valides avant de centrer la carte
                    if (
                        isFinite(firstMagasin.lat) &&
                        isFinite(firstMagasin.lng)
                    ) {
                        const firstPosition = new google.maps.LatLng(
                            firstMagasin.lat,
                            firstMagasin.lng
                        );
                        this.map.setCenter(firstPosition);
                    } else {
                        console.error("Coordonnées invalides:", firstMagasin);
                    }
                } else {
                    // Si aucun magasin n'est trouvé dans le rayon, centrer la carte sur la position de l'utilisateur
                    if (isFinite(this.userLat) && isFinite(this.userLng)) {
                        this.map.setCenter({
                            lat: this.userLat,
                            lng: this.userLng,
                        });
                    } else {
                        console.error(
                            "Coordonnées utilisateur invalides:",
                            this.userLat,
                            this.userLng
                        );
                    }
                }
            });
        },
        fetchAllCategories() {
            axios
                .get("/api/categories")
                .then((response) => {
                    this.allCategories = this.flattenCategories(response.data);
                    this.categories = response.data;
                })
                .catch((error) => {
                    console.error(
                        "Erreur lors de la récupération des catégories:",
                        error
                    );
                });
        },
        flattenCategories(categories) {
            let flattened = [];
            categories.forEach((category) => {
                if (category.children) {
                    category.children.forEach((subcategory) => {
                        if (subcategory.children) {
                            subcategory.children.forEach((subsubcategory) => {
                                // Ajouter une ligne pour la catégorie, sous-catégorie et sous-sous-catégorie
                                flattened.push({
                                    name: `${category.name} dans ${subcategory.name} dans ${subsubcategory.name}`,
                                    id: subsubcategory.id,
                                });
                            });
                        } else {
                            // Ajouter une ligne pour la catégorie et la sous-catégorie
                            flattened.push({
                                name: `${category.name} dans ${subcategory.name}`,
                                id: subcategory.id,
                            });
                        }
                    });
                } else {
                    // Ajouter une ligne juste pour la catégorie
                    flattened.push({
                        name: `${category.name}`,
                        id: category.id,
                    });
                }
            });
            return flattened;
        },
        extractCity(address) {
            if (!address) return "";

            // Supposons que l'adresse est séparée par des virgules
            const parts = address.split(",");

            // On récupère la partie avant-dernière qui est souvent la ville avec le code postal
            if (parts.length >= 3) {
                let potentialCity = parts[parts.length - 2].trim();

                // Supposons que le code postal est au format "69005 Lyon" ou similaire
                // On retire les chiffres du début pour ne conserver que le nom de la ville
                let city = potentialCity.replace(/^\d{5}\s*/, ""); // Retirer le code postal s'il est présent

                return (
                    city.charAt(0).toUpperCase() + city.slice(1).toLowerCase()
                ); // Mettre la première lettre en majuscule
            }

            // Si l'adresse n'est pas dans un format attendu, renvoyer l'adresse complète
            return address;
        },
        getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.userLat = position.coords.latitude;
                        this.userLng = position.coords.longitude;
                        this.reverseGeocode(this.userLat, this.userLng); // Appelle la méthode de géocodage inversé
                    },
                    (error) => {
                        console.error("Erreur de géolocalisation:", error);
                        alert("Impossible de récupérer votre position.");
                        this.setDefaultLocation(); // Utiliser la localisation par défaut si la géolocalisation échoue
                    }
                );
            } else {
                this.setDefaultLocation();
            }
        },

        // Nouvelle méthode pour convertir les coordonnées en adresse
        reverseGeocode(lat, lng) {
            const geocoder = new google.maps.Geocoder();
            const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        const address = results[0].formatted_address;
                        this.selectedLocation = address; // Mettre à jour la variable qui lie l'input de localisation
                        this.$refs.locationInput.value = address; // Mettre à jour l'input visuellement
                    } else {
                        console.error("Aucune adresse trouvée");
                        this.setDefaultLocation(); // Utiliser la localisation par défaut si aucune adresse n'est trouvée
                    }
                } else {
                    console.error("Erreur lors du géocodage: " + status);
                    this.setDefaultLocation();
                }
            });
        },

        setDefaultLocation() {
            this.selectedLocation = "Lyon, France"; // Localisation par défaut si la géolocalisation échoue
            this.$refs.locationInput.value = "Lyon, France";
            this.fetchMagasins();
        },
        initAutocomplete() {
            const input = this.$refs.locationInput;

            // Définir les limites géographiques pour Lyon et la région Auvergne-Rhône-Alpes
            const lyonBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(44.0, 3.0), // Coordonnées au sud-ouest de la région
                new google.maps.LatLng(46.5, 7.5) // Coordonnées au nord-est de la région
            );

            const autocomplete = new google.maps.places.Autocomplete(input, {
                bounds: lyonBounds, // Limite la recherche à cette zone
                strictBounds: true, // Forcer les résultats à être dans ces limites
                types: ["geocode"], // Limiter la recherche aux villes/adresses géographiques
            });

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    // Mettre à jour la latitude et la longitude de la localisation choisie
                    this.userLat = place.geometry.location.lat();
                    this.userLng = place.geometry.location.lng();
                    this.selectedLocation = place.formatted_address; // Met à jour l'adresse sélectionnée

                    // Si une catégorie est sélectionnée, rechercher par catégorie et localisation
                    if (this.selectedDropdownCategoryId) {
                        this.fetchMagasinsByCategory(
                            this.selectedDropdownCategoryId
                        );
                    } else {
                        // Sinon, rechercher simplement avec la localisation
                        this.fetchMagasins();
                    }
                }
            });
        },
        filterCategories() {
            const query = this.searchQuery.toLowerCase();
            this.filteredCategories = this.allCategories.filter((category) =>
                category.name.toLowerCase().includes(query)
            );
        },
        selectDropdownCategory(category) {
            this.selectedDropdownCategory = category;
            this.selectedDropdownCategoryName = category.name;
            this.selectedDropdownCategoryId = category.id;
            this.dropdownOpen = false;
            this.dropdownSelection = true;
            this.searchTriggered = true;
            // Utiliser la nouvelle méthode pour extraire la dernière partie du nom de la catégorie
            const lastCategoryPart = this.extractLastCategoryPart(
                category.name
            );

            // Mettre à jour le champ de recherche avec la dernière partie du nom de la catégorie
            this.searchQuery = lastCategoryPart;
            // Réinitialiser toutes les valeurs liées à la Recherche par catégorie
            this.selectedSearchCategory = null;
            this.selectedSearchCategoryName = "";
            this.searchQuery = category.name; // Réinitialiser le champ de recherche
            this.selectedSearchCategoryId = "";

            // Appeler fetchMagasinsByCategory pour filtrer par catégorie du menu déroulant
            this.fetchMagasinsByCategory(this.selectedDropdownCategoryId);
        },
        extractLastCategoryPart(categoryName) {
            // Vérifie si la chaîne contient "dans"
            if (categoryName.includes("dans")) {
                const categoryParts = categoryName.split(" dans ");
                // Retourne la dernière partie si elle existe
                return categoryParts[categoryParts.length - 3];
            } else {
                // Retourne simplement le nom si "dans" n'est pas trouvé
                return categoryName;
            }
        },
        findSelectedSubCategoryByKeyword() {
            if (!this.searchQuery) return null;

            // Parcourir les catégories et sous-catégories
            for (let category of this.categories) {
                for (let child of category.children) {
                    // Si le nom de la sous-catégorie contient le mot-clé
                    if (
                        child.name
                            .toLowerCase()
                            .includes(this.searchQuery.toLowerCase())
                    ) {
                        return child; // Retourner la sous-catégorie
                    }
                }
            }

            // Retourner null si aucune sous-catégorie n'est trouvée
            return null;
        },
        selectSearchCategory(category) {
            this.selectedSearchCategory = category;

            // On divise le nom de la catégorie en ses parties
            const categoryParts = category.name.split(" dans ");

            // On inverse l'ordre des parties
            const reversedCategory = categoryParts.reverse().join(" dans ");

            // On met à jour la barre de recherche avec la chaîne inversée
            this.searchQuery = reversedCategory;

            // On conserve aussi le nom original de la catégorie sélectionnée si nécessaire
            this.selectedSearchCategoryName = category.name;
            this.selectedSearchCategoryId = category.id;

            // Fermer le dropdown après la sélection
            this.showDropdown = false;

            // Réinitialiser le menu déroulant pour les catégories
            this.selectedDropdownCategory = null;
            this.selectedDropdownCategoryName = "";
            this.selectedDropdownCategoryId = "";
            this.dropdownSelection = false;

            // Re-fetch magasins with the new search category and existing location
            if (this.selectedLocation) {
                this.fetchMagasins();
            }
        },

        searchWithKeyword() {
            // Fermer le dropdown après la recherche
            this.showDropdown = false;

            if (this.searchQuery.trim() !== "") {
                this.selectedSearchCategory = null; // Réinitialiser la catégorie sélectionnée
                this.selectedDropdownCategoryName = "";
                this.selectedSearchCategoryId = ""; // Pas de catégorie spécifique
                this.searchTriggered = true;
                this.dropdownSelection = false;
                this.searchStarted = true;

                // Lancer la recherche des magasins
                this.fetchMagasins();
            }
        },
        searchOnBlur() {
            // Lancer la recherche lorsque le champ perd le focus
            this.searchWithKeyword();
        },
        hideDropdown() {
            setTimeout(() => {
                this.showDropdown = false;
            }, 200);
        },

        async fetchMagasins() {
            this.isLoading = true; // Activer l'indicateur de chargement
            this.searchCompleted = false; // Réinitialiser avant la recherche

            try {
                const response = await axios.post("/api/magasins", {
                    query: this.searchQuery,
                    category_id: this.selectedSearchCategoryId,
                    location: this.selectedLocation,
                });

                await this.delay(500); // Attendre 500ms si nécessaire

                // Si la réponse contient des magasins
                if (response.data.length > 0) {
                    this.allMagasins = response.data.map((magasin) => ({
                        ...magasin,
                        showPhone: false, // Cacher le numéro de téléphone initialement
                    }));

                    // Géocoder les magasins et filtrer en fonction du rayon
                    await this.geocodeMagasins();

                    this.magasins = this.allMagasins.filter(
                        (magasin) => magasin.distance <= this.selectedRadius
                    );

                    this.initMap();
                    this.updateMapMarkers(); // Mettre à jour les marqueurs sur la carte
                } else {
                    // Si aucun magasin n'est trouvé
                    console.log("Aucun magasin trouvé pour cette recherche.");
                    this.magasins = []; // Réinitialiser la liste à vide
                }

                this.searchCompleted = true; // Indiquer que la recherche est terminée
                this.searchStarted = true;
            } catch (error) {
                if (error.response && error.response.status === 404) {
                    // Gérer les résultats vides sans provoquer une erreur
                    console.log("Aucun magasin trouvé pour cette recherche.");
                    this.magasins = []; // Réinitialiser la liste des magasins à vide
                } else {
                    console.error(
                        "Erreur lors de la récupération des magasins:",
                        error
                    );
                    this.magasins = [];
                }
            } finally {
                this.isLoading = false; // Désactiver l'indicateur de chargement
                this.searchAttempted = true; // Marquer la tentative de recherche
            }
        },
        async fetchMagasinsWithRadius() {
            this.isLoading = true; // Start loading
            this.searchCompleted = false; // Reset search completed state
            this.searchAttempted = true;

            try {
                // If the radius is 0, show all stores
                if (this.selectedRadius === 0) {
                    this.magasins = this.allMagasins;
                } else {
                    // If a category is selected, fetch stores by category
                    if (this.selectedDropdownCategoryId) {
                        await this.fetchMagasinsByCategory(
                            this.selectedDropdownCategoryId
                        );
                    } else {
                        // Otherwise, filter only by radius
                        this.magasins = this.allMagasins.filter((magasin) => {
                            return magasin.distance <= this.selectedRadius;
                        });
                    }

                    // Geocode the magasins and update distances
                    await this.geocodeMagasins();

                    // Filter magasins again after geocoding if needed
                    this.magasins = this.allMagasins.filter(
                        (magasin) => magasin.distance <= this.selectedRadius
                    );

                    // Update the map with new markers
                    this.initMap();
                    this.updateMapMarkers();
                }

                this.searchCompleted = true; // Mark search as completed
            } catch (error) {
                console.error("Error fetching magasins by radius:", error);
                this.magasins = [];
            } finally {
                this.isLoading = false; // Stop loading
            }
        },
        async geocodeMagasins() {
            const geocoder = new google.maps.Geocoder();
            const magasinsProches = [];

            const geocodingPromises = this.allMagasins.map((magasin) => {
                return new Promise((resolve, reject) => {
                    geocoder.geocode(
                        { address: magasin.adresse },
                        (results, status) => {
                            if (status === "OK" && results[0]) {
                                const magasinLat =
                                    results[0].geometry.location.lat();
                                const magasinLng =
                                    results[0].geometry.location.lng();

                                // Calculer la distance par rapport à la localisation sélectionnée (Paris)
                                const distance = this.calculateDistance(
                                    this.userLat, // Latitude de l'utilisateur ou de Paris
                                    this.userLng, // Longitude de l'utilisateur ou de Paris
                                    magasinLat,
                                    magasinLng
                                );

                                magasin.distance = distance;

                                // Ajouter les magasins seulement s'ils sont dans le rayon sélectionné
                                if (distance <= this.selectedRadius) {
                                    magasinsProches.push(magasin);
                                }
                                resolve();
                            } else {
                                console.error(
                                    `Erreur de géocodage pour ${magasin.nom}: ${status}`
                                );
                                resolve();
                            }
                        }
                    );
                });
            });

            await Promise.all(geocodingPromises);

            // Trier les magasins proches par distance
            this.magasins = magasinsProches.sort(
                (a, b) => a.distance - b.distance
            );

            this.updateMapMarkers();
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Rayon de la Terre en km
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) *
                    Math.cos(lat2 * (Math.PI / 180)) *
                    Math.sin(dLon / 2) *
                    Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance en km
        },
        async fetchMagasinsByCategory(categoryId) {
            if (!categoryId) return;

            this.isLoading = true; // Activer l'indicateur de chargement
            this.searchStarted = true;

            this.searchCompleted = false;

            try {
                const response = await axios.get(
                    `/api/categories/${categoryId}/magasins`
                );

                await this.delay(500); // Attendre 1 seconde si nécessaire

                // Stocker les données récupérées sans les afficher directement
                this.allMagasins = response.data;

                // Geocoder les magasins pour calculer la distance avant le filtrage par rayon
                await this.geocodeMagasins();

                // Appliquer le filtrage par rayon avant de les afficher
                this.magasins = this.allMagasins.filter(
                    (magasin) => magasin.distance <= this.selectedRadius
                );

                // Mettre à jour la carte et les marqueurs
                this.initMap();
                this.updateMapMarkers(); // Mettre à jour les marqueurs après le filtrage
                this.searchStarted = true;

                this.searchCompleted = true;
            } catch (error) {
                console.error(
                    "Erreur lors de la récupération des magasins:",
                    error
                );
                this.magasins = [];
            } finally {
                this.isLoading = false; // Désactiver l'indicateur de chargement
                this.searchAttempted = true;
            }
        },

        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },

        toggleRadiusDropdown() {
            this.radiusDropdownOpen = !this.radiusDropdownOpen;
        },
        selectRadius(radius) {
            this.selectedRadius = radius.value;
            this.selectedRadiusName = radius.name;
            this.radiusDropdownOpen = false;
            this.fetchMagasinsWithRadius(); // Appeler la méthode de filtrage des magasins par rayon
        },
    },
};
</script>

<style scoped>
.app-background {
    background-image: url("/images/Lyon-background.png"); /* Chemin vers votre image */
    background-size: cover; /* Assurez-vous que l'image couvre tout l'écran */
    background-position: center center; /* Centre l'image */
    background-repeat: no-repeat; /* Empêche la répétition de l'image */
    min-height: 100vh; /* Prend toute la hauteur de la fenêtre */
    display: flex;
    flex-direction: column;
    background-color: rgba(0, 0, 0, 0.5); /* Couche noire semi-transparente */
}
.resume {
    background-color: rgba(
        255,
        255,
        255,
        0.7
    ); /* Fond blanc avec transparence */
    /* Garder les autres styles que tu as déjà */
}
.container {
    display: flex;
    flex-direction: column;
}

/* Store cards container (scrollable) */
.store-cards-container {
    height: 500px; /* Ajustez la hauteur selon vos besoins */
    overflow-y: scroll; /* Permettre le défilement vertical */
    box-sizing: border-box;

    /* Masquer la barre de défilement dans les navigateurs modernes */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer 10+ */
}

.store-cards-container.w-full {
    width: 100%; /* Occupe toute la largeur pour la vue "Liste" */
}
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

.store-cards-container::-webkit-scrollbar {
    display: none; /* Masquer la barre de défilement pour Chrome, Safari et Opera */
}

.map-container {
    width: 100%;
    height: 100%;
    /* border: 2px solid rgb(208, 254, 223);  */
}

/* Basic styling for the store cards */
.grid {
    display: grid;
    gap: 20px;
}

.grid-cols-1 {
    grid-template-columns: 1fr;
}

.border {
    border: 1px solid #ccc;
}

.bg-white {
    background-color: #fff;
}

.flex {
    display: flex;
}

.items-start {
    align-items: flex-start;
    background-color: rgba(255, 255, 255, 0.7);
}
.custom-input:focus {
    outline: none;
    box-shadow: none;
    border-color: #ccc;
}

.relative .fas.fa-search {
    color: #6b7280;
    font-size: 1rem;
    margin-top: 12px;
    margin-left: 10px;
}

.relative .fas.fa-map-marker-alt {
    margin-top: 12px;
    margin-left: 10px;
}
.custom-shadow {
    box-shadow: 0px -4px 6px rgba(0, 0, 0, 0.1),
        /* Ombre en haut */ 0px 4px 6px rgba(0, 0, 0, 0.1),
        /* Ombre en bas */ 0px 1px 3px rgba(0, 0, 0, 0.08); /* Ombre plus légère en bas */
}
.icon-small {
    font-size: 12px; /* Ajustez la taille selon vos besoins */
}
.rotate-0 {
    transform: rotate(0deg);
}

.rotate-180 {
    transform: rotate(180deg);
}

.transition-transform {
    transition: transform 0.3s ease;
}

.relative .fas.fa-times {
    position: absolute;
    margin-left: 20px;

    transform: translateY(-50%);
}

.relative .fas.fa-times:hover {
    color: #ff0000; /* Change la couleur de l'icône "X" au survol */
}

.relative .fas.fa-times {
    position: absolute;
    left: 385px; /* Ajuste la position de l'icône "X" à droite */
    bottom: 4px;
    transform: translateY(-50%);
}
.loader-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
}

.radar-gif {
    width: 150px;
    height: 150px;
    border-radius: 50%; /* Forme circulaire */
    object-fit: contain; /* S'assure que l'image n'est pas déformée */
    background-color: transparent; /* S'assure que le fond est transparent */
}

.store-cards-container {
    margin-bottom: 0 !important; /* Supprime toute marge sur les cartes */
}
.toggle-switch {
    display: flex;
    border: 1px solid #000;
    border-radius: 30px;
    overflow: hidden;
    width: 300px;
    margin: 20px auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Options du switch */
.toggle-option {
    flex: 1;
    padding: 10px 20px;
    background-color: #fff;
    border: none;
    text-align: center;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-size: 16px;
    text-decoration: none; /* Supprime la ligne sous les liens */
    color: black; /* Couleur par défaut du texte */
}

/* Styles actifs (option sélectionnée) */
.toggle-option.active {
    background-color: #4c7dfb; /* Fond vert clair pour l'option active */
    color: #ffffff; /* Texte en noir */
}
.image-container .magasin-image {
    height: 120px;
    width: 120px;
    object-fit: cover !important;
}
.images {
    height: 120px !important;
    width: 120px !important;
    object-fit: cover !important;
}
.imagees {
    height: 80px !important;
    width: 80px !important;
    object-fit: cover !important;
}
.header {
    display: block;
}
.hamburger-btn {
    position: relative; /* Ajustement selon les besoins */
    /* Ajuste la distance verticale du bouton hamburger */
    right: 0; /* Assure qu'il n'y a pas de décalage horizontal */
    margin: 0; /* Supprime toute marge par défaut */
    padding: 5px; /* Réduit le padding si nécessaire */

    background: transparent; /* Supprime tout fond de bouton */
    border: none; /* Supprime les bordures du bouton */
    cursor: pointer; /* Montre que c'est un bouton cliquable */
    font-size: 25px;
}
.hamburger-icon {
    display: inline-block;
    cursor: pointer;
    width: 30px; /* Largeur de l'icône */
    height: 20px; /* Hauteur totale de l'icône */
    position: relative;
}

.hamburger-icon span {
    background-color: #676767; /* Couleur des barres */
    width: 100%; /* Largeur des barres */
    height: 3px; /* Épaisseur des barres (ajustez ici pour les rendre plus minces) */
    display: block;
    position: absolute;
    left: 0;
    transition: all 0.3s ease; /* Animation pour transition fluide */
}

@media (min-width: 320px) and (max-width: 600px) {
    .hamburger-btn {
        display: block;
    }
    .text_nom {
        font-size: 11px;
    }
    .text_adresse {
        font-size: 10px;
    }
    .text_resume {
        font-size: 16px;
    }
    .categorie-bar,
    .localisation-bar,
    .rayon-bar {
        display: none;
    }
    .app-background {
        background-size: cover;
        background-position: right center;
        background-repeat: no-repeat;
    }

    /* Montrer ces sections lorsque le menu est ouvert */
    .NavBar-container {
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .NavBar-container.open-nav .localisation-bar,
    .NavBar-container.open-nav .rayon-bar {
        display: flex;
        width: 100%;
        flex-direction: column;
        background-color: white;
    }

    /* .NavBar-container.open-nav {
        margin-top: 10px;

        display: flex;
        flex-direction: column;
        background-color: white;
        padding: 10px;
        width: 100%;
    } */

    .NavBar {
        margin-right: 55px;
        margin-bottom: 5px;
        margin-left: 35px;
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .image-container .magasin-image {
        height: 80px !important;
        width: 80px !important;
        object-fit: cover !important;
    }
    .images {
        height: 80px !important;
        width: 80px !important;
        object-fit: cover !important;
    }
    #map {
        height: 300px; /* Ajustez la hauteur selon vos besoins */
        min-width: 300px;
        max-width: 600px;
        width: 100%; /* Permet à l'élément de s'adapter entre 400px et 600px */
    }

    .rayon {
        width: 27% !important;
    }

    .NavBar {
        display: block;
    }

    /* Ajouter éventuellement des marges ou des espacements */

    .categorie {
        margin-bottom: 5px;
        margin-left: 8px;
        width: 100%;
    }
    .recherche-bar {
        margin-bottom: 5px;
        font-size: 15px;
    }
    /* .header {
        display: none;
    } */
    .responsive-item {
        flex: 1 !important;
        width: 100% !important;
    }
}
@media (min-width: 600px) and (max-width: 722px) {
    #map {
        width: 330px;
    }
}
@media (min-width: 722px) and (max-width: 844px) {
    #map {
        width: 410px;
    }
    .hamburger-btn {
        display: none;
    }
}
@media (min-width: 844px) and (max-width: 968px) {
    #map {
        width: 480px;
    }
    .hamburger-btn {
        display: none;
    }
}
@media (min-width: 968px) {
    #map {
        width: 650px;
    }
    .NavBar-container {
        display: flex;
        justify-content: center;
        width: 100%; /* Le conteneur prend toute la largeur de l'écran */
    }

    /* Cacher le bouton hamburger sur les grands écrans */
    .hamburger-btn {
        display: none;
    }

    /* Afficher les éléments côte à côte comme avant */
    .NavBar {
        flex-direction: row;
        justify-content: space-between;
        width: 90%;
    }
}
</style>
