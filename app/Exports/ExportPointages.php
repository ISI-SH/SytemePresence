<?php

namespace App\Exports;

use App\Models\Pointage;
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
        $query = Pointage::with('user');

        // Appliquer les filtres
        if (isset($this->filters['date_start']) && $this->filters['date_start']) {
            $query->whereDate('check_in', '>=', $this->filters['date_start']);
        }

        if (isset($this->filters['date_end']) && $this->filters['date_end']) {
            $query->whereDate('check_in', '<=', $this->filters['date_end']);
        }

        if (isset($this->filters['employee_id']) && $this->filters['employee_id']) {
            $query->where('user_id', $this->filters['employee_id']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('check_in', 'desc')->get();
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
            $pointage->user->name,
            $pointage->user->email,
            $pointage->check_in ? $pointage->check_in->format('d/m/Y') : '-',
            $pointage->check_in ? $pointage->check_in->format('H:i') : '-',
            $pointage->check_out ? $pointage->check_out->format('H:i') : '-',
            $pointage->status,
            $pointage->hours_worked ? $pointage->hours_worked . 'h' : '-'
        ];
    }
}
