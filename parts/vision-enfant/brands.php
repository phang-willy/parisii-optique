<section class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8" id="marques-pour-enfants">
  <header class="mb-6">
    <h2 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 dark:text-white">Marques pour enfants</h2>
    <p class="text-gray-700 dark:text-gray-300 mt-2">Une sélection de marques pour enfants alliant confort, résistance et style.</p>
  </header>

  <?php if (class_exists('Parisii_Optique_Brand_Display')) : ?>
    <?php Parisii_Optique_Brand_Display::render_brands_grid(['kids' => true]); ?>
  <?php else : ?>
    <div class="card text-center py-8">
      <p class="text-gray-500 dark:text-gray-400">Les marques pour enfants ne sont pas disponibles pour le moment.</p>
    </div>
  <?php endif; ?>
</section>