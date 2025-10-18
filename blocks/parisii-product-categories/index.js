import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

registerBlockType('parisii-optique/product-categories', {
    title: __('Catégories Produits', 'parisii-optique'),
    description: __('Affichage des catégories de produits WooCommerce', 'parisii-optique'),
    icon: 'products',
    category: 'parisii-woocommerce',
    keywords: [__('products', 'parisii-optique'), __('categories', 'parisii-optique'), __('woocommerce', 'parisii-optique')],
    supports: {
        align: ['wide', 'full'],
    },
    attributes: {
        style: {
            type: 'string',
            default: 'grid',
        },
        columns: {
            type: 'number',
            default: 3,
        },
        showIcons: {
            type: 'boolean',
            default: true,
        },
        showCounts: {
            type: 'boolean',
            default: true,
        },
        showDescriptions: {
            type: 'boolean',
            default: false,
        },
        hideEmpty: {
            type: 'boolean',
            default: true,
        },
        number: {
            type: 'number',
            default: 10,
        },
        orderby: {
            type: 'string',
            default: 'name',
        },
        order: {
            type: 'string',
            default: 'ASC',
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { 
            style, 
            columns, 
            showIcons, 
            showCounts, 
            showDescriptions, 
            hideEmpty, 
            number, 
            orderby, 
            order 
        } = attributes;
        const blockProps = useBlockProps({
            className: 'parisii-product-categories-block',
        });

        return (
            <div {...blockProps}>
                <div className="categories-settings" style={{ marginBottom: '20px', padding: '15px', background: '#f0f0f0', borderRadius: '5px' }}>
                    <h4 style={{ margin: '0 0 10px 0' }}>Paramètres des Catégories</h4>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '15px' }}>
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Style d'affichage</label>
                            <select
                                value={style}
                                onChange={(e) => setAttributes({ style: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="grid">Grille</option>
                                <option value="list">Liste</option>
                                <option value="dropdown">Menu déroulant</option>
                                <option value="tabs">Onglets</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Nombre de colonnes</label>
                            <input
                                type="number"
                                value={columns}
                                onChange={(e) => setAttributes({ columns: parseInt(e.target.value) })}
                                min="1"
                                max="6"
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Nombre de catégories</label>
                            <input
                                type="number"
                                value={number}
                                onChange={(e) => setAttributes({ number: parseInt(e.target.value) })}
                                min="1"
                                max="50"
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Trier par</label>
                            <select
                                value={orderby}
                                onChange={(e) => setAttributes({ orderby: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="name">Nom</option>
                                <option value="count">Nombre de produits</option>
                                <option value="slug">Slug</option>
                                <option value="term_id">ID</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Ordre</label>
                            <select
                                value={order}
                                onChange={(e) => setAttributes({ order: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="ASC">Croissant</option>
                                <option value="DESC">Décroissant</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style={{ marginTop: '15px', display: 'flex', gap: '15px', flexWrap: 'wrap' }}>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                            <input
                                type="checkbox"
                                checked={showIcons}
                                onChange={(e) => setAttributes({ showIcons: e.target.checked })}
                            />
                            Afficher les icônes
                        </label>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                            <input
                                type="checkbox"
                                checked={showCounts}
                                onChange={(e) => setAttributes({ showCounts: e.target.checked })}
                            />
                            Afficher le nombre de produits
                        </label>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                            <input
                                type="checkbox"
                                checked={showDescriptions}
                                onChange={(e) => setAttributes({ showDescriptions: e.target.checked })}
                            />
                            Afficher les descriptions
                        </label>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                            <input
                                type="checkbox"
                                checked={hideEmpty}
                                onChange={(e) => setAttributes({ hideEmpty: e.target.checked })}
                            />
                            Masquer les catégories vides
                        </label>
                    </div>
                </div>
                
                <div className="categories-preview" style={{ 
                    border: '2px dashed #ccc', 
                    padding: '20px', 
                    borderRadius: '8px',
                    minHeight: '200px'
                }}>
                    <div style={{ textAlign: 'center', color: '#666' }}>
                        <h4 style={{ margin: '0 0 10px 0' }}>Aperçu des Catégories</h4>
                        <p style={{ margin: '0 0 15px 0', fontStyle: 'italic' }}>
                            Style: {style} | Colonnes: {columns} | Nombre: {number}
                        </p>
                        <div style={{ 
                            display: 'grid', 
                            gridTemplateColumns: `repeat(${columns}, 1fr)`, 
                            gap: '15px',
                            maxWidth: '600px',
                            margin: '0 auto'
                        }}>
                            {Array.from({ length: Math.min(number, 6) }, (_, i) => (
                                <div key={i} style={{ 
                                    border: '1px solid #ddd', 
                                    borderRadius: '8px', 
                                    padding: '15px',
                                    textAlign: 'center',
                                    background: '#f9f9f9'
                                }}>
                                    <div style={{ 
                                        width: '40px', 
                                        height: '40px', 
                                        background: '#558763', 
                                        borderRadius: '50%', 
                                        margin: '0 auto 10px auto' 
                                    }}></div>
                                    <h5 style={{ margin: '0 0 5px 0' }}>Catégorie {i + 1}</h5>
                                    {showCounts && (
                                        <p style={{ margin: '0', fontSize: '12px', color: '#666' }}>
                                            {Math.floor(Math.random() * 20)} produits
                                        </p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        const blockProps = useBlockProps.save({
            className: 'parisii-product-categories',
        });

        // Les données seront générées côté serveur
        return (
            <div {...blockProps}>
                <div className="product-categories-container">
                    {/* Le contenu sera généré par le render_callback PHP */}
                </div>
            </div>
        );
    },
});
