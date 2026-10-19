<section id="hello">
    <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-6 justify-center">
                <h1><?php echo esc_html(parisii_optique_front_page_seo()['h1']); ?></h1>
                <div class="flex flex-col gap-4">
                    <p>Bienvenue chez <?php echo esc_html(get_bloginfo('name')); ?>, votre opticien de référence situé au cœur du nouveau quartier Seine Parisii à Cormeilles-en-Parisis.</p>
                    <p>Notre magasin est l'endroit idéal pour trouver un spécialiste en santé visuelle et choisir la paire de lunettes parfaite.</p>
                </div>
                <div class="flex md:flex-row flex-col gap-4">
                    <a href="<?php echo esc_url(parisii_optique_get_page_url('nous-contacter', '/nous-contacter/')); ?>" class="btn btn-primary">Prendre Rendez-vous</a>
                    <a href="<?php echo esc_url(parisii_optique_get_page_url('marques', '/marques/')); ?>" class="btn btn-secondary">Retrouvez vos marques préférées</a>
                </div>
            </div>
            <div>
               <?php
               parisii_optique_default_image([
                   'id' => 'home-hero',
                   'alt' => 'Opticien Parisii Optique à Cormeilles-en-Parisis',
                   'loading' => 'eager',
                   'fetchpriority' => 'high',
               ]);
               ?>
            </div>
        </div>
    </div>
</section>
