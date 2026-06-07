<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $setting = DB::table('landing_page_settings')->first();
        if (! $setting) return;

        $config = json_decode($setting->config_sections, true) ?? [];

        // ── Ordre des sections (Gallery retirée — images stock génériques) ──
        $config['section_order'] = [
            'header',
            'hero',
            'stats',
            'features',
            'modules',
            'benefits',
            'cta',
            'footer',
        ];

        // ── Visibilité ─────────────────────────────────────────────────────
        $config['section_visibility'] = [
            'header'   => true,
            'hero'     => true,
            'stats'    => true,
            'features' => true,
            'modules'  => true,
            'benefits' => true,
            'gallery'  => false,   // masquée
            'cta'      => true,
            'footer'   => true,
            'pricing'  => true,
        ];

        // ── Header ─────────────────────────────────────────────────────────
        $config['sections']['header'] = array_merge(
            $config['sections']['header'] ?? [],
            [
                'variant'      => 'header1',
                'company_name' => 'SahelHub',
                'cta_text'     => 'S\'inscrire',
            ]
        );

        // ── Hero : variante hero3 (fond sombre, centré, très impactant) ────
        $config['sections']['hero'] = array_merge(
            $config['sections']['hero'] ?? [],
            [
                'variant'              => 'hero3',
                'title'                => 'La plateforme ERP tout-en-un pour les entreprises africaines',
                'subtitle'             => 'Gérez votre comptabilité, vos ressources humaines, votre CRM et vos ventes depuis une seule plateforme cloud sécurisée et accessible partout.',
                'primary_button_text'  => 'S\'inscrire gratuitement',
                'primary_button_link'  => route('register'),
                'secondary_button_text' => 'Se connecter',
                'secondary_button_link' => route('login'),
                'highlight_text'       => 'SahelHub',
            ]
        );

        // ── Stats : chiffres adaptés au marché ─────────────────────────────
        $config['sections']['stats'] = array_merge(
            $config['sections']['stats'] ?? [],
            [
                'variant' => 'stats1',
                'stats'   => [
                    ['label' => 'Entreprises actives',    'value' => '500+'],
                    ['label' => 'Disponibilité garantie', 'value' => '99.9%'],
                    ['label' => 'Support client',         'value' => '24/7'],
                    ['label' => 'Pays couverts',          'value' => '15+'],
                ],
            ]
        );

        // ── Features : variante cards 2 colonnes (plus aérée) ─────────────
        $config['sections']['features'] = array_merge(
            $config['sections']['features'] ?? [],
            [
                'variant'  => 'features3',
                'title'    => 'Tout ce dont votre entreprise a besoin',
                'subtitle' => 'Une suite complète de modules intégrés pour piloter chaque aspect de votre activité',
                'features' => [
                    ['title' => 'Comptabilité',          'description' => 'Double entrée, facturation automatique, rapprochement bancaire et états financiers en temps réel.', 'icon' => 'Calculator'],
                    ['title' => 'Ressources humaines',   'description' => 'Recrutement, paie, congés, présences et évaluation des performances en un seul outil.', 'icon' => 'UserCheck'],
                    ['title' => 'CRM & Ventes',          'description' => 'Gérez vos prospects, pipelines commerciaux et clients pour convertir davantage d\'opportunités.', 'icon' => 'Users'],
                    ['title' => 'Point de vente',        'description' => 'Caisse rapide, gestion des stocks multi-entrepôts et programmes de fidélité intégrés.', 'icon' => 'CreditCard'],
                    ['title' => 'Gestion de projets',    'description' => 'Kanban, diagrammes de Gantt, jalons et suivi du temps pour livrer dans les délais.', 'icon' => 'FolderOpen'],
                    ['title' => 'Rapports & Analytics',  'description' => 'Tableaux de bord personnalisés, indicateurs clés et rapports exportables pour décider en confiance.', 'icon' => 'Building2'],
                ],
            ]
        );

        // ── Modules : variante tabs (interactive) ──────────────────────────
        $config['sections']['modules'] = array_merge(
            $config['sections']['modules'] ?? [],
            [
                'variant'  => 'modules1',
                'title'    => 'Des solutions métier complètes',
                'subtitle' => 'Chaque module est conçu pour s\'intégrer parfaitement aux autres et simplifier vos opérations quotidiennes',
                'modules'  => [
                    [
                        'key'         => 'hrm',
                        'label'       => 'RH & Paie',
                        'title'       => 'Module RH',
                        'description' => 'Gérez l\'intégralité du cycle de vie de vos employés : recrutement, onboarding, présences, congés et traitement de la paie. Des portails en libre-service permettent à vos collaborateurs de consulter leurs bulletins et de soumettre leurs demandes en toute autonomie.',
                        'image'       => 'packages/workdo/LandingPage/src/Resources/assets/img/hrm.png',
                    ],
                    [
                        'key'         => 'account',
                        'label'       => 'Comptabilité',
                        'title'       => 'Module Comptabilité',
                        'description' => 'Système comptable en double entrée, conçu pour la précision et la rapidité. Automatisez la facturation, rapprochez vos transactions bancaires en quelques secondes et générez des bilans, comptes de résultat et flux de trésorerie conformes aux normes en vigueur.',
                        'image'       => 'packages/workdo/LandingPage/src/Resources/assets/img/accounting.png',
                    ],
                    [
                        'key'         => 'taskly',
                        'label'       => 'Projets',
                        'title'       => 'Module Projets',
                        'description' => 'Planifiez, suivez et livrez vos projets dans les délais et le budget. Visualisez les flux de travail avec des tableaux Kanban et des diagrammes de Gantt, assignez les ressources efficacement et mesurez la productivité de chaque équipe en temps réel.',
                        'image'       => 'packages/workdo/LandingPage/src/Resources/assets/img/project.png',
                    ],
                    [
                        'key'         => 'crm',
                        'label'       => 'CRM',
                        'title'       => 'Module CRM',
                        'description' => 'Transformez vos prospects en clients fidèles. Suivez chaque interaction, gérez vos pipelines commerciaux par glisser-déposer, automatisez les relances et analysez le comportement de vos clients pour conclure plus de ventes, plus rapidement.',
                        'image'       => 'packages/workdo/LandingPage/src/Resources/assets/img/crm.png',
                    ],
                    [
                        'key'         => 'pos',
                        'label'       => 'Point de vente',
                        'title'       => 'Module POS',
                        'description' => 'Caisse rapide et fiable pour vos points de vente physiques. Synchronisez vos stocks entre entrepôts en temps réel, traitez les paiements en toute sécurité et fidélisez vos clients avec des programmes de récompenses intégrés.',
                        'image'       => 'packages/workdo/LandingPage/src/Resources/assets/img/pos.png',
                    ],
                ],
            ]
        );

        // ── Benefits : variante tabs (interactive) ─────────────────────────
        $config['sections']['benefits'] = array_merge(
            $config['sections']['benefits'] ?? [],
            [
                'variant'  => 'benefits2',
                'title'    => 'Pourquoi choisir SahelHub ?',
                'benefits' => [
                    ['title' => 'Plateforme tout-en-un',          'description' => 'Fini les silos entre vos logiciels. RH, Comptabilité, CRM, Projets et POS fonctionnent en harmonie et partagent une source de données unique pour toute votre entreprise.'],
                    ['title' => 'Adapté au marché africain',       'description' => 'SahelHub a été conçu en tenant compte des réalités locales : multi-devises, conformité fiscale régionale, accès mobile optimisé pour des connexions limitées.'],
                    ['title' => 'Déploiement immédiat',           'description' => 'Pas de serveur à installer ni d\'équipe IT dédiée. Votre espace est prêt en quelques minutes avec toutes les données de votre entreprise sécurisées dans le cloud.'],
                    ['title' => 'Support dédié',                  'description' => 'Notre équipe support est disponible 24h/7j pour vous accompagner dans la prise en main et répondre à toutes vos questions métier.'],
                    ['title' => 'Sécurité & confidentialité',     'description' => 'Vos données sont chiffrées, sauvegardées quotidiennement et accessibles uniquement par les membres de votre équipe que vous autorisez.'],
                    ['title' => 'Rapports en temps réel',         'description' => 'Prenez des décisions éclairées grâce à des tableaux de bord dynamiques qui agrègent finance, ventes et opérations en un coup d\'œil.'],
                ],
            ]
        );

        // ── CTA ─────────────────────────────────────────────────────────────
        $config['sections']['cta'] = array_merge(
            $config['sections']['cta'] ?? [],
            [
                'variant'          => 'cta1',
                'title'            => 'Prêt à transformer votre entreprise ?',
                'subtitle'         => 'Rejoignez des centaines d\'entreprises africaines qui gèrent déjà leur activité avec SahelHub.',
                'primary_button'   => 'Créer un compte gratuit',
                'secondary_button' => 'Contacter l\'équipe',
            ]
        );

        // ── Footer ──────────────────────────────────────────────────────────
        $config['sections']['footer'] = array_merge(
            $config['sections']['footer'] ?? [],
            [
                'variant'                  => 'footer1',
                'description'              => 'La solution de gestion d\'entreprise tout-en-un pour les PME africaines.',
                'email'                    => 'support@sahelhub.com',
                'phone'                    => '+221 33 000 00 00',
                'newsletter_title'         => 'Restez informé',
                'newsletter_description'   => 'Abonnez-vous pour recevoir nos conseils de gestion et les dernières actualités SahelHub.',
                'newsletter_button_text'   => 'S\'abonner',
                'copyright_text'           => '© ' . date('Y') . ' SahelHub. Tous droits réservés.',
                'navigation_sections'      => [
                    [
                        'title' => 'Produit',
                        'links' => [
                            ['text' => 'Fonctionnalités', 'href' => '#features'],
                            ['text' => 'Tarifs',          'href' => '#pricing'],
                            ['text' => 'Modules',         'href' => '#modules'],
                        ],
                    ],
                    [
                        'title' => 'Entreprise',
                        'links' => [
                            ['text' => 'À propos',     'href' => '#about'],
                            ['text' => 'Contact',      'href' => '#contact'],
                            ['text' => 'Support',      'href' => '#support'],
                        ],
                    ],
                ],
            ]
        );

        DB::table('landing_page_settings')
            ->where('id', $setting->id)
            ->update(['config_sections' => json_encode($config)]);
    }

    public function down(): void {}
};
