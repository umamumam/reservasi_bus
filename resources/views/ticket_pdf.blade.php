<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket HDEX Trans - {{ $booking->booking_code }}</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 0; padding: 20px; }
        .ticket { border: 2px solid #eee; border-radius: 10px; padding: 30px; position: relative; max-width: 700px; margin: 0 auto; }
        .header { border-bottom: 2px dashed #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; }
        .logo span { color: #d4af37; }
        .booking-code { float: right; text-align: right; }
        .booking-code p { margin: 0; font-size: 10px; color: #999; text-transform: uppercase; }
        .booking-code h2 { margin: 0; color: #e11d48; font-size: 18px; }
        .clear { clear: both; }
        .info-grid { width: 100%; margin-top: 20px; }
        .info-item { margin-bottom: 20px; vertical-align: top; }
        .label { font-size: 10px; color: #999; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .value { font-size: 16px; font-weight: bold; }
        .status { margin-top: 30px; text-align: center; }
        .badge { background: #22c55e; color: white; padding: 8px 20px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }
        .footer { margin-top: 40px; font-size: 10px; color: #999; border-top: 1px solid #eee; pt: 10px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="booking-code">
                <p>Kode Booking</p>
                <h2>#{{ $booking->booking_code }}</h2>
            </div>
            <div class="logo">HDEX<span>TRANS</span></div>
            <div class="clear"></div>
        </div>

        <table class="info-grid">
            <tr>
                <td colspan="2" class="info-item">
                    <div class="label">Penumpang</div>
                    <div class="value">{{ $booking->user->name }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-item" width="50%">
                    <div class="label">Asal</div>
                    <div class="value">{{ $booking->schedule->busRoute->originCity->name }}</div>
                    <div style="font-size: 12px; color: #666;">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('l, d F Y') }}</div>
                    <div style="font-size: 16px; color: #e11d48; font-weight: bold;">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') }}</div>
                </td>
                <td class="info-item" width="50%">
                    <div class="label">Tujuan</div>
                    <div class="value">{{ $booking->schedule->busRoute->destinationCity->name }}</div>
                    <div style="font-size: 12px; color: #666;">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('l, d F Y') }}</div>
                    <div style="font-size: 16px; color: #333; font-weight: bold;">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('H:i') }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-item">
                    <div class="label">Bus & Kelas</div>
                    <div class="value">{{ $booking->schedule->bus->name }}</div>
                    <div style="font-size: 11px; color: #666;">{{ $booking->schedule->bus->layout_type == '2-2' ? 'Executive 2-2' : 'Sleeper Class' }}</div>
                </td>
                <td class="info-item">
                    <div class="label">Nomor Kursi</div>
                    <div class="value">
                        @foreach($booking->seats as $seat)
                            {{ $seat->seat_number }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>

        <div class="status">
            <div class="label">Status Tiket</div>
            <div class="badge">LUNAS</div>
        </div>

        <div class="footer">
            <p>Syarat & Ketentuan:</p>
            <ul>
                <li>Tiket ini adalah bukti pembayaran yang sah.</li>
                <li>Harap tiba 30 menit sebelum jadwal keberangkatan.</li>
                <li>Tunjukkan tiket ini saat boarding.</li>
            </ul>
        </div>
    </div>
</body>
</html>
