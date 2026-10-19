<section id="services">
    <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 gap-8">
            <h2 class="text-center">Nos services</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <article class="flex flex-col gap-6 items-center bg-main text-white px-4 py-8 text-center">
                    <h3>Bilan Visuel</h3>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-services-bilan-visuel',
                            'alt' => 'Bilan Visuel proposé par Parisii Optique',
                        ]);
                    ?>
                    <p>Vérification de votre vue<br>Avec ou sans rendez-vous</p>
                </article>
                <article class="flex flex-col gap-6 items-center bg-main text-white px-4 py-8 text-center">
                    <h3>Atelier Montage</h3>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-services-atelier-montage',
                            'alt' => 'Atelier Montage proposé par Parisii Optique',
                        ]);
                    ?>
                    <p>Taille et réparation<br>de vos montures</p>
                </article>
                <article class="flex flex-col gap-6 items-center bg-main text-white px-4 py-8 text-center">
                    <h3>Lentilles</h3>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-services-lentilles',
                            'alt' => 'Lentilles proposées par Parisii Optique',
                        ]);
                    ?>
                    <p>Manipulations et entretiens</p>
                </article>
                <article class="flex flex-col gap-6 items-center bg-main text-white px-4 py-8 text-center">
                    <h3>Tiers-Payant</h3>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-services-tiers-payant',
                            'alt' => 'Tiers-Payant proposé par Parisii Optique',
                        ]);
                    ?>
                    <p>Gestion simplifiée avec<br>Votre mutuelle</p>
                </article>
            </div>
        </div>
    </div>
</section>
