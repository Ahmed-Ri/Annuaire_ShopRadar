<template>
    <footer class="bg-gray-100 py-5 px-5 w-full">
        <div class="footer-container flex justify-between max-w-7xl mx-auto px-4">
            <!-- ShopRadar Section -->
            <div class="footer-section w-1/3 shop-radar">
                <h3 class="font-semibold text-lg">ShopRadar</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="text-gray-600 hover:underline">Nous contacter</a></li>
                    <li><a href="#" class="text-gray-600 hover:underline">Assistance</a></li>
                    <li><a href="#" class="text-gray-600 hover:underline">Vendre sur ShopRadar</a></li>
                </ul>
            </div>

            <!-- Quick Links Section -->
            <div class="footer-section w-1/3 quick-links">
                <h3 class="font-semibold text-lg">Liens rapides</h3>
                <ul class="mt-4 space-y-1">
                    <li><a href="#" class="text-gray-600 hover:underline">Accéder à mon compte</a></li>
                    <li><a href="#" class="text-gray-600 hover:underline">Magasins populaires</a></li>
                </ul>
            </div>

            <!-- Social Media Section -->
            <div class="footer-section w-1/3 social-media">
                <h3 class="font-semibold text-lg">Restons en contact</h3>
                <div class="mt-8 flex space-x-6">
                    <!-- Icônes de réseaux sociaux -->
                    <a href="#"><i class="fab fa-facebook fa-2x mx-2" style="color: #3b82f6 !important"></i></a>
                    <a href="#"><i class="fab fa-linkedin fa-2x mx-2" style="color: #000000 !important"></i></a>
                    <a href="#"><i class="fab fa-instagram fa-2x mx-2" style="color: #60a5fa !important"></i></a>
                    <a href="#"><i class="fab fa-youtube fa-2x mx-2" style="color: #ef4444 !important"></i></a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default{
    
}
</script>
<style>
/* Par défaut, le footer montre toutes les sections */
footer {
    background-color: #f7fafc;
    padding: 2rem 0;
}

footer h3 {
    font-weight: 600;
    font-family: Georgia, serif;
    font-size: 1.125rem;
}

footer ul {
    margin-top: 1rem;
    font-family: Georgia, serif;
}

footer ul li {
    list-style-type: none;
    font-family: Georgia, serif;
}

footer ul li a {
    color: #4a5568;
    text-decoration: none;
    font-family: Georgia, serif;
}

footer ul li a:hover {
    text-decoration: underline;
}

footer .social-icons {
    margin-top: 1rem;
}

footer .social-icons a {
    margin-right: 1rem;
    font-size: 1.5rem;
    color: #4a5568;
}

footer .social-icons a:hover {
    color: #2d3748;
}

/* Responsive CSS - Masquer les autres sections pour les petits écrans */
@media (max-width: 600px) {
    .footer-section.shop-radar, .footer-section.quick-links {
        display: none; /* Masquer les sections ShopRadar et Quick Links */
    }

    .footer-container {
        justify-content: center; /* Centrer la section Social Media */
    }

    .footer-section.social-media {
        width: 100%; /* Faire prendre toute la largeur à la section Social Media */
        text-align: center;
    }
    
    .footer-section.social-media .flex {
        justify-content: center;
    }
}
</style>