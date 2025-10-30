<?php

namespace App\Console\Commands;

use App\Events\Document\DocumentExpiring;
use App\Events\Employee\ContractExpiring;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Console\Command;

class CheckExpirations extends Command
{
    protected $signature = 'notifications:check-expirations';

    protected $description = 'Verificar contratos de empleados y documentos próximos a vencer';

    public function handle()
    {
        $this->info('🔍 Verificando vencimientos...');

        // Verificar contratos que vencen en 30 días
        $this->checkContracts30Days();

        // Verificar contratos que vencen en 7 días
        $this->checkContracts7Days();

        // Verificar documentos que vencen o ya vencieron
        $this->checkDocuments();

        $this->info('✅ Verificación completada!');
    }

    protected function checkContracts30Days()
    {
        // Asumiendo que tienes un campo 'contract_end_date' en la tabla employees
        $employees = Employee::whereBetween('contract_end_date', [
            now()->addDays(29)->startOfDay(),
            now()->addDays(30)->endOfDay(),
        ])->get();

        foreach ($employees as $employee) {
            event(new ContractExpiring($employee, $employee->contract_end_date));
        }

        $this->info("   📋 Contratos (30 días): {$employees->count()}");
    }

    protected function checkContracts7Days()
    {
        $employees = Employee::whereBetween('contract_end_date', [
            now()->addDays(6)->startOfDay(),
            now()->addDays(7)->endOfDay(),
        ])->get();

        foreach ($employees as $employee) {
            event(new ContractExpiring($employee, $employee->contract_end_date));
        }

        $this->info("   ⚠️  Contratos (7 días): {$employees->count()}");
    }

    protected function checkDocuments()
    {
        // Documentos que vencen en los próximos 30 días o ya vencieron (hasta 1 día atrás)
        $documents = Document::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [
                now()->subDay()->startOfDay(),
                now()->addDays(30)->endOfDay(),
            ])
            ->with('employee')
            ->get();

        foreach ($documents as $document) {
            event(new DocumentExpiring($document, $document->expiration_date));
        }

        $this->info("   📄 Documentos: {$documents->count()}");
    }
}
