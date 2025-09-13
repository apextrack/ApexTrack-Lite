<?php
include 'layout/header.php'; 

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php?error=' . urlencode('Sesi Anda telah berakhir. Silakan login kembali.'));
    exit();
}

?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="summary-cards">
        <div class="card flex flex-col items-start bg-blue-100 text-blue-800 border-l-4 border-blue-500 p-4 shadow-md summary-clicks">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-chart-line w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Clicks Today</h3>
            </div>
            <div class="text-2xl font-bold" id="clicks-today">0</div>
        </div>
        
        <div class="card flex flex-col items-start bg-purple-100 text-purple-800 border-l-4 border-purple-500 p-4 shadow-md summary-conversions">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-user-check w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Conversions Today</h3>
            </div>
            <div class="text-2xl font-bold" id="conversions-today">0</div>
        </div>
        
        <div class="card flex flex-col items-start bg-orange-100 text-orange-800 border-l-4 border-orange-500 p-4 shadow-md summary-revenue">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-dollar-sign w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Revenue Today</h3>
            </div>
            <div class="text-2xl font-bold" id="revenue-today">$0.00</div>
        </div>
        
        <div class="card flex flex-col items-start bg-green-100 text-green-800 border-l-4 border-green-500 p-4 shadow-md summary-epc">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-arrow-trend-up w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">EPC Today</h3>
            </div>
            <div class="text-2xl font-bold" id="epc-today">$0.00</div>
        </div>
    </div>

    <div class="card p-6 shadow-md bg-white mt-6">
        <div class="flex items-center text-gray-900 mb-4">
            <h3 class="text-xl font-semibold">Recent Clicks</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Offer Name</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">IP Address</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Country</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">OS</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Device</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Redirect Type</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Date</th>
                    </tr>
                </thead>
                <tbody id="recent-clicks-table-body">
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-4 border border-gray-300">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="card p-6 shadow-md bg-white">
            <div class="flex items-center text-gray-900 mb-4">
                <h3 class="text-xl font-semibold">Performance Report</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Hits</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Conversions</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Revenue</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">CR (%)</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">EPC ($)</th>
                        </tr>
                    </thead>
                    <tbody id="performance-report-table-body">
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4 border border-gray-300">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card p-6 shadow-md bg-white">
            <div class="flex items-center text-gray-900 mb-4">
                <h3 class="text-xl font-semibold">Recent Conversions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Revenue</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Country</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Device</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">IP Address</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Date</th>
                        </tr>
                    </thead>
                    <tbody id="recent-conversions-table-body">
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4 border border-gray-300">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const fetchDataAndRender = async () => {
        try {
            const response = await fetch('api/dashboard.php'); 
            const data = await response.json();

            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = 'login.php?error=' + encodeURIComponent(data.error);
                }
                throw new Error(data.error || 'Failed to fetch data.');
            }

            document.getElementById('clicks-today').textContent = new Intl.NumberFormat().format(data.summary.today_clicks || 0);
            document.getElementById('conversions-today').textContent = new Intl.NumberFormat().format(data.summary.today_leads || 0);
            document.getElementById('revenue-today').textContent = '$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.summary.today_revenue || 0);
            document.getElementById('epc-today').textContent = '$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.summary.today_epc || 0);

            const renderTable = (tableBodyId, tableData, columns, emptyMessage) => {
                const tableBody = document.getElementById(tableBodyId);
                tableBody.innerHTML = ''; 

                if (tableData && tableData.length > 0) {
                    tableData.forEach(row => {
                        const tr = document.createElement('tr');
                        columns.forEach(col => {
                            const td = document.createElement('td');
                            td.className = 'py-3 px-4 text-sm text-gray-700 border border-gray-300';
                            let content = row[col.key] ?? 'N/A';
                            
                            if (col.key === 'country_code') {
                                const countryCode = (row.country_code ?? 'us').toLowerCase();
                                const flagCode = countryCode === 'uk' ? 'gb' : countryCode;
                                td.innerHTML = `<div class="flex items-center"><span class="flag-icon flag-icon-${flagCode} mr-2"></span>${row.country_code ?? 'N/A'}</div>`;
                            } else if (col.key === 'device_type') {
                                let deviceIcon = 'fa-mobile-screen-button';
                                if (row.device_type) {
                                    switch (row.device_type.toLowerCase()) {
                                        case 'desktop': deviceIcon = 'fa-desktop'; break;
                                        case 'tablet': deviceIcon = 'fa-tablet'; break;
                                    }
                                }
                                td.innerHTML = `<div class="flex items-center"><i class="fa-solid ${deviceIcon} w-4 h-4 mr-2"></i>${row.device_type ?? 'N/A'}</div>`;
                            } else if (col.key === 'created_at') {
                                const date = new Date(row.created_at);
                                content = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID');
                                td.textContent = content;
                            } else if (col.key === 'payout' || col.key === 'revenue') {
                                const value = parseFloat(row.payout || row.revenue) || 0; 
                                content = `$${value.toFixed(2)}`;
                                td.textContent = content;
                            } else if (col.key === 'cr') {
                                const value = parseFloat(row.cr) || 0;
                                content = `${value.toFixed(2)}%`;
                                td.textContent = content;
                            } else if (col.key === 'epc') {
                                const value = parseFloat(row.epc) || 0;
                                content = `$${value.toFixed(2)}`;
                                td.textContent = content;
                            } else if (col.key === 'hits' || col.key === 'conversions') {
                                content = new Intl.NumberFormat().format(row[col.key] || 0);
                                td.textContent = content;
                            } else if (col.key === 'sub_id') {
                                td.innerHTML = `<div class="flex items-center"><i class="fa-solid fa-user w-4 h-4 mr-2"></i>${row.sub_id ?? 'N/A'}</div>`;
                            }
                             else {
                                td.textContent = content;
                            }
                            tr.appendChild(td);
                        });
                        tableBody.appendChild(tr);
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="${columns.length}" class="text-center text-gray-500 py-4 border border-gray-300">${emptyMessage}</td></tr>`;
                }
            };
            
            const recentClicksColumns = [
                { key: 'sub_id' },
                { key: 'offer_name' },
                { key: 'ip_address' },
                { key: 'country_code' },
                { key: 'os' },
                { key: 'device_type' },
                { key: 'redirect_type_used' },
                { key: 'created_at' }
            ];
            
            const performanceReportColumns = [
                { key: 'sub_id' },
                { key: 'hits' },
                { key: 'conversions' },
                { key: 'revenue' },
                { key: 'cr' },
                { key: 'epc' }
            ];

            const recentConversionsColumns = [
                { key: 'sub_id' },
                { key: 'payout' },
                { key: 'country_code' },
                { key: 'device_type' },
                { key: 'ip_address' },
                { key: 'created_at' }
            ];

            renderTable('recent-clicks-table-body', data.recent_clicks, recentClicksColumns, 'Tidak ada klik terbaru hari ini.');
            renderTable('performance-report-table-body', data.performance_report, performanceReportColumns, 'Tidak ada data untuk laporan kinerja.');
            renderTable('recent-conversions-table-body', data.recent_leads, recentConversionsColumns, 'Tidak ada konversi terbaru hari ini.');

        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    };

    fetchDataAndRender();

    setInterval(fetchDataAndRender, 30000); 
});
</script>

<?php
include 'layout/footer.php'; 
?>