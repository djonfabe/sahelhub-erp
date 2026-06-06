import { ArrowRight } from 'lucide-react';
import { getImagePath } from '@/utils/helpers';
import { useTranslation } from 'react-i18next';

interface HeroProps {
    settings?: any;
}

const HERO_VARIANTS = {
    hero1: {
        section: 'bg-white py-20',
        container: 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
        title: 'text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight',
        subtitle: 'text-xl text-gray-600 mb-8 leading-relaxed',
        buttons: 'flex flex-col sm:flex-row gap-4',
        primaryBtn: 'text-white px-8 py-3 rounded-lg text-lg font-medium flex items-center transition-all duration-300 shadow-lg hover:shadow-xl',
        secondaryBtn: 'border-2 border-gray-300 text-gray-700 px-8 py-3 rounded-lg text-lg font-medium hover:bg-gray-50 transition-all duration-300',
        layout: 'right-split',
        showImage: true
    },
    hero2: {
        section: 'bg-white py-20',
        container: 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
        title: 'text-4xl md:text-5xl font-bold text-gray-900 mb-6',
        subtitle: 'text-xl text-gray-600 mb-8',
        buttons: 'flex flex-col sm:flex-row gap-4 justify-center',
        primaryBtn: 'text-white px-8 py-3 rounded-md text-lg font-medium flex items-center justify-center transition-colors',
        secondaryBtn: 'border border-gray-300 text-gray-700 px-8 py-3 rounded-md text-lg font-medium hover:bg-gray-50',
        layout: 'split',
        showImage: true
    },
    hero3: {
        section: 'relative bg-gray-900 py-32 md:py-40 overflow-hidden',
        container: 'relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white z-10',
        title: 'text-4xl md:text-6xl lg:text-7xl font-bold mb-8 leading-tight drop-shadow-2xl',
        subtitle: 'text-xl md:text-2xl mb-12 max-w-4xl mx-auto opacity-95 leading-relaxed',
        buttons: 'flex flex-col sm:flex-row gap-6 justify-center items-center',
        primaryBtn: 'text-white px-10 py-4 rounded-2xl text-lg font-semibold flex items-center justify-center transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 backdrop-blur-sm',
        secondaryBtn: 'border-2 border-white/30 text-white px-10 py-4 rounded-2xl text-lg font-semibold hover:bg-white/10 hover:border-white/50 transition-all duration-300 backdrop-blur-sm',
        layout: 'background',
        showImage: false
    },
    hero4: {
        section: 'bg-white py-16 md:py-20 border-b border-gray-100',
        container: 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center',
        title: 'text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight tracking-tight',
        subtitle: 'text-lg md:text-xl text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed font-medium',
        buttons: 'flex flex-col sm:flex-row gap-3 justify-center items-center',
        primaryBtn: 'text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md',
        secondaryBtn: 'text-gray-600 px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all duration-200 border border-gray-200 hover:border-gray-300',
        layout: 'minimal',
        showImage: false
    },
    hero5: {
        section: 'relative py-28 md:py-36 overflow-hidden',
        container: 'relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white z-10',
        title: 'text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight tracking-tight',
        subtitle: 'text-lg md:text-xl mb-10 max-w-3xl mx-auto leading-relaxed',
        buttons: 'flex flex-col sm:flex-row gap-4 justify-center items-center',
        primaryBtn: 'text-white px-8 py-4 rounded-xl text-base font-bold flex items-center justify-center transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5',
        secondaryBtn: 'border-2 border-white/30 text-white px-8 py-4 rounded-xl text-base font-semibold hover:bg-white/10 hover:border-white/50 transition-all duration-300',
        layout: 'branded',
        showImage: false
    }
};

export default function Hero({ settings }: HeroProps) {
    const { t } = useTranslation();
    const sectionData = settings?.config_sections?.sections?.hero || {};
    const variant = sectionData.variant || 'hero5';
    const config = HERO_VARIANTS[variant as keyof typeof HERO_VARIANTS] || HERO_VARIANTS.hero1;
    
    const title = sectionData.title || 'Transform Your Business with SahelHub';
    const subtitle = sectionData.subtitle || 'The complete all-in-one business management solution that combines ERP, Accounting, CRM, POS, HRM, and Project Management into a single powerful platform.';
    const primaryButtonText = sectionData.primary_button_text || 'Start Free Trial';
    const primaryButtonLink = sectionData.primary_button_link || route('register');
    const secondaryButtonText = sectionData.secondary_button_text || 'Request Demo';
    const secondaryButtonLink = sectionData.secondary_button_link || route('login');
    const highlightText = sectionData.highlight_text;
    const heroImage = sectionData.image;
    const colors = settings?.config_sections?.colors || { primary: '#13443B', secondary: '#E0762A', accent: '#F4B23E' };

    const renderTitle = () => {
        if (highlightText && title?.includes(highlightText)) {
            const titleParts = title.split(highlightText);
            return (
                <>
                    {titleParts[0]}
                    <span style={{ color: colors.primary }}>{highlightText}</span>
                    {titleParts[1]}
                </>
            );
        }
        return title;
    };

    const renderButtons = () => (
        <div className={config.buttons}>
            <button 
                className={config.primaryBtn} 
                style={{ backgroundColor: colors.primary }} 
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = colors.secondary} 
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = colors.primary}
                onClick={() => window.location.href = primaryButtonLink}
            >
                {primaryButtonText}
                {config.layout !== 'minimal' && <ArrowRight className="ml-3 h-6 w-6" />}
            </button>
            <button 
                className={config.secondaryBtn}
                onClick={() => window.location.href = secondaryButtonLink}
            >
                {secondaryButtonText}
            </button>
        </div>
    );

    const renderBackgroundImage = () => {
        if (config.layout !== 'background') return null;
        return (
            <>
                {heroImage && (
                    <div className="absolute inset-0">
                        <img src={getImagePath(heroImage)} alt="Hero Background" className="w-full h-full object-cover" />
                        <div className="absolute inset-0 bg-gradient-to-br from-black/60 via-black/40 to-black/60"></div>
                    </div>
                )}
                {!heroImage && (
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black"></div>
                )}
                <div className="absolute inset-0">
                    <div className="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div className="absolute bottom-20 right-20 w-48 h-48 bg-white/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
                    <div className="absolute top-1/2 left-1/3 w-24 h-24 bg-white/10 rounded-full blur-2xl animate-pulse delay-500"></div>
                </div>
            </>
        );
    };

    const renderImage = () => {
        if (!config.showImage || config.layout === 'background') return null;
        
        return (
            <div className={`bg-gray-100 rounded-xl ${config.layout === 'split' || config.layout === 'right-split' ? '' : 'mt-12'} flex items-center justify-center overflow-hidden shadow-[0px_2px_4px_0px_rgba(14,30,37,0.12),0px_2px_16px_0px_rgba(14,30,37,0.32)]`}>
                {heroImage ? (
                    <img src={getImagePath(heroImage)} alt="Hero" className="w-full h-full object-cover" />
                ) : (
                    <span className="text-gray-500">{t('Hero Image')}</span>
                )}
            </div>
        );
    };

    const renderContent = () => (
        <div className={config.layout === 'split' || config.layout === 'right-split' ? '' : 'w-full text-center'}>
            <h1 className={config.title}>
                {renderTitle()}
            </h1>
            <p className={config.subtitle}>
                {subtitle}
            </p>
            {renderButtons()}
        </div>
    );

    if (config.layout === 'branded') {
        return (
            <section className={config.section} style={{ backgroundColor: colors.primary }}>
                <div className="absolute inset-0 overflow-hidden pointer-events-none">
                    <svg className="absolute -top-10 -right-10 w-[500px] h-[500px] opacity-[0.07]" viewBox="0 0 200 231" preserveAspectRatio="xMidYMid slice">
                        <defs>
                            <pattern id="hexgrid" x="0" y="0" width="40" height="46" patternUnits="userSpaceOnUse">
                                <polygon points="20,0 40,11.5 40,34.5 20,46 0,34.5 0,11.5" fill="none" stroke="white" strokeWidth="1.5"/>
                            </pattern>
                        </defs>
                        <rect width="200" height="231" fill="url(#hexgrid)"/>
                    </svg>
                    <div className="absolute -bottom-20 -left-20 w-[400px] h-[400px] rounded-full opacity-[0.07] blur-3xl" style={{ backgroundColor: colors.secondary }}></div>
                    <div className="absolute top-10 right-1/3 w-[200px] h-[200px] rounded-full opacity-[0.05] blur-2xl" style={{ backgroundColor: colors.accent }}></div>
                    <div className="absolute top-1/4 left-1/4 w-2 h-2 bg-white/40 rounded-full animate-ping"></div>
                    <div className="absolute top-3/4 right-1/4 w-1.5 h-1.5 bg-white/30 rounded-full animate-ping" style={{ animationDelay: '700ms' }}></div>
                    <div className="absolute top-1/2 right-1/3 w-1 h-1 bg-white/20 rounded-full animate-ping" style={{ animationDelay: '1200ms' }}></div>
                </div>
                <div className={config.container}>
                    <div className="mb-6">
                        <span className="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium border border-white/20" style={{ backgroundColor: 'rgba(255,255,255,0.08)' }}>
                            🌍 La solution ERP tout-en-un pour l'Afrique
                        </span>
                    </div>
                    <h1 className={config.title}>{renderTitle()}</h1>
                    <p className={`${config.subtitle} opacity-85`}>{subtitle}</p>
                    <div className={config.buttons}>
                        <button
                            className={config.primaryBtn}
                            style={{ backgroundColor: colors.secondary }}
                            onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = colors.accent; }}
                            onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = colors.secondary; }}
                            onClick={() => window.location.href = primaryButtonLink}
                        >
                            {primaryButtonText}
                            <ArrowRight className="ml-2 h-5 w-5" />
                        </button>
                        <button
                            className={config.secondaryBtn}
                            onClick={() => window.location.href = secondaryButtonLink}
                        >
                            {secondaryButtonText}
                        </button>
                    </div>
                    <div className="mt-12 flex flex-wrap justify-center gap-6 md:gap-10">
                        {['20,000+ entreprises', '99.9% disponibilité', 'Support 24/7', '70+ pays'].map((item, i) => (
                            <div key={i} className="flex items-center gap-2 text-white/70 text-sm">
                                <svg className="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                </svg>
                                {item}
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className={config.section}>
            {renderBackgroundImage()}
            <div className={config.container}>
                {config.layout === 'split' || config.layout === 'right-split' ? (
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        {config.layout === 'right-split' ? (
                            <>
                                {renderImage()}
                                {renderContent()}
                            </>
                        ) : (
                            <>
                                {renderContent()}
                                {renderImage()}
                            </>
                        )}
                    </div>
                ) : config.layout === 'background' ? (
                    <div className="relative">
                        {renderContent()}
                        <div className="absolute inset-0 pointer-events-none">
                            <div className="absolute top-1/4 left-1/4 w-2 h-2 bg-white rounded-full animate-ping"></div>
                            <div className="absolute top-3/4 right-1/4 w-1 h-1 bg-white/70 rounded-full animate-ping delay-700"></div>
                            <div className="absolute top-1/2 right-1/3 w-1.5 h-1.5 bg-white/50 rounded-full animate-ping delay-1000"></div>
                        </div>
                    </div>
                ) : (
                    <>
                        {renderContent()}
                        {renderImage()}
                    </>
                )}
            </div>
        </section>
    );
}