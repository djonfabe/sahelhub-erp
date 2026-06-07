<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('users')->where('type', 'superadmin')->value('id');

        // ── 1. Supprimer les plans demo génériques ─────────────────────────
        DB::table('plans')->delete();

        // ── 2. Créer les 4 plans SahelHub en FCFA ─────────────────────────
        $now = now();

        $plans = [
            [
                'name'                  => 'Gratuit',
                'description'           => 'Idéal pour démarrer et explorer les fonctionnalités essentielles de SahelHub.',
                'number_of_users'       => 5,
                'status'                => true,
                'free_plan'             => true,
                'modules'               => json_encode(['Taskly', 'Calendar', 'SupportTicket']),
                'package_price_monthly' => 0,
                'package_price_yearly'  => 0,
                'storage_limit'         => 512000,    // 500 Mo
                'trial'                 => false,
                'trial_days'            => 0,
                'created_by'            => $adminId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'name'                  => 'Starter',
                'description'           => 'Pour les petites équipes qui veulent gérer leur activité avec les outils essentiels.',
                'number_of_users'       => 20,
                'status'                => true,
                'free_plan'             => false,
                'modules'               => json_encode([
                    'Taskly', 'Account', 'Calendar',
                    'SupportTicket', 'FormBuilder', 'Quotation',
                ]),
                'package_price_monthly' => 15000,
                'package_price_yearly'  => 150000,
                'storage_limit'         => 5000000,   // 5 Go
                'trial'                 => true,
                'trial_days'            => 14,
                'created_by'            => $adminId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'name'                  => 'Professionnel',
                'description'           => 'La suite complète pour les entreprises en croissance : RH, CRM, Comptabilité, Projets et POS.',
                'number_of_users'       => 100,
                'status'                => true,
                'free_plan'             => false,
                'modules'               => json_encode([
                    'Taskly', 'Account', 'Hrm', 'Lead', 'Pos',
                    'BudgetPlanner', 'Calendar', 'Contract', 'DoubleEntry',
                    'FormBuilder', 'Goal', 'Performance', 'Quotation',
                    'Recruitment', 'SupportTicket', 'Timesheet',
                    'Training', 'ZoomMeeting',
                ]),
                'package_price_monthly' => 35000,
                'package_price_yearly'  => 350000,
                'storage_limit'         => 25000000,  // 25 Go
                'trial'                 => true,
                'trial_days'            => 30,
                'created_by'            => $adminId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'name'                  => 'Entreprise',
                'description'           => 'Tous les modules, utilisateurs illimités et intégrations avancées pour les grandes organisations.',
                'number_of_users'       => -1,         // illimité
                'status'                => true,
                'free_plan'             => false,
                'modules'               => json_encode([
                    'Taskly', 'Account', 'Hrm', 'Lead', 'Pos',
                    'Stripe', 'Paypal', 'AIAssistant', 'BudgetPlanner',
                    'Calendar', 'Contract', 'DoubleEntry', 'FormBuilder',
                    'Goal', 'Performance', 'Quotation', 'Recruitment',
                    'Slack', 'SupportTicket', 'Telegram', 'Timesheet',
                    'Training', 'Twilio', 'Webhook', 'ZoomMeeting',
                ]),
                'package_price_monthly' => 75000,
                'package_price_yearly'  => 750000,
                'storage_limit'         => 100000000, // 100 Go
                'trial'                 => true,
                'trial_days'            => 30,
                'created_by'            => $adminId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ];

        DB::table('plans')->insert($plans);

        // ── 3. Mettre à jour la devise (FCFA) dans les paramètres admin ────
        if ($adminId) {
            $currencySettings = [
                'defaultCurrency'        => 'XOF',
                'currencySymbol'         => 'FCFA',
                'currencySymbolPosition' => 'right',
                'currencySymbolSpace'    => '1',      // espace avant le symbole
                'decimalFormat'          => '0',      // pas de décimales en FCFA
                'decimalSeparator'       => ',',
                'thousandsSeparator'     => ' ',      // espace fine en typographie française
                'floatNumber'            => '0',
            ];

            foreach ($currencySettings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key, 'created_by' => $adminId],
                    ['value' => $value, 'is_public' => true, 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void {}
};
