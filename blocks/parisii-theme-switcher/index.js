import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

registerBlockType('parisii-optique/theme-switcher', {
    title: __('Sélecteur de Thème', 'parisii-optique'),
    description: __('Bouton de changement de thème clair/sombre', 'parisii-optique'),
    icon: 'admin-appearance',
    category: 'parisii-components',
    keywords: [__('theme', 'parisii-optique'), __('dark', 'parisii-optique'), __('light', 'parisii-optique')],
    supports: {
        align: ['left', 'center', 'right'],
    },
    attributes: {
        size: {
            type: 'string',
            default: 'medium',
        },
        style: {
            type: 'string',
            default: 'button',
        },
        showLabel: {
            type: 'boolean',
            default: true,
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { size, style, showLabel } = attributes;
        const blockProps = useBlockProps({
            className: 'parisii-theme-switcher-block',
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
                case 'minimal':
                    return 'bg-transparent border border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800';
                case 'outline':
                    return 'bg-transparent border-2 border-main-500 text-main-500 hover:bg-main-500 hover:text-white';
                default:
                    return 'bg-main-500 text-white hover:bg-main-600';
            }
        };

        return (
            <div {...blockProps}>
                <div className="theme-switcher-settings" style={{ marginBottom: '20px', padding: '15px', background: '#f0f0f0', borderRadius: '5px' }}>
                    <h4 style={{ margin: '0 0 10px 0' }}>Paramètres du Sélecteur</h4>
                    <div style={{ display: 'flex', gap: '15px', flexWrap: 'wrap' }}>
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Taille</label>
                            <select
                                value={size}
                                onChange={(e) => setAttributes({ size: e.target.value })}
                                style={{ padding: '5px' }}
                            >
                                <option value="small">Petit</option>
                                <option value="medium">Moyen</option>
                                <option value="large">Grand</option>
                            </select>
                        </div>
                        <div>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Style</label>
                            <select
                                value={style}
                                onChange={(e) => setAttributes({ style: e.target.value })}
                                style={{ padding: '5px' }}
                            >
                                <option value="button">Bouton</option>
                                <option value="outline">Contour</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '5px', marginTop: '20px' }}>
                            <input
                                type="checkbox"
                                checked={showLabel}
                                onChange={(e) => setAttributes({ showLabel: e.target.checked })}
                            />
                            <label>Afficher le label</label>
                        </div>
                    </div>
                </div>
                
                <div className="theme-switcher-preview" style={{ 
                    border: '2px dashed #ccc', 
                    padding: '20px', 
                    borderRadius: '8px',
                    textAlign: 'center'
                }}>
                    <h4 style={{ margin: '0 0 15px 0' }}>Aperçu du Sélecteur</h4>
                    <button
                        className={`theme-switcher-btn inline-flex items-center gap-2 rounded-lg font-medium transition-colors ${getSizeClass(size)} ${getStyleClass(style)}`}
                        style={{ cursor: 'not-allowed' }}
                        disabled
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                        </svg>
                        {showLabel && 'Thème'}
                    </button>
                    <p style={{ margin: '10px 0 0 0', fontSize: '12px', color: '#666' }}>
                        Cliquez pour changer de thème
                    </p>
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        const { size, style, showLabel } = attributes;
        const blockProps = useBlockProps.save({
            className: 'parisii-theme-switcher',
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
                case 'minimal':
                    return 'bg-transparent border border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800';
                case 'outline':
                    return 'bg-transparent border-2 border-main-500 text-main-500 hover:bg-main-500 hover:text-white';
                default:
                    return 'bg-main-500 text-white hover:bg-main-600';
            }
        };

        return (
            <div {...blockProps}>
                <button
                    className={`theme-switcher-btn inline-flex items-center gap-2 rounded-lg font-medium transition-colors ${getSizeClass(size)} ${getStyleClass(style)}`}
                    data-theme-switcher
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                    </svg>
                    {showLabel && 'Thème'}
                </button>
            </div>
        );
    },
});
