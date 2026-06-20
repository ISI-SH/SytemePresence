<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportPointages implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with('user');

        if (isset($this->filters['date_start']) && $this->filters['date_start']) {
            $query->whereDate('date', '>=', $this->filters['date_start']);
        }

        if (isset($this->filters['date_end']) && $this->filters['date_end']) {
            $query->whereDate('date', '<=', $this->filters['date_end']);
        }

        if (isset($this->filters['employee_id']) && $this->filters['employee_id']) {
            $query->where('user_id', $this->filters['employee_id']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employé',
            'Email',
            'Date',
            'Heure arrivée',
            'Heure départ',
            'Statut',
            'Heures travaillées'
        ];
    }

    public function map($pointage): array
    {
        return [
            $pointage->id,
            $pointage->user?->name ?? '-',
            $pointage->user?->email ?? '-',
            $pointage->date ? $pointage->date->format('d/m/Y') : '-',
            $pointage->check_in ? $pointage->check_in->format('H:i') : '-',
            $pointage->check_out ? $pointage->check_out->format('H:i') : '-',
            $pointage->statusLabel(),
            $pointage->hoursWorkedFormatted(),
        ];
    }
}
