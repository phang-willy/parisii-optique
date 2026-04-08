<?php
/**
 * The template for displaying all pages
 *
 * @package Parisii_Optique
 */
?>

<?php get_header(); ?>

<?php if ( is_front_page() ) : ?>
  <?php the_content(); ?>
<?php else : ?>
  <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <?php
    /**
     * Breadcrumb
     */
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 mb-6" aria-label="breadcrumb">', '</nav>');
    } else {
        // Breadcrumb avec hiérarchie des pages
        echo '<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 flex flex-wrap gap-1" aria-label="breadcrumb">';
        echo '<a href="' . esc_url(home_url('/')) . '">' . __('Accueil', 'parisii-optique') . '</a>';
        
        // Récupérer les pages parentes
        $parents = array();
        $parent_id = wp_get_post_parent_id(get_the_ID());
        
        while ($parent_id) {
            $parents[] = $parent_id;
            $parent_id = wp_get_post_parent_id($parent_id);
        }
        
        // Inverser pour afficher du parent le plus haut au plus bas
        $parents = array_reverse($parents);
        
        // Afficher les parents
        foreach ($parents as $parent) {
            ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right w-4 text-gray-400">
                  <path d="m9 18 6-6-6-6"/>
              </svg>
            <?php
            echo '<a href="' . esc_url(get_permalink($parent)) . '">' . get_the_title($parent) . '</a>';
        }
        
        // Page actuelle
        ?>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right w-4 text-gray-400">
            <path d="m9 18 6-6-6-6"/>
          </svg>
        <?php
        echo '<span class="text-gray-900 dark:text-white">' . get_the_title() . '</span>';
        echo '</nav>';
    }
    ?>
  </div>
  <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white">
      <?php the_title(); ?>
    </h1>
  </div>

  <div class="prose prose-lg dark:prose-invert max-w-none">
    <?php the_content(); ?>
  </div>
<?php endif ; ?>

<?php get_footer(); ?>