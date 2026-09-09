<section id="where-we-are" class="bg-[#c5b68d]">
  <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16 text-black">
    <h2 class="text-center mb-8">Où nous trouver ?</h2>
    <div class="grid md:grid-cols-2 gap-6">
      <article>
        <div id="google-maps-embed-wrapper" class="w-full h-112.5">
          <?php $google_maps_embed = get_theme_mod('google_maps_embed'); ?>
          <?php if ($google_maps_embed) : ?>
            <?php echo $google_maps_embed; ?>
          <?php endif; ?>
        </div>
      </article>
      <div class="flex flex-col gap-16 justify-center">
        <article class="flex flex-col gap-1">
          <h3>Adresse :</h3>
          <p><?php echo get_theme_mod('address_street', '12 Véloroute Sequana'); ?>, <?php echo get_theme_mod('address_city', '95240 Cormeilles-en-Parisis'); ?></p>
        </article>
        <article class="flex flex-col gap-1">
          <h3>Horaires :</h3>
          <ul>
            <li>Du Lundi au vendredi de <?php echo get_theme_mod('hours_weekdays', '9h30 - 19h00'); ?></li>
            <li>Le samedi de <?php echo get_theme_mod('hours_saturday', '10h00 - 19h30'); ?></li>
            <li>Dimanche <?php echo get_theme_mod('hours_sunday', 'Fermé'); ?></li>
          </ul>
        </article>
      </div>
    </div>
  </div>
</section>
