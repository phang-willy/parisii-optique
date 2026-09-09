<section id="solars" class="bg-[#d9c9ae]">
    <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 gap-8 text-black">
            <h2 class="text-center">Lunettes de soleil</h2>
            <div class="grid grid-cols-1 gap-8">
                <article class="grid grid-cols-1 sm:grid-cols-2 gap-8 px-4 py-8">
                    <div class="flex flex-col gap-4">
                        <h3 class="text-2xl uppercase">Attention danger !</h3>
                        <p>Porter des lunettes de marché, c'est forcer vos yeux à s'ouvrir face au danger. Sans protection réelle, la dilatation de la pupille expose votre cristallin à des lésions irréversibles.</p>
                        <p><strong>Notre engagement :</strong> 100% de nos solaires garantissent une protection UV totale.</p>
                    </div>
                    <?php
                        parisii_optique_default_image([
                            'id' => 'home-solars-danger',
                        ]);
                    ?>
                </article>
                <article class="grid grid-cols-1 sm:grid-cols-2 gap-8 px-4 py-8">
                    <div class="flex flex-col gap-4 order-1 sm:order-2">
                        <h3 class="text-2xl uppercase">Lunettes de soleil de qualité à prix abordable : c'est possible !</h3>
                        <p>Acheter vos solaires chez un opticien, c'est la garantie d'un équipement qui dure et qui protège vraiment. Contrairement aux produits d'origine incertaine, chaque monture Parisii Optique est un gage de sécurité pour votre regard.</p>
                        <div class="flex flex-col">
                            <p><strong>Normes CE :</strong> Une protection 100% UVA et UVB rigoureusement certifiée.</p>
                            <p><strong>Qualité Durable :</strong> Des matériaux résistants, ajustés précisément à votre visage.</p>
                            <p><strong>Tous Budgets :</strong> La haute protection n'est pas un luxe, nous avons des solutions pour tous.</p>
                        </div>
                    </div>
                    <div class="order-2 sm:order-1">
                        <?php
                            parisii_optique_default_image([
                                'id' => 'home-solars-quality',
                            ]);
                        ?>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
