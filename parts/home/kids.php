<section id="kids">
    <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 gap-8 text-black dark:text-white">
            <h2 class="text-center">La vision de l'enfant</h2>
            <div class="grid grid-cols-1 gap-8">
                <article class="grid grid-cols-1 sm:grid-cols-2 gap-8 px-4 py-8">
                    <div class="flex flex-col gap-4">
                        <h3 class="text-2xl">Ne perdez pas de temps</h3>
                        <p><strong>Le saviez-vous ?</strong> La vue se stabilise dès l'âge de 7 ans. Détecter un trouble tôt, c'est éviter une baisse de vision irréversible (amblyopie).</p>
                        <p><strong>Les signes :</strong> Il louche, fronce les sourcils ou se frotte souvent les yeux ?</p>
                        <p><strong>Notre solution :</strong> Des montures morphologiques, incassables et parfaitement adaptées au visage des plus petits.</p>
                        <div>
                            <a href="<?= home_url('/vision-de-l-enfant'); ?>" class="btn btn-secondary">Protéger la vue de mon enfant</a>
                        </div>
                    </div>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-kids',
                        ]);
                    ?>
                </article>
            </div>
        </div>
    </div>
</section>
