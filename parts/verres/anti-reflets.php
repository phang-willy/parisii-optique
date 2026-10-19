<?php 
  $table = [
    "head" => [
      "couche" => "Couche graphene",
      "caractéristiques" => "Caractéristiques",
      "avantages" => "Avantages",
    ],
    "body" => [
      [
        [
          "Superhydrophobe",
          "Répulsion de l'eau",
          "Facilite le nettoyage",
        ],
        [
          "Antistatique",
          "Verre électrostatiquement neutre",
          "Évite l'adhésion de poussière",
        ],
        [
          "Oléophobe",
          "Répulsion des corps gras",
          "Facilite l'entretien",
        ],
        [
          "Antireflet",
          "Optimisation de la transmission de la lumière",
          "Qualité et confort visuel incomparable",
        ],
        [
          "Anti UV face avant et arrière",
          "Réflexion des UV",
          "Minimise l'exposition de l'œil aux rayonnements",
        ],
      ],
    ],
  ];

  $columns = array_values($table["head"]);
  $rows = $table["body"];
  if (isset($rows[0][0]) && is_array($rows[0][0])) {
      $rows = $rows[0];
  }
  $last_row = count($rows) - 1;
?>

<section id="verres-anti-reflets" class="bg-section-1 dark:text-black">
  <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
    <h2 class="text-center mb-8">Verres Anti-Reflets</h2>
    <div class="grid grid-cols-1 gap-8">
      <div class="grid md:grid-cols-2 gap-6">
        <article class="flex flex-col gap-8">
          <h3>ANTI-REFLET :</h3>
          <p>C'est un film multicouches qui permet de diminuer la part de lumière réfléchie et donc d'augmenter la part de la lumière transmise à travers les verres. En pratique, l'anti-reflet permet d'obtenir une vision plus nette et contrastée en éliminant les reflets gênants.</p>
        </article>
        <figure>
          <?php
            parisii_optique_default_image([
              'id' => 'verres-anti-reflets',
            ]);
          ?>
        </figure>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
        <figure class="order-2 md:order-1">
          <?php
            parisii_optique_default_image([
              'id' => 'verres-anti-reflets-graphene',
            ]);
          ?>
        </figure>
        <article class="order-1 md:order-2 flex flex-col gap-8">
          <h3>Anti-reflet GRAPHENE :</h3>
          <p>C'est le nouvel anti-reflet développé par le verrier Novacel. La succession de couches du traitement anti-reflet a été densifiée par bombardement ionique pour donner naissance au Graphene qui devient encore plus résistant de 25% de plus à l'abrasion.</p>
        </article>
      </div>
    </div>
    <div class="mt-8 flex flex-col gap-6">
      <h3 class="text-center">Caractéristique de votre nouveau verre :</h3>
      <div class="overflow-x-auto">
        <table class="w-full table-fixed text-center border border-black">
          <thead>
            <tr class="border-b border-black">
              <?php foreach ($columns as $column) : ?>
                <th scope="col" class="py-3 px-2 font-semibold uppercase hidden md:table-cell bg-section-2"><?php echo esc_html($column); ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row_index => $row) : ?>
              <?php $is_even_row = $row_index % 2 === 1; ?>
              <tr<?php echo $row_index < $last_row ? ' class="border-b border-black"' : ""; ?>>
                <?php foreach ($row as $cell_index => $cell) : ?>
                  <td class="py-2 px-1 md:py-4 md:px-2 block md:table-cell text-left md:text-center<?php echo $is_even_row ? " bg-section-2" : ""; ?>" data-cell="<?php echo esc_attr($columns[$cell_index] ?? ""); ?>"><?php echo esc_html($cell); ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>