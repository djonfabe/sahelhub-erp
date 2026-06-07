<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('users')->where('type', 'superadmin')->value('id');

        // ── 1. Textes landing page — positionnement global francophone ─────
        $setting = DB::table('landing_page_settings')->first();
        if ($setting) {
            $config = json_decode($setting->config_sections, true) ?? [];

            // Hero : universel, pas de référence géographique
            $config['sections']['hero'] = array_merge(
                $config['sections']['hero'] ?? [],
                [
                    'title'                => 'La plateforme ERP tout-en-un pour les PME modernes',
                    'subtitle'             => 'Comptabilité, RH, CRM, ventes et gestion de projets réunis dans une seule plateforme cloud. En français, simple et puissant.',
                    'primary_button_text'  => "S'inscrire gratuitement",
                    'secondary_button_text' => 'Se connecter',
                    'highlight_text'       => 'SahelHub',
                ]
            );

            // Stats : neutres, crédibles
            $config['sections']['stats'] = array_merge(
                $config['sections']['stats'] ?? [],
                [
                    'stats' => [
                        ['label' => 'Entreprises actives',    'value' => '500+'],
                        ['label' => 'Disponibilité garantie', 'value' => '99.9%'],
                        ['label' => 'Support client',         'value' => '24/7'],
                        ['label' => 'Modules intégrés',       'value' => '20+'],
                    ],
                ]
            );

            // Features : universelles
            $config['sections']['features'] = array_merge(
                $config['sections']['features'] ?? [],
                [
                    'title'    => 'Tout ce dont votre entreprise a besoin',
                    'subtitle' => 'Une suite complète de modules intégrés pour piloter chaque aspect de votre activité, où que vous soyez.',
                    'features' => [
                        ['title' => 'Comptabilité',         'description' => 'Double entrée, facturation automatique, rapprochement bancaire et états financiers en temps réel.', 'icon' => 'Calculator'],
                        ['title' => 'Ressources humaines',  'description' => 'Recrutement, paie, congés, présences et évaluation des performances dans un seul outil.', 'icon' => 'UserCheck'],
                        ['title' => 'CRM & Ventes',         'description' => 'Gérez vos prospects, pipelines commerciaux et clients pour convertir davantage d\'opportunités.', 'icon' => 'Users'],
                        ['title' => 'Point de vente',       'description' => 'Caisse rapide, gestion des stocks multi-entrepôts et programmes de fidélité intégrés.', 'icon' => 'CreditCard'],
                        ['title' => 'Gestion de projets',   'description' => 'Kanban, diagrammes de Gantt, jalons et suivi du temps pour livrer dans les délais.', 'icon' => 'FolderOpen'],
                        ['title' => 'Rapports & Analytics', 'description' => 'Tableaux de bord personnalisés et rapports exportables pour décider en confiance.', 'icon' => 'Building2'],
                    ],
                ]
            );

            // Benefits : avantages universels — supprimer "marché africain"
            $config['sections']['benefits'] = array_merge(
                $config['sections']['benefits'] ?? [],
                [
                    'title'    => 'Pourquoi choisir SahelHub ?',
                    'benefits' => [
                        ['title' => 'Plateforme tout-en-un',        'description' => 'Fini les silos entre vos logiciels. RH, Comptabilité, CRM, Projets et POS fonctionnent en harmonie et partagent une source de données unique pour toute votre entreprise.'],
                        ['title' => 'Interface 100 % en français',   'description' => 'Conçu dès le départ pour les équipes francophones. Chaque fonctionnalité, chaque notification et chaque rapport est en français, sans traduction approximative.'],
                        ['title' => 'Déploiement immédiat',          'description' => 'Pas de serveur à installer ni d\'équipe IT dédiée. Votre espace est prêt en quelques minutes et accessible depuis n\'importe quel appareil connecté.'],
                        ['title' => 'Support dédié',                 'description' => 'Notre équipe support est disponible pour vous accompagner dans la prise en main et répondre à toutes vos questions métier.'],
                        ['title' => 'Sécurité & confidentialité',    'description' => 'Vos données sont chiffrées, sauvegardées quotidiennement et accessibles uniquement par les membres de votre équipe que vous autorisez.'],
                        ['title' => 'Rapports en temps réel',        'description' => 'Prenez des décisions éclairées grâce à des tableaux de bord dynamiques qui agrègent finance, ventes et opérations en un coup d\'œil.'],
                    ],
                ]
            );

            // CTA : universel
            $config['sections']['cta'] = array_merge(
                $config['sections']['cta'] ?? [],
                [
                    'title'          => 'Prêt à simplifier la gestion de votre entreprise ?',
                    'subtitle'       => 'Rejoignez des centaines d\'entreprises qui gèrent déjà leur activité avec SahelHub.',
                    'primary_button' => 'Créer un compte gratuit',
                    'secondary_button' => 'Contacter l\'équipe',
                ]
            );

            // Footer : coordonnées neutres
            $config['sections']['footer'] = array_merge(
                $config['sections']['footer'] ?? [],
                [
                    'description'            => 'La solution ERP tout-en-un pour les PME francophones du monde entier.',
                    'email'                  => 'support@sahelhub.com',
                    'phone'                  => '',
                    'newsletter_title'       => 'Restez informé',
                    'newsletter_description' => 'Abonnez-vous pour recevoir nos conseils de gestion et les dernières actualités SahelHub.',
                    'newsletter_button_text' => "S'abonner",
                    'copyright_text'         => '© ' . date('Y') . ' SahelHub. Tous droits réservés.',
                ]
            );

            DB::table('landing_page_settings')
                ->where('id', $setting->id)
                ->update([
                    'company_name'    => 'SahelHub',
                    'contact_email'   => 'support@sahelhub.com',
                    'contact_phone'   => '',
                    'contact_address' => '',
                    'config_sections' => json_encode($config),
                    'updated_at'      => now(),
                ]);
        }

        // ── 2. SEO — positionnement global francophone ─────────────────────
        if ($adminId) {
            $seo = [
                'metaTitle'       => 'SahelHub — ERP tout-en-un pour les PME francophones',
                'metaDescription' => 'SahelHub est la plateforme ERP cloud en français pour les PME : comptabilité, RH, CRM, gestion de projets et point de vente dans un seul outil.',
                'metaKeywords'    => 'ERP français, logiciel gestion PME, comptabilité, RH, CRM, SaaS francophone, ERP cloud, SahelHub',
            ];
            foreach ($seo as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key, 'created_by' => $adminId],
                    ['value' => $value, 'is_public' => true, 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void {}
};
