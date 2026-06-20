<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->migratePointages();
        $this->migrateLeaveRequests();
        $this->migrateDailyTokens();

        Schema::dropIfExists('pointages');
        Schema::dropIfExists('demandes_conges');
        Schema::dropIfExists('qr_codes');
    }

    public function down(): void
    {
        if (!Schema::hasTable('pointages')) {
            Schema::create('pointages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->enum('status', ['Présent', 'En retard', 'Absent'])->default('Absent');
                $table->decimal('hours_worked', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('demandes_conges')) {
            Schema::create('demandes_conges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->text('reason');
                $table->enum('status', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('qr_codes')) {
            Schema::create('qr_codes', function (Blueprint $table) {
                $table->id();
                $table->string('token')->unique();
                $table->date('date')->unique();
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    private function migratePointages(): void
    {
        if (!Schema::hasTable('pointages') || !Schema::hasTable('attendances')) {
            return;
        }

        DB::table('pointages')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $date = $row->check_in
                    ? Carbon::parse($row->check_in)->toDateString()
                    : Carbon::parse($row->created_at ?? now())->toDateString();

                $exists = DB::table('attendances')
                    ->where('user_id', $row->user_id)
                    ->whereDate('date', $date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $hoursWorked = null;
                if ($row->check_in && $row->check_out) {
                    $hoursWorked = max(0, Carbon::parse($row->check_in)->diffInMinutes(Carbon::parse($row->check_out), false));
                } elseif ($row->hours_worked !== null) {
                    $hoursWorked = max(0, (int) round(((float) $row->hours_worked) * 60));
                }

                DB::table('attendances')->insert([
                    'user_id' => $row->user_id,
                    'date' => $date,
                    'check_in' => $row->check_in,
                    'check_out' => $row->check_out,
                    'status' => match ($row->status) {
                        'Présent' => 'present',
                        'En retard' => 'late',
                        'Départ anticipé' => 'early_departure',
                        default => 'absent',
                    },
                    'method' => 'legacy_migration',
                    'hours_worked' => $hoursWorked,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }
        });
    }

    private function migrateLeaveRequests(): void
    {
        if (!Schema::hasTable('demandes_conges') || !Schema::hasTable('leave_requests')) {
            return;
        }

        DB::table('demandes_conges')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $exists = DB::table('leave_requests')
                    ->where('user_id', $row->user_id)
                    ->whereDate('start_date', $row->start_date)
                    ->whereDate('end_date', $row->end_date)
                    ->where('reason', $row->reason)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('leave_requests')->insert([
                    'user_id' => $row->user_id,
                    'start_date' => $row->start_date,
                    'end_date' => $row->end_date,
                    'reason' => $row->reason,
                    'status' => match ($row->status) {
                        'accepte' => 'approved',
                        'refuse' => 'rejected',
                        default => 'pending',
                    },
                    'reviewed_by' => $row->reviewed_by,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }
        });
    }

    private function migrateDailyTokens(): void
    {
        if (!Schema::hasTable('qr_codes') || !Schema::hasTable('daily_tokens')) {
            return;
        }

        DB::table('qr_codes')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $exists = DB::table('daily_tokens')
                    ->whereDate('date', $row->date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('daily_tokens')->insert([
                    'token' => $row->token,
                    'date' => $row->date,
                    'expires_at' => $row->expires_at,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }
        });
    }
};
