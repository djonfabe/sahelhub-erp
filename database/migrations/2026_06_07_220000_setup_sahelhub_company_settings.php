<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('users')->where('type', 'superadmin')->value('id');
        if (! $adminId) return;

        $settings = [
            // ── Marque ──────────────────────────────────────────────────────
            'titleText'   => 'SahelHub',
            'footerText'  => 'Copyright © ' . date('Y') . ' SahelHub',
            'customColor' => '#13443B',
            'themeColor'  => 'custom',

            // ── Langue & région ─────────────────────────────────────────────
            'defaultLanguage'  => 'fr',
            'dateFormat'       => 'd/m/Y',
            'timeFormat'       => 'H:i',
            'calendarStartDay' => '1',   // lundi

            // ── Inscription & vérification ───────────────────────────────────
            'enableRegistration'       => 'on',
            'enableEmailVerification'  => 'on',
            'landingPageEnabled'       => 'on',

            // ── SEO ──────────────────────────────────────────────────────────
            'metaTitle'       => 'SahelHub — Plateforme ERP pour les entreprises africaines',
            'metaDescription' => 'SahelHub est la solution ERP tout-en-un pour les PME africaines : comptabilité, RH, CRM, gestion de projets et point de vente dans un seul outil cloud.',
            'metaKeywords'    => 'ERP Afrique, logiciel gestion entreprise, comptabilité, RH, CRM, SaaS Afrique de l\'Ouest, SahelHub',

            // ── Consentement cookies (FR) ─────────────────────────────────────
            'enableCookiePopup'              => '1',
            'cookieTitle'                    => 'Gestion des cookies',
            'strictlyCookieTitle'            => 'Cookies strictement nécessaires',
            'cookieDescription'              => 'Nous utilisons des cookies pour améliorer votre expérience de navigation et vous proposer un contenu personnalisé.',
            'strictlyCookieDescription'      => 'Ces cookies sont indispensables au bon fonctionnement du site.',
            'contactUsDescription'           => 'Pour toute question sur notre politique de cookies, contactez-nous.',
            'contactUsUrl'                   => 'mailto:support@sahelhub.com',

            // ── Stockage ──────────────────────────────────────────────────────
            'storageType'       => 'local',
            'allowedFileTypes'  => 'jpg,png,webp,gif,jpeg,pdf,xlsx,csv,docx',
            'maxUploadSize'     => '10240',   // 10 Mo
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'created_by' => $adminId],
                ['value' => $value, 'is_public' => true, 'updated_at' => now()]
            );
        }

        // ── Landing page : coordonnées réelles ────────────────────────────
        DB::table('landing_page_settings')
            ->where('id', '>', 0)
            ->update([
                'company_name'    => 'SahelHub',
                'contact_email'   => 'support@sahelhub.com',
                'contact_phone'   => '+221 33 000 00 00',
                'contact_address' => 'Dakar, Sénégal',
                'updated_at'      => now(),
            ]);
    }

    public function down(): void {}
};
