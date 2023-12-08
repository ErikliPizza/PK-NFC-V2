<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function store(Request $request, Card $card)
    {
        $this->authorize('owner', $card);

        // Check if the user is premium
        $isPremium = $request->user()->premium;
        $maxUploads = $isPremium ? 3 : 1;

        // Check if the user has already uploaded the max number of PDFs for the card
        if ($card->pdfs->count() >= $maxUploads) {
            return redirect()->back()->with('error', __('notifications.max_pdf'));
        }

        // Validate the uploaded file
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:20480',
            'title' => 'required|string|max:30',
        ]);

        // Store the PDF file in Google Cloud Storage and create a record
        $pdf = $request->file('pdf');
        $extension = $pdf->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $storagePath = 'pdfs/' . $filename; // Adjusted the path for GCS
        // Use the 'gcs' disk that you've configured
        Storage::disk('gcs')->put($storagePath, file_get_contents($pdf), 'public');

        $pdfModel = new Pdf([
            'title' => $request->title,
            'path' => $filename
        ]);

        $card->pdfs()->save($pdfModel);

        return redirect()->back()->with('success', __('notifications.card_updated'));
    }

    public function destroy(Pdf $pdf)
    {
        $card = $pdf->card;
        $this->authorize('owner', $card);

        // Delete the pdf record from the pdfs table
        $pdf->delete();

        $pdfPath = 'pdfs/' . $pdf->path;
        // Delete the pdf file on the server
        $disk = Storage::disk('gcs');
        if ($disk->exists($pdfPath)) {
            $disk->delete($pdfPath);
        }
        return redirect()->back()->with('success', __('notifications.card_updated'));
    }
}
