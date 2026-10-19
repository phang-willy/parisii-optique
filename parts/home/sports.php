<section id="sports" class="bg-section-1">
  <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16 text-black">
    <h2 class="text-center mb-8">Sportives et sportifs :</h2>
    <article class="grid grid-cols-1 sm:grid-cols-2 gap-8">
      <div class="order-2 sm:order-1">
        <?php
          parisii_optique_default_image([
            'id' => 'lunettes-de-sport',
            'alt' => 'Lunettes de sport chez Parisii Optique',
          ]);
        ?>
      </div>
      <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4">
          <h3>Pratiquez en toutes situations :</h3>
          <div class="flex flex-col gap-2">
            <p>Les amateurs de sport et athlètes ne sont pas en reste ! <?php echo get_bloginfo('name'); ?> met à votre disposition une gamme de lunettes spécialement conçues pour les activités physiques.</p>
            <p>Pour vous permettre d'effectuer votre activité favorite sans faire l'impasse sur votre confort visuel, notre collection sport inclut des modèles robustes et performants, adaptés à diverses disciplines telles que la course, le cyclisme, ou les sports aquatiques.</p>
            <p>Ces lunettes sont conçues pour offrir un confort optimal, une résistance accrue, et une visibilité parfaite, même dans les conditions les plus exigeantes.</p>
          </div>
        </div>
        <div class="flex flex-col gap-4">
          <h3>À trouver en magasin :</h3>
          <div class="flex flex-col gap-2">
            <?php
              $sport_brand_names = array();
              if (
                  class_exists('Parisii_Optique_Brand_Display')
                  && method_exists('Parisii_Optique_Brand_Display', 'get_brand_names')
              ) {
                  $sport_brand_names = Parisii_Optique_Brand_Display::get_brand_names(['sports' => true]);
              }
            ?>
            <?php if (!$sport_brand_names) : ?>
              <p>Marques de sports non disponibles en magasin actuellement.</p>
            <?php else : ?>
              <p>Retrouvez en magasin les lunettes de sport : <?php
                $sport_brand_count = count($sport_brand_names);
                foreach ($sport_brand_names as $sport_brand_index => $sport_brand_name) {
                    if ($sport_brand_index > 0) {
                        echo $sport_brand_index === $sport_brand_count - 1 ? ' et ' : ', ';
                    }
                    echo '<strong>' . esc_html($sport_brand_name) . '</strong>';
                }
              ?></p>
            <?php endif; ?>
            <p>Et dès 2027 : <strong>Oakley</strong> et bien plus encore !</p>
          </div>
        </div>
      </div>
    </article>
  </div>
</section>
