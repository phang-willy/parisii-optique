# Parisii Optique - Thème WordPress Personnalisé

Un thème WordPress moderne et responsive pour un magasin d'opticien, développé avec Tailwind CSS et optimisé pour le SEO.

## 🚀 Fonctionnalités

### Design & Interface
- **Design épuré** avec une approche moderne et professionnelle
- **Mode sombre/clair** avec sélecteur de thème
- **Responsive design** optimisé pour tous les appareils
- **Animations fluides** et transitions élégantes
- **Typographie** : Lexend pour les titres, Inter pour le texte

### Couleurs
- **Couleur principale** : #558763 (vert nature)
- **Couleur secondaire** : #5C442F (marron chaleureux)
- **Variantes hover** : #64BE7D et #9F7550

### Composants
- **Boutons** avec styles variés
- **Newsletter** intégrée
- **Scroll to top** automatique

### WooCommerce
- **Intégration complète** avec WooCommerce
- **Design personnalisé** pour les produits
- **Suppression des CTA** d'achat (selon spécifications)
- **Templates optimisés** pour l'affichage des produits

### Gestion des Popups
- **Plugin intégré** pour la gestion des popups
- **Déclencheurs multiples** : chargement, scroll, clic, sortie
- **Ciblage avancé** : pages, catégories, produits, dates
- **Types de popups** : modal, bannière, slide-in
- **Gestion des cookies** pour "afficher une seule fois"

## 📁 Structure du Thème

```
parisii-optique/
├── components/           # Composants réutilisables
│   ├── carousel.php
│   ├── accordion.php
│   └── sections.php
├── dist/                # CSS compilé
│   └── style.css
├── inc/                 # Fonctions du thème
│   ├── theme-functions.php
│   ├── woocommerce-functions.php
│   └── theme-switcher.php
├── js/                  # JavaScript
│   ├── theme.js
│   ├── carousel.js
│   └── accordion.js
├── popup-manager/       # Plugin de gestion des popups
│   ├── popup-manager.php
│   ├── css/
│   └── js/
├── src/                 # Sources CSS
│   └── input.css
├── templates-parts/     # Parties de templates
│   ├── header/
│   └── footer/
├── woocommerce/         # Templates WooCommerce
├── functions.php        # Fonctions principales
├── style.css           # Fichier de style principal
└── tailwind.config.ts  # Configuration Tailwind v4
```

## 🛠️ Installation

1. **Télécharger le thème** dans `/wp-content/themes/parisii-optique/`
2. **Installer les dépendances** :
   ```bash
   npm install
   ```
3. **Compiler le CSS** (Tailwind CSS v4) :
   ```bash
   npm run build:prod
   ```
4. **Activer le thème** dans l'admin WordPress
5. **Configurer** les options dans Apparence > Personnaliser

### 🔧 Développement

```bash
# Mode développement (avec watch)
npm run dev

# Production (minifié)
npm run build:prod

# Watch mode
npm run watch
```

### 🎨 Tailwind CSS v4

Le thème utilise **Tailwind CSS v4** avec la nouvelle syntaxe `@theme` :

```css
@theme {
  --color-main-500: #558763;
  --color-secondary-800: #5C442F;
  --font-family-heading: 'Lexend', sans-serif;
  --font-family-body: 'Inter', sans-serif;
}
```

## ⚙️ Configuration

### Personnaliseur WordPress
- **Section Héro** : Titre, sous-titre, image
- **Footer** : Description, contact, horaires, adresse
- **Thème** : Mode par défaut (clair/sombre/système)

### Gestion des Popups
1. Aller dans **Popups** dans l'admin WordPress
2. **Créer un nouveau popup**
3. **Configurer** les paramètres d'affichage
4. **Publier** le popup

### WooCommerce
- Le thème est compatible avec WooCommerce
- Les templates sont automatiquement appliqués
- Personnalisation possible via le personnaliseur

## 🔧 Développement

### Compilation CSS
```bash
# Mode développement (avec watch)
npm run dev

# Production (minifié)
npm run build:prod
```

### Structure des Templates
- `index.php` : Page d'accueil
- `page.php` : Pages statiques
- `single.php` : Articles de blog
- `archive.php` : Archives et catégories
- `woocommerce/` : Templates WooCommerce

## 📱 Responsive Design

Le thème est entièrement responsive avec des breakpoints optimisés :
- **Mobile** : < 640px
- **Tablet** : 640px - 1024px
- **Desktop** : > 1024px

## ♿ Accessibilité

- **Navigation au clavier** complète
- **Contraste** respecté (WCAG 2.1)
- **Focus visible** sur tous les éléments interactifs
- **Alt text** pour toutes les images
- **Structure sémantique** HTML5

## 🚀 Performance

- **CSS minifié** et optimisé
- **JavaScript modulaire** et léger
- **Images responsives** avec lazy loading
- **Cache-friendly** structure

## 📞 Support

Pour toute question ou support technique, contactez l'équipe de développement.

## 📄 Licence

Ce thème est développé spécifiquement pour Parisii Optique. Tous droits réservés.

---

**Version** : 1.0.0  
**Dernière mise à jour** : Septembre 2025
**WordPress** : 6.8+
**PHP** : 8.3+