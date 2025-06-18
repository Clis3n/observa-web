<?php

namespace App\Services;

class KmlGeneratorService
{
    public function generate(array $notes, array $routes): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"></kml>');
        $document = $xml->addChild('Document');
        $document->addChild('name', 'Observa Export Data');
        $document->addChild('description', 'Data diekspor dari aplikasi Observa Web.');

        $this->addStyles($document);

        foreach ($notes as $note) {
            $this->addNotePlacemark($document, $note);
        }

        foreach ($routes as $route) {
            $this->addRoutePlacemark($document, $route);
        }

        // Proper formatting for XML output
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    private function addStyles(\SimpleXMLElement $document): void
    {
        // Style and StyleMap for Notes (Points)
        $styleMapNote = $document->addChild('StyleMap');
        $styleMapNote->addAttribute('id', 'style_note');
        $pairNormalNote = $styleMapNote->addChild('Pair');
        $pairNormalNote->addChild('key', 'normal');
        $pairNormalNote->addChild('styleUrl', '#style_note_normal');
        $pairHighlightNote = $styleMapNote->addChild('Pair');
        $pairHighlightNote->addChild('key', 'highlight');
        $pairHighlightNote->addChild('styleUrl', '#style_note_highlight');

        $styleNormalNote = $document->addChild('Style');
        $styleNormalNote->addAttribute('id', 'style_note_normal');
        $iconStyleNormal = $styleNormalNote->addChild('IconStyle');
        $iconStyleNormal->addChild('scale', '1.1');
        $iconNormal = $iconStyleNormal->addChild('Icon');
        $iconNormal->addChild('href', 'http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png');
        $hotSpotNormal = $iconStyleNormal->addChild('hotSpot');
        $hotSpotNormal->addAttribute('x', '20');
        $hotSpotNormal->addAttribute('y', '2');
        $hotSpotNormal->addAttribute('xunits', 'pixels');
        $hotSpotNormal->addAttribute('yunits', 'pixels');

        $styleHighlightNote = $document->addChild('Style');
        $styleHighlightNote->addAttribute('id', 'style_note_highlight');
        $iconStyleHighlight = $styleHighlightNote->addChild('IconStyle');
        $iconStyleHighlight->addChild('scale', '1.3');
        $iconHighlight = $iconStyleHighlight->addChild('Icon');
        $iconHighlight->addChild('href', 'http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png');
        $hotSpotHighlight = $iconStyleHighlight->addChild('hotSpot');
        $hotSpotHighlight->addAttribute('x', '20');
        $hotSpotHighlight->addAttribute('y', '2');
        $hotSpotHighlight->addAttribute('xunits', 'pixels');
        $hotSpotHighlight->addAttribute('yunits', 'pixels');

        // Style and StyleMap for Routes (Lines)
        $styleMapRoute = $document->addChild('StyleMap');
        $styleMapRoute->addAttribute('id', 'style_route');
        $pairNormalRoute = $styleMapRoute->addChild('Pair');
        $pairNormalRoute->addChild('key', 'normal');
        $pairNormalRoute->addChild('styleUrl', '#style_route_normal');
        $pairHighlightRoute = $styleMapRoute->addChild('Pair');
        $pairHighlightRoute->addChild('key', 'highlight');
        $pairHighlightRoute->addChild('styleUrl', '#style_route_highlight');

        $styleNormalRoute = $document->addChild('Style');
        $styleNormalRoute->addAttribute('id', 'style_route_normal');
        $lineStyleNormal = $styleNormalRoute->addChild('LineStyle');
        $lineStyleNormal->addChild('color', 'ff05bcfb'); // AABBGGRR for #FBBC05
        $lineStyleNormal->addChild('width', '4');

        $styleHighlightRoute = $document->addChild('Style');
        $styleHighlightRoute->addAttribute('id', 'style_route_highlight');
        $lineStyleHighlight = $styleHighlightRoute->addChild('LineStyle');
        $lineStyleHighlight->addChild('color', 'ff05bcfb');
        $lineStyleHighlight->addChild('width', '6');
    }

    private function addNotePlacemark(\SimpleXMLElement $document, array $note): void
    {
        $placemark = $document->addChild('Placemark');
        $placemark->addChild('name', htmlspecialchars($note['title'] ?? 'Tanpa Judul'));
        $placemark->addChild('description', htmlspecialchars($note['description'] ?? 'Tidak ada deskripsi.'));
        $placemark->addChild('styleUrl', '#style_note');
        $point = $placemark->addChild('Point');
        $point->addChild('coordinates', "{$note['longitude']},{$note['latitude']},0");
    }

    private function addRoutePlacemark(\SimpleXMLElement $document, array $route): void
    {
        if (empty($route['route']) || !is_array($route['route'])) return;

        $placemark = $document->addChild('Placemark');
        $placemark->addChild('name', htmlspecialchars($route['title'] ?? 'Tanpa Judul'));
        $placemark->addChild('description', htmlspecialchars($route['description'] ?? 'Tidak ada deskripsi.'));
        $placemark->addChild('styleUrl', '#style_route');
        $line = $placemark->addChild('LineString');
        $line->addChild('tessellate', '1');

        $coordinatesString = collect($route['route'])
            ->map(fn($coord) => "{$coord['longitude']},{$coord['latitude']},0")
            ->implode(' ');

        $line->addChild('coordinates', $coordinatesString);
    }
}
