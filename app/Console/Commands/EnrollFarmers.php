<?php

namespace App\Console\Commands;

use App\Models\Farmer;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Bulk-enrols farmers into a program so test data can be set up without
 * clicking through the UI one farmer at a time.
 *
 *   php artisan farmers:enroll
 *   php artisan farmers:enroll --program=3 --limit=20
 *   php artisan farmers:enroll --program=3 --barangay=5
 */
class EnrollFarmers extends Command
{
    protected $signature = 'farmers:enroll
                            {--program= : Program ID to enrol into (asks if omitted)}
                            {--limit= : Only enrol this many farmers (default: all)}
                            {--barangay= : Only farmers from this barangay ID}
                            {--all-statuses : Include farmers whose registration is still pending or rejected}';

    protected $description = 'Enrol multiple farmers into a program in one go (for setting up sample data)';

    public function handle(): int
    {
        $programs = Program::orderBy('name')->get();

        if ($programs->isEmpty()) {
            $this->error('There are no programs in the database yet. Create one first.');
            return self::FAILURE;
        }

        // --- pick the program ---
        $programId = $this->option('program');

        if (!$programId) {
            $this->newLine();
            $this->line('<comment>Available programs:</comment>');
            foreach ($programs as $p) {
                $count = ProgramEnrollment::where('program_id', $p->id)->where('status', 'active')->count();
                $this->line("  [{$p->id}] {$p->name}  <fg=gray>({$count} already enrolled)</>");
            }
            $this->newLine();
            $programId = $this->ask('Which program ID?');
        }

        $program = $programs->firstWhere('id', (int) $programId);

        if (!$program) {
            $this->error("No program found with ID {$programId}.");
            return self::FAILURE;
        }

        // --- pick the farmers ---
        $query = Farmer::query();

        if (!$this->option('all-statuses')) {
            // Only approved farmers by default — a pending registration isn't
            // supposed to be in a program yet.
            $query->where(function ($q) {
                $q->where('registration_status', 'approved')->orWhereNull('registration_status');
            });
        }

        if ($barangayId = $this->option('barangay')) {
            $query->where('barangay_id', $barangayId);
        }

        // Never double-enrol someone who is already active in this program.
        $alreadyIn = ProgramEnrollment::where('program_id', $program->id)
            ->where('status', 'active')
            ->pluck('farmer_id');

        $query->whereNotIn('id', $alreadyIn);

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $farmers = $query->orderBy('surname')->get();

        if ($farmers->isEmpty()) {
            // Say WHY there's nothing to do — "already enrolled" is misleading
            // when the real reason is that the filter matched no farmers at all.
            $matching = Farmer::query();
            if (!$this->option('all-statuses')) {
                $matching->where(function ($q) {
                    $q->where('registration_status', 'approved')->orWhereNull('registration_status');
                });
            }
            if ($barangayId) {
                $matching->where('barangay_id', $barangayId);
            }
            $matchingCount = $matching->count();

            if ($matchingCount === 0) {
                $this->warn($barangayId
                    ? "No approved farmers found in barangay ID {$barangayId}."
                    : 'No approved farmers found at all.');

                $this->newLine();
                $this->line('<comment>Barangays that actually have farmers:</comment>');
                $rows = Farmer::selectRaw('barangay_id, COUNT(*) as total')
                    ->whereNotNull('barangay_id')
                    ->groupBy('barangay_id')
                    ->orderByDesc('total')
                    ->get();

                if ($rows->isEmpty()) {
                    $this->line('  <fg=gray>(none — no farmer has a barangay set)</>');
                } else {
                    foreach ($rows as $row) {
                        $name = \App\Models\Barangay::find($row->barangay_id)?->name ?? 'Unknown';
                        $this->line("  [{$row->barangay_id}] {$name} — {$row->total} farmer(s)");
                    }
                }
            } else {
                $this->warn("All {$matchingCount} matching farmer(s) are already enrolled in \"{$program->name}\".");
            }

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line("About to enrol <info>{$farmers->count()}</info> farmer(s) into <info>{$program->name}</info>.");

        if (!$this->confirm('Proceed?', true)) {
            $this->line('Cancelled — nothing was changed.');
            return self::SUCCESS;
        }

        // Attribute the enrolment to the program's coordinator if it has one,
        // otherwise the first admin, so processed_by is never null.
        $processedBy = $program->assigned_user_id
            ?? User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->value('id');

        $bar = $this->output->createProgressBar($farmers->count());
        $bar->start();

        foreach ($farmers as $farmer) {
            ProgramEnrollment::create([
                'program_id'      => $program->id,
                'farmer_id'       => $farmer->id,
                'status'          => 'active',
                'enrollment_date' => now(),
                'remarks'         => 'Bulk enrolled for testing',
                'processed_by'    => $processedBy,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done — {$farmers->count()} farmer(s) enrolled into {$program->name}.");

        return self::SUCCESS;
    }
}
