<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function downloadPdf(Booking $booking)
    {
        // Load relationships needed for the ticket
        $booking->load(['user', 'schedule.busRoute.originCity', 'schedule.busRoute.destinationCity', 'schedule.bus', 'seats']);

        $pdf = Pdf::loadView('ticket_pdf', compact('booking'));
        
        return $pdf->download('Tiket-HDEX-' . $booking->booking_code . '.pdf');
    }
}
