<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDemoData extends Command
{
    protected $signature   = 'app:clean-demo {--force : Skip confirmation prompt}';
    protected $description = 'Supprime toutes les données métier demo en gardant superadmin, plans, paramètres et templates';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Supprimer toutes les données demo ? (superadmin, plans et paramètres seront conservés)', false)) {
            $this->info('Annulé.');
            return self::SUCCESS;
        }

        $superadminId = DB::table('users')->where('type', 'superadmin')->value('id');

        if (! $superadminId) {
            $this->error('Superadmin introuvable — abandon.');
            return self::FAILURE;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ── Données métier core ────────────────────────────────────────────
        $coreTables = [
            // Ventes
            'sales_invoice_return_item_taxes',
            'sales_invoice_return_items',
            'sales_invoice_returns',
            'sales_invoice_item_taxes',
            'sales_invoice_items',
            'sales_invoices',
            // Propositions commerciales
            'sales_proposal_item_taxes',
            'sales_proposal_items',
            'sales_proposals',
            // Achats
            'purchase_invoice_item_taxes',
            'purchase_invoice_items',
            'purchase_invoices',
            'purchase_return_item_taxes',
            'purchase_return_items',
            'purchase_returns',
            // Transferts & commandes
            'transfers',
            'orders',
            'bank_transfer_payments',
            // Entrepôts
            'warehouses',
            // Helpdesk
            'helpdesk_replies',
            'helpdesk_tickets',
            'helpdesk_categories',
            // Messagerie
            'ch_pinned',
            'ch_favorites',
            'ch_messages',
            // Divers
            'login_histories',
            'user_coupons',
            'coupons',
            'notifications',
            'media',
        ];

        foreach ($coreTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  truncated  <comment>{$table}</comment>");
            }
        }

        // ── Tables des modules packages ────────────────────────────────────
        $moduleTables = [
            // Comptabilité
            'journal_entry_items', 'journal_entries',
            'bank_transactions', 'bank_accounts', 'bank_transfers',
            'credit_note_applications', 'credit_note_item_taxes', 'credit_note_items', 'credit_notes',
            'debit_note_applications', 'debit_note_item_taxes', 'debit_note_items', 'debit_notes',
            'customer_payment_allocations', 'customer_payments',
            'vendor_payment_allocations', 'vendor_payments',
            'revenues', 'expenses',
            'opening_balances',
            // CRM
            'deal_activity_logs', 'deal_calls', 'deal_discussions', 'deal_emails', 'deal_files', 'deal_tasks', 'user_deals', 'deals',
            'lead_activity_logs', 'lead_calls', 'lead_discussions', 'lead_emails', 'lead_files', 'lead_tasks', 'user_leads', 'leads',
            'clients', 'client_deals',
            // Clients & Fournisseurs
            'customers', 'vendors',
            // Produits
            'warehouse_stocks',
            'product_service_items', 'product_service_categories', 'product_service_taxes', 'product_service_units',
            // HRM
            'payroll_entries', 'payrolls',
            'attendances', 'leaves', 'leave_applications',
            'loans', 'overtime', 'allowances', 'deductions',
            'employees',
            // Projets
            'project_activity_logs', 'project_bugs', 'project_files', 'project_milestones',
            'task_comments', 'task_subtasks', 'project_tasks',
            'timesheets', 'project_users', 'projects',
            // POS
            'pos_payments', 'pos_items', 'pos',
            // Goals / Performance
            'goal_contributions', 'goal_milestones', 'goal_tracking', 'goals',
            'performance_employee_goals', 'performance_employee_reviews', 'performance_review_cycles',
            // Recrutement
            'candidate_assessments', 'candidate_onboardings', 'candidates',
            'interview_feedbacks', 'interview_rounds', 'interviews',
            'offer_letters', 'job_postings',
            // Autres
            'contracts', 'contract_attachments', 'contract_comments', 'contract_notes', 'contract_renewals', 'contract_signatures',
            'complaints', 'resignations', 'terminations', 'warnings', 'announcements',
            'sales_quotation_item_taxes', 'sales_quotation_items', 'sales_quotations',
            'form_conversions', 'form_responses',
            'zoom_meetings',
            'newsletter_subscribers',
        ];

        foreach ($moduleTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  truncated  <comment>{$table}</comment>");
            }
        }

        // ── Utilisateurs entreprises (garder superadmin) ──────────────────
        $companyUserIds = DB::table('users')
            ->where('type', '!=', 'superadmin')
            ->pluck('id');

        if ($companyUserIds->isNotEmpty()) {
            // Supprimer les paramètres des entreprises
            DB::table('settings')->whereIn('created_by', $companyUserIds)->delete();
            // Supprimer les assignations de rôles
            if (Schema::hasTable('model_has_roles')) {
                DB::table('model_has_roles')->whereIn('model_id', $companyUserIds)->delete();
            }
            // Supprimer les utilisateurs entreprises
            DB::table('users')->whereIn('id', $companyUserIds)->delete();
            $this->line("  deleted    <comment>" . $companyUserIds->count() . " utilisateur(s) entreprise</comment>");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('✅ Données demo supprimées. Superadmin, plans, paramètres et templates conservés.');

        return self::SUCCESS;
    }
}
