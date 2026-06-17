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
                        @foreach($reportHeaders as $header)
                            <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #4CAF50; min-width: 130px;">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportRows as $row)
                        <tr style="border-bottom: 1px solid #3d3d5c;">
                            @foreach($row as $value)
                                <td style="padding: 0.75rem; vertical-align: top;">{{ $value }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($reportHeaders) }}" style="padding: 2rem; text-align: center; color: #8890a8;">
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
