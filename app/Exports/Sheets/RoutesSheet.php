<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class RoutesSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function collection()
    {
        return collect($this->routes);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'Deskripsi',
            'Koordinat (WKT)',
            'Waktu Dibuat (UTC)',
        ];
    }

    public function map($route): array
    {
        $wkt = 'LINESTRING (';
        if (!empty($route['route']) && is_array($route['route'])) {
            $coords = collect($route['route'])
                ->map(fn($c) => ($c['longitude'] ?? '0') . ' ' . ($c['latitude'] ?? '0'))
                ->implode(', ');
            $wkt .= $coords;
        }
        $wkt .= ')';

        return [
            $route['id'],
            $route['title'] ?? 'Tanpa Judul',
            $route['description'] ?? '',
            $wkt,
            isset($route['timestamp']) ? Carbon::parse($route['timestamp'])->toDateTimeString() : '',
        ];
    }

    public function title(): string
    {
        return 'Rute (Routes)';
    }
}
