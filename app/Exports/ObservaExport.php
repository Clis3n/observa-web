<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\NotesSheet;
use App\Exports\Sheets\RoutesSheet;

class ObservaExport implements WithMultipleSheets
{
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function sheets(): array
    {
        $sheets = [];

        $notes = array_filter($this->items, fn($item) => $item['type'] === 'note');
        $routes = array_filter($this->items, fn($item) => $item['type'] === 'route');

        if (!empty($notes)) {
            $sheets[] = new NotesSheet($notes);
        }

        if (!empty($routes)) {
            $sheets[] = new RoutesSheet($routes);
        }

        return $sheets;
    }
}
