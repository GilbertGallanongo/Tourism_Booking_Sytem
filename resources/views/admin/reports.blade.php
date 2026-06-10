<x-layout>
    <div class="section">
        <h1 class="title">Reports</h1>
        <p class="lead">View and export booking reports by time period.</p>
    </div>

    <div class="card">
        <h2 class="subtitle">Report Filters</h2>
        <form method="GET" action="{{ route('admin.reports.index') }}" style="margin-top: 1.5rem;">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Time Period</label>
                    <select name="period" id="period" onchange="toggleCustomDate()" style="width: 100%; padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div id="customDateContainer" style="display: {{ $period === 'custom' ? 'flex' : 'none' }}; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" style="padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: white;">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" style="padding: 0.75rem; border: 1px solid #3d3d5c; border-radius: 0.5rem; background: #1a1a2e; color: white;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <h2 class="subtitle">Booking Data</h2>
        <div style="overflow-x: auto; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #1a1a2e; color: white;">
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Booking #</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Customer</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Package</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Location</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Start Date</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">End Date</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Guests</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Promo</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Status</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Total Price</th>
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50;">Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr style="border-bottom: 1px solid #3d3d5c;">
                            <td style="padding: 0.75rem;">{{ $booking->booking_number }}</td>
                            <td style="padding: 0.75rem;">{{ $booking->user?->name ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ $booking->package?->name ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ $booking->package?->location ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ optional($booking->tour_start_date)->format('Y-m-d') ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ optional($booking->tour_end_date)->format('Y-m-d') ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ $booking->num_guests }}</td>
                            <td style="padding: 0.75rem;">
                                @if($booking->promoPackage)
                                    <span style="color: #ffc107; font-weight: 600;">{{ $booking->promoPackage->name }} ({{ $booking->promoPackage->discount_percentage }}%)</span>
                                @else
                                    <span style="color: #8890a8;">-</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 600; 
                                    @if($booking->status === 'approved') background: #28a745; color: white;
                                    @elseif($booking->status === 'pending') background: #ffc107; color: black;
                                    @elseif($booking->status === 'cancelled') background: #dc3545; color: white;
                                    @elseif($booking->status === 'declined') background: #6c757d; color: white;
                                    @else background: #17a2b8; color: white; @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">PHP {{ number_format($booking->total_price, 2) }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 600;
                                    @if($booking->payment?->status === 'paid') background: #28a745; color: white;
                                    @elseif($booking->payment?->status === 'pending') background: #ffc107; color: black;
                                    @else background: #6c757d; color: white; @endif">
                                    {{ ucfirst($booking->payment?->status ?? 'unpaid') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding: 2rem; text-align: center; color: #8890a8;">
                                No booking data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    {{ $bookings->links() }}
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.8rem; color: #8890a8;">Export:</span>
                    <button onclick="downloadReport('csv')" class="btn btn-primary" id="csvBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">CSV</button>
                    <button onclick="downloadReport('xlsx')" class="btn btn-primary" id="xlsxBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">XLSX</button>
                    <button onclick="downloadReport('pdf')" class="btn btn-primary" id="pdfBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">PDF</button>
                </div>
            </div>
        @else
            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.8rem; color: #8890a8;">Export:</span>
                <button onclick="downloadReport('csv')" class="btn btn-primary" id="csvBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">CSV</button>
                <button onclick="downloadReport('xlsx')" class="btn btn-primary" id="xlsxBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">XLSX</button>
                <button onclick="downloadReport('pdf')" class="btn btn-primary" id="pdfBtn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">PDF</button>
            </div>
        @endif
    </div>

    <script>
        function toggleCustomDate() {
            const period = document.getElementById('period').value;
            const customContainer = document.getElementById('customDateContainer');
            customContainer.style.display = period === 'custom' ? 'flex' : 'none';
        }

        function downloadReport(format) {
            const btn = document.getElementById(format + 'Btn');
            const originalText = btn.textContent;

            btn.disabled = true;
            btn.textContent = '...';

            // Get current filter parameters
            const period = document.getElementById('period').value;
            const startDate = document.querySelector('input[name="start_date"]')?.value || '';
            const endDate = document.querySelector('input[name="end_date"]')?.value || '';

            let url = '{{ route('admin.reports.bookings', ['format' => 'FORMAT']) }}'.replace('FORMAT', format);
            const params = [];
            if (period !== 'all') {
                params.push('period=' + encodeURIComponent(period));
            }
            if (startDate) {
                params.push('start_date=' + encodeURIComponent(startDate));
            }
            if (endDate) {
                params.push('end_date=' + encodeURIComponent(endDate));
            }
            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            // Create hidden link and trigger download
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Re-enable button after short delay
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            }, 1000);
        }
    </script>

</x-layout>
