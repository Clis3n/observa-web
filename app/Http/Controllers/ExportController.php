<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Services\KmlGeneratorService;
use App\Exports\ObservaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class ExportController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'string',
        ]);

        $userId = Session::get('firebase_user_id');
        $items = $this->firebaseService->getCombinedItemsByIds($userId, $request->selected_ids);

        $filename = 'observa_data_'.date('Ymd_His').'.xlsx';

        return Excel::download(new ObservaExport($items), $filename);
    }

    public function exportKml(Request $request, KmlGeneratorService $kmlGenerator, $format = 'kml')
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'string',
        ]);

        $userId = Session::get('firebase_user_id');
        $items = $this->firebaseService->getCombinedItemsByIds($userId, $request->selected_ids);

        $notes = array_filter($items, fn($item) => $item['type'] === 'note');
        $routes = array_filter($items, fn($item) => $item['type'] === 'route');

        $kmlContent = $kmlGenerator->generate($notes, $routes);
        $filename = 'observa_data_'.date('Ymd_His');

        if (strtolower($format) === 'kmz') {
            $zip = new \ZipArchive();
            $zipPath = storage_path('app/'.$filename.'.zip');

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFromString('doc.kml', $kmlContent);
                $zip->close();
                return response()->download($zipPath, $filename.'.kmz')->deleteFileAfterSend(true);
            }
        }

        return response($kmlContent, 200, [
            'Content-Type' => 'application/vnd.google-earth.kml+xml',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.kml"',
        ]);
    }
}
