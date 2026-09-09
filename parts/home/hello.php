<section id="hello">
    <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-6 justify-center">
                <h1><?= bloginfo('description'); ?></h1>
                <div class="flex flex-col gap-4">
                    <p>Bienvenue chez <?= bloginfo('name'); ?>, votre opticien de référence situé au cœur du nouveau quartier Seine Parisii à Cormeilles-en-Parisis.</p>
                    <p>Notre magasin est l'endroit idéal pour trouver un spécialiste en santé visuelle et choisir la paire de lunettes parfaite.</p>
                </div>
                <div class="flex md:flex-row flex-col gap-4">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('nous-contacter'))); ?>" class="btn btn-primary">Prendre Rendez-vous</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('collections-et-marques'))); ?>" class="btn btn-secondary">Voir nos collections</a>
                </div>
            </div>
            <div>
               <?php parisii_optique_default_image(['id' => 'home-hero']); ?>
            </div>
        </div>
    </div>
</section>
