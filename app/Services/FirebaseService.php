<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Auth; // <-- Tambahkan ini jika belum ada
use Kreait\Firebase\Contract\Firestore; // <-- Tambahkan ini

class FirebaseService
{
    protected \Google\Cloud\Firestore\FirestoreClient $firestore; // <-- Ubah tipe menjadi lebih spesifik
    protected Auth $auth;

    // [PERBAIKAN UTAMA] Terima Factory yang sudah dibuat via dependency injection
    public function __construct(Factory $firebaseFactory)
    {
        $this->firestore = $firebaseFactory->createFirestore()->database();
        $this->auth = $firebaseFactory->createAuth();
    }

    private function getNotesCollection($userId)
    {
        return $this->firestore->collection('notes')->document($userId)->collection('user_notes');
    }

    // ... (sisa kode FirebaseService tetap sama) ...
    private function getRoutesCollection($userId)
    {
        return $this->firestore->collection('routes')->document($userId)->collection('user_routes');
    }

    public function getNotes($userId)
    {
        $notes = [];
        $documents = $this->getNotesCollection($userId)->orderBy('timestamp', 'DESC')->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $data = $document->data();
                $data['id'] = $document->id();
                $data['type'] = 'note';
                if (isset($data['timestamp']) && $data['timestamp'] instanceof Timestamp) {
                    $data['timestamp'] = $data['timestamp']->get()->format('Y-m-d\TH:i:s\Z');
                }
                $notes[] = $data;
            }
        }
        return $notes;
    }

    public function getRoutes($userId)
    {
        $routes = [];
        $documents = $this->getRoutesCollection($userId)->orderBy('timestamp', 'DESC')->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $data = $document->data();
                $data['id'] = $document->id();
                $data['type'] = 'route';
                 if (isset($data['timestamp']) && $data['timestamp'] instanceof Timestamp) {
                    $data['timestamp'] = $data['timestamp']->get()->format('Y-m-d\TH:i:s\Z');
                }
                $routes[] = $data;
            }
        }
        return $routes;
    }

    public function deleteItem($userId, $type, $itemId)
    {
        if ($type === 'note') {
            $this->getNotesCollection($userId)->document($itemId)->delete();
        } elseif ($type === 'route') {
            $this->getRoutesCollection($userId)->document($itemId)->delete();
        }
    }

    public function updateNote($userId, $noteId, $data)
    {
        $noteRef = $this->getNotesCollection($userId)->document($noteId);
        $updateData = [
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'latitude' => (float)($data['latitude'] ?? 0),
            'longitude' => (float)($data['longitude'] ?? 0),
            'place' => (string)($data['place'] ?? ''),
            'timestamp' => FieldValue::serverTimestamp()
        ];
        $noteRef->set($updateData, ['merge' => true]);
        return $noteRef->snapshot()->data();
    }

    public function getCombinedItemsByIds($userId, array $typedIds)
    {
        $items = [];
        foreach ($typedIds as $typedId) {
            // Pastikan formatnya benar sebelum di-explode
            if (strpos($typedId, ':') === false) continue;

            [$type, $id] = explode(':', $typedId, 2);
            $document = null;

            try {
                if ($type === 'note') {
                    $document = $this->getNotesCollection($userId)->document($id)->snapshot();
                } elseif ($type === 'route') {
                    $document = $this->getRoutesCollection($userId)->document($id)->snapshot();
                }
            } catch (\Exception $e) {
                // Abaikan jika dokumen tidak ditemukan atau ada error
                continue;
            }

            if ($document && $document->exists()) {
                $data = $document->data();
                $data['id'] = $document->id();
                $data['type'] = $type;
                if (isset($data['timestamp']) && $data['timestamp'] instanceof Timestamp) {
                    $data['timestamp'] = $data['timestamp']->get()->format('Y-m-d\TH:i:s\Z');
                }
                $items[] = $data;
            }
        }
        return $items;
    }

    public function updateRoute($userId, $routeId, $data)
    {
        $routeRef = $this->getRoutesCollection($userId)->document($routeId);
        $updateData = [
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'timestamp' => FieldValue::serverTimestamp()
        ];
        $routeRef->set($updateData, ['merge' => true]);
        return $routeRef->snapshot()->data();
    }
}
