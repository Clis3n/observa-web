<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class NotesSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $notes;

    public function __construct(array $notes)
    {
        $this->notes = $notes;
    }

    public function collection()
    {
        return collect($this->notes);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Deskripsi',
            'Latitude',
            'Longitude',
            'Waktu Dibuat (UTC)',
        ];
    }

    public function map($note): array
    {
        return [
            $note['id'],
            $note['title'] ?? 'Tanpa Judul',
            $note['description'] ?? '',
            $note['latitude'] ?? '',
            $note['longitude'] ?? '',
            isset($note['timestamp']) ? Carbon::parse($note['timestamp'])->toDateTimeString() : '',
        ];
    }

    public function title(): string
    {
        return 'Titik Lokasi (Notes)';
    }
}
