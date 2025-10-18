import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

registerBlockType('parisii-optique/duplicate', {
    title: __('Bouton Dupliquer', 'parisii-optique'),
    description: __('Bouton de duplication pour les administrateurs', 'parisii-optique'),
    icon: 'admin-page',
    category: 'parisii-components',
    keywords: [__('duplicate', 'parisii-optique'), __('copy', 'parisii-optique'), __('admin', 'parisii-optique')],
    supports: {
        align: ['left', 'center', 'right'],
    },
    attributes: {
        itemId: {
            type: 'string',
            default: '',
        },
        itemType: {
            type: 'string',
            default: 'post',
        },
        buttonText: {
            type: 'string',
            default: 'Dupliquer',
        },
        buttonStyle: {
            type: 'string',
            default: 'outline',
        },
        size: {
            type: 'string',
            default: 'medium',
        },
        confirmMessage: {
            type: 'string',
            default: 'Êtes-vous sûr de vouloir dupliquer cet élément ?',
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { itemId, itemType, buttonText, buttonStyle, size, confirmMessage } = attributes;
        const blockProps = useBlockProps({
            className: 'parisii-duplicate-block',
        });

        const getSizeClass = (s) => {
            switch (s) {
                case 'small':
                    return 'text-sm px-3 py-1';
                case 'large':
                    return 'text-lg px-6 py-3';
                default:
                    return 'text-base px-4 py-2';
            }
        };

        const getStyleClass = (s) => {
            switch (s) {
                case 'primary':
                    return 'bg-main-500 text-white hover:bg-main-600';
                case 'secondary':
                    return 'bg-secondary-500 text-white hover:bg-secondary-600';
                case 'minimal':
                    return 'bg-transparent border border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800';
                default:
                    return 'bg-transparent border-2 border-main-500 text-main-500 hover:bg-main-500 hover:text-white';
            }
        };

        return (
            <div {...blockProps}>
                <div className="duplicate-settings" style={{ marginBottom: '20px', padding: '15px', background: '#f0f0f0', borderRadius: '5px' }}>
                    <h4 style={{ margin: '0 0 10px 0' }}>Paramètres du Bouton</h4>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '15px' }}>
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>ID de l'élément</label>
                            <input
                                type="text"
                                value={itemId}
                                onChange={(e) => setAttributes({ itemId: e.target.value })}
                                placeholder="Ex: 123"
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Type d'élément</label>
                            <select
                                value={itemType}
                                onChange={(e) => setAttributes({ itemType: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="post">Article</option>
                                <option value="page">Page</option>
                                <option value="product">Produit</option>
                                <option value="category">Catégorie</option>
                                <option value="tag">Tag</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Texte du bouton</label>
                            <input
                                type="text"
                                value={buttonText}
                                onChange={(e) => setAttributes({ buttonText: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Style du bouton</label>
                            <select
                                value={buttonStyle}
                                onChange={(e) => setAttributes({ buttonStyle: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="outline">Contour</option>
                                <option value="primary">Principal</option>
                                <option value="secondary">Secondaire</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Taille</label>
                            <select
                                value={size}
                                onChange={(e) => setAttributes({ size: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            >
                                <option value="small">Petit</option>
                                <option value="medium">Moyen</option>
                                <option value="large">Grand</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style={{ marginTop: '15px' }}>
                        <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Message de confirmation</label>
                        <input
                            type="text"
                            value={confirmMessage}
                            onChange={(e) => setAttributes({ confirmMessage: e.target.value })}
                            style={{ width: '100%', padding: '5px' }}
                        />
                    </div>
                </div>
                
                <div className="duplicate-preview" style={{ 
                    border: '2px dashed #ccc', 
                    padding: '20px', 
                    borderRadius: '8px',
                    textAlign: 'center'
                }}>
                    <h4 style={{ margin: '0 0 15px 0' }}>Aperçu du Bouton</h4>
                    <button
                        className={`duplicate-btn inline-flex items-center gap-2 rounded-lg font-medium transition-colors ${getSizeClass(size)} ${getStyleClass(buttonStyle)}`}
                        style={{ cursor: 'not-allowed' }}
                        disabled
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 013.75 3.75v1.5c0 1.036.84 1.875 1.875 1.875h1.5c1.036 0 1.875.84 1.875 1.875v9.75c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 18.75v-9.75c0-1.036.84-1.875 1.875-1.875h1.5c1.036 0 1.875-.84 1.875-1.875v-1.5a3.75 3.75 0 013.75-3.75h.375zm0 2.25a.375.375 0 00-.375-.375h-.375a.375.375 0 00-.375.375v1.5c0 .621-.504 1.125-1.125 1.125h-1.5a.375.375 0 00-.375.375v9.75c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125v-9.75a.375.375 0 00-.375-.375h-1.5a.375.375 0 00-.375-.375v-1.5a.375.375 0 00-.375-.375h-.375z" />
                        </svg>
                        {buttonText}
                    </button>
                    <p style={{ margin: '10px 0 0 0', fontSize: '12px', color: '#666' }}>
                        ID: {itemId || 'Non défini'} | Type: {itemType}
                    </p>
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        const { itemId, itemType, buttonText, buttonStyle, size, confirmMessage } = attributes;
        const blockProps = useBlockProps.save({
            className: 'parisii-duplicate',
        });

        const getSizeClass = (s) => {
            switch (s) {
                case 'small':
                    return 'text-sm px-3 py-1';
                case 'large':
                    return 'text-lg px-6 py-3';
                default:
                    return 'text-base px-4 py-2';
            }
        };

        const getStyleClass = (s) => {
            switch (s) {
                case 'primary':
                    return 'bg-main-500 text-white hover:bg-main-600';
                case 'secondary':
                    return 'bg-secondary-500 text-white hover:bg-secondary-600';
                case 'minimal':
                    return 'bg-transparent border border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800';
                default:
                    return 'bg-transparent border-2 border-main-500 text-main-500 hover:bg-main-500 hover:text-white';
            }
        };

        return (
            <div {...blockProps}>
                <button
                    className={`duplicate-btn inline-flex items-center gap-2 rounded-lg font-medium transition-colors ${getSizeClass(size)} ${getStyleClass(buttonStyle)}`}
                    data-item-id={itemId}
                    data-item-type={itemType}
                    data-confirm-message={confirmMessage}
                    data-duplicate-btn
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 013.75 3.75v1.5c0 1.036.84 1.875 1.875 1.875h1.5c1.036 0 1.875.84 1.875 1.875v9.75c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 18.75v-9.75c0-1.036.84-1.875 1.875-1.875h1.5c1.036 0 1.875-.84 1.875-1.875v-1.5a3.75 3.75 0 013.75-3.75h.375zm0 2.25a.375.375 0 00-.375-.375h-.375a.375.375 0 00-.375.375v1.5c0 .621-.504 1.125-1.125 1.125h-1.5a.375.375 0 00-.375.375v9.75c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125v-9.75a.375.375 0 00-.375-.375h-1.5a.375.375 0 00-.375-.375v-1.5a.375.375 0 00-.375-.375h-.375z" />
                    </svg>
                    {buttonText}
                </button>
            </div>
        );
    },
});
