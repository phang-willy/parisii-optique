import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

registerBlockType('parisii-optique/newsletter-section', {
    title: __('Section Newsletter', 'parisii-optique'),
    description: __('Section d\'inscription à la newsletter', 'parisii-optique'),
    icon: 'email-alt',
    category: 'parisii-sections',
    keywords: [__('newsletter', 'parisii-optique'), __('email', 'parisii-optique'), __('form', 'parisii-optique')],
    supports: {
        align: ['wide', 'full'],
    },
    attributes: {
        title: {
            type: 'string',
            source: 'html',
            selector: '.newsletter-title',
        },
        subtitle: {
            type: 'string',
            source: 'html',
            selector: '.newsletter-subtitle',
        },
        placeholder: {
            type: 'string',
            default: 'Votre adresse email',
        },
        buttonText: {
            type: 'string',
            default: 'S\'inscrire',
        },
        content: {
            type: 'string',
            source: 'html',
            selector: '.newsletter-content',
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { title, subtitle, placeholder, buttonText, content } = attributes;
        const blockProps = useBlockProps({
            className: 'parisii-newsletter-section-block',
        });

        return (
            <div {...blockProps}>
                <div className="newsletter-settings" style={{ marginBottom: '20px', padding: '15px', background: '#f0f0f0', borderRadius: '5px' }}>
                    <h4 style={{ margin: '0 0 10px 0' }}>Paramètres de la Newsletter</h4>
                    <div style={{ display: 'flex', gap: '15px', flexWrap: 'wrap' }}>
                        <div style={{ flex: '1', minWidth: '200px' }}>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Placeholder</label>
                            <input
                                type="text"
                                value={placeholder}
                                onChange={(e) => setAttributes({ placeholder: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                        <div style={{ flex: '1', minWidth: '200px' }}>
                            <label style={{ display: 'block', marginBottom: '5px', fontWeight: 'bold' }}>Texte du bouton</label>
                            <input
                                type="text"
                                value={buttonText}
                                onChange={(e) => setAttributes({ buttonText: e.target.value })}
                                style={{ width: '100%', padding: '5px' }}
                            />
                        </div>
                    </div>
                </div>
                
                <div className="newsletter-preview" style={{ 
                    background: '#558763', 
                    color: 'white', 
                    padding: '40px 20px', 
                    borderRadius: '8px',
                    textAlign: 'center'
                }}>
                    <div style={{ maxWidth: '600px', margin: '0 auto' }}>
                        <div style={{ marginBottom: '20px' }}>
                            <RichText
                                tagName="h2"
                                value={title}
                                onChange={(value) => setAttributes({ title: value })}
                                placeholder={__('Titre de la newsletter...', 'parisii-optique')}
                                allowedFormats={['core/bold', 'core/italic']}
                                style={{ fontSize: '2rem', marginBottom: '1rem' }}
                            />
                            
                            <RichText
                                tagName="p"
                                value={subtitle}
                                onChange={(value) => setAttributes({ subtitle: value })}
                                placeholder={__('Sous-titre de la newsletter...', 'parisii-optique')}
                                allowedFormats={['core/bold', 'core/italic']}
                                style={{ fontSize: '1.25rem', opacity: '0.9', marginBottom: '2rem' }}
                            />
                        </div>
                        
                        <div style={{ 
                            display: 'flex', 
                            flexDirection: 'column', 
                            gap: '1rem', 
                            maxWidth: '400px', 
                            margin: '0 auto 2rem auto' 
                        }}>
                            <input
                                type="email"
                                placeholder={placeholder}
                                disabled
                                style={{ 
                                    padding: '12px', 
                                    borderRadius: '8px', 
                                    border: 'none',
                                    fontSize: '16px',
                                    color: '#333'
                                }}
                            />
                            <button
                                disabled
                                style={{ 
                                    padding: '12px 24px', 
                                    backgroundColor: 'white', 
                                    color: '#558763', 
                                    border: 'none',
                                    borderRadius: '8px',
                                    fontWeight: '600',
                                    cursor: 'not-allowed'
                                }}
                            >
                                {buttonText}
                            </button>
                        </div>
                        
                        {content && (
                            <div style={{ fontSize: '0.875rem', opacity: '0.75' }}>
                                <RichText
                                    tagName="div"
                                    value={content}
                                    onChange={(value) => setAttributes({ content: value })}
                                    placeholder={__('Texte additionnel (conditions, etc.)...', 'parisii-optique')}
                                    allowedFormats={['core/bold', 'core/italic', 'core/link']}
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        const { title, subtitle, placeholder, buttonText, content } = attributes;
        const blockProps = useBlockProps.save({
            className: 'parisii-newsletter-section',
        });

        return (
            <div {...blockProps}>
                <div className="max-w-2xl mx-auto text-center">
                    {title && (
                        <h2 className="newsletter-title text-3xl md:text-4xl font-heading font-bold mb-4">
                            <RichText.Content value={title} />
                        </h2>
                    )}
                    
                    {subtitle && (
                        <p className="newsletter-subtitle text-xl mb-8 opacity-90">
                            <RichText.Content value={subtitle} />
                        </p>
                    )}
                    
                    <form className="newsletter-form flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input 
                            type="email" 
                            name="email" 
                            placeholder={placeholder}
                            required
                            className="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50"
                        />
                        <button 
                            type="submit" 
                            className="px-6 py-3 bg-white text-main-500 font-semibold rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            {buttonText}
                        </button>
                    </form>
                    
                    {content && (
                        <div className="newsletter-content mt-8 text-sm opacity-75">
                            <RichText.Content value={content} />
                        </div>
                    )}
                </div>
            </div>
        );
    },
});
