@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <h2 class="text-2xl font-bold mb-6 text-[#1B2559]">Monitoring Absensi</h2>
        <!-- Stats Cards -->
        <div class="flex gap-4 mb-6 overflow-x-auto pb-2">
            <div
                class="bg-white p-4 rounded-xl border border-gray-100 hover:border-blue-200 transition-all flex-shrink-0 min-w-[200px]">
                <div class="flex items-center gap-3">
                    <i class="fa fa-building text-[#4318FF] text-lg"></i>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5 whitespace-nowrap">Asrama</p>
                        <h3 class="text-xl font-bold text-[#1B2559]">-</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl border border-gray-100 hover:border-blue-200 transition-all flex-shrink-0 min-w-[200px]">
                <div class="flex items-center gap-3">
                    <i class="fa fa-users text-[#4318FF] text-lg"></i>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5 whitespace-nowrap">Total Santri Putra</p>
                        <h3 class="text-xl font-bold text-[#1B2559]">-</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl border border-gray-100 hover:border-blue-200 transition-all flex-shrink-0 min-w-[200px]">
                <div class="flex items-center gap-3">
                    <i class="fa fa-user-tie text-[#4318FF] text-lg"></i>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5 whitespace-nowrap">Total Pengurus</p>
                        <h3 class="text-xl font-bold text-[#1B2559]">-</h3>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl border border-gray-100 hover:border-blue-200 transition-all flex-shrink-0 min-w-[200px]">
                <div class="flex items-center gap-3">
                    <i class="fa fa-key text-[#4318FF] text-lg"></i>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5 whitespace-nowrap">Total Kepala Kamar</p>
                        <h3 class="text-xl font-bold text-[#1B2559]">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <!-- Date Filter -->
        <!-- Date Filter -->
        <div id="date-container"
            class="bg-white px-4 py-3 sm:py-2 rounded-xl shadow-sm mb-6 w-full sm:w-fit flex items-center justify-between sm:justify-start gap-4 sm:gap-3 border border-gray-100 cursor-pointer hover:border-blue-200 hover:shadow-md transition-all group relative">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="text-[#4318FF] flex-shrink-0">
                    <i class="fa fa-calendar-alt"></i>
                </div>
                <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

                <!-- Visible Text -->
                <input type="text" id="tanggal-display"
                    class="bg-transparent border-none text-[#1B2559] font-bold text-sm sm:text-base p-0 flex-1 sm:w-56 pointer-events-none focus:ring-0 text-left"
                    placeholder="Pilih Tanggal" readonly>
            </div>

            <i
                class="fa fa-chevron-down text-[#A3AED0] text-xs transition-transform group-hover:translate-y-0.5 pointer-events-none flex-shrink-0"></i>

            <!-- Anchor for Datepicker (Invisible 1px line at bottom) -->
            <input type="text" id="tanggal" class="absolute bottom-0 left-0 w-full h-px opacity-0 p-0 border-0 -z-10"
                readonly>
        </div>

        <!-- Activity Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $activityList = [
                    ['id' => 'subuh', 'title' => 'Jamaah Subuh', 'data' => $subuh],
                    ['id' => 'dhuhur', 'title' => 'Jamaah Dhuhur', 'data' => $dhuhur],
                    ['id' => 'waqiah', 'title' => 'Waqiah', 'data' => $waqiah],
                    ['id' => 'ashar', 'title' => 'Jamaah Ashar', 'data' => $ashar],
                    ['id' => 'maghrib', 'title' => 'Jamaah Maghrib', 'data' => $maghrib],
                    ['id' => 'isya', 'title' => 'Jamaah Isya', 'data' => $isya],
                    ['id' => 'ngasore', 'title' => 'Ngaji Sore', 'data' => $ngasore],
                    ['id' => 'ngamalam', 'title' => 'Ngaji Malam', 'data' => $ngamalam],
                ];
            @endphp

            @foreach($activityList as $activity)
                <div
                    class="card h-fit transition-all duration-300 hover:shadow-lg hover:border-blue-100 border border-transparent group">
                    <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                        <h3 class="text-lg font-bold text-[#1B2559] group-hover:text-[#4318FF] transition-colors">
                            {{ $activity['title'] }}
                        </h3>
                        <i
                            class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300 group-hover:text-[#4318FF]"></i>
                    </div>
                    <div class="overflow-x-auto hidden agenda-content">
                        <table class="table w-full">
                            <thead>
                                <tr class="text-left text-sm text-[#A3AED0]">
                                    <th class="pb-2">Kepala Kamar</th>
                                    <th class="pb-2">Jml Santri</th>
                                    <th class="pb-2">Hadir</th>
                                    <th class="pb-2">Sakit</th>
                                    <th class="pb-2">Izin</th>
                                    <th class="pb-2">Alfa</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-medium text-[#2B3674]" id="tbody-{{ $activity['id'] }}">
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>


    </div>
@endsection

@section('script')
    <script>
        const kepkams = @json($kepkams);
        const activitiesData = {
            subuh: @json($subuh),
            dhuhur: @json($dhuhur),
            waqiah: @json($waqiah),
            ashar: @json($ashar),
            maghrib: @json($maghrib),
            isya: @json($isya),
            ngasore: @json($ngasore),
            ngamalam: @json($ngamalam)
        };

        function formatDate(date) {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        function toggleAgenda(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('i');

            content.classList.toggle('hidden');
            if (content.classList.contains('hidden')) {
                icon.classList.remove('rotate-180');
            } else {
                icon.classList.add('rotate-180');
            }
        }

        function renderTables(selectedDate) {
            if (!selectedDate) return;

            let dbDate = selectedDate;

            // Check formatted dd/mm/yyyy
            if (selectedDate.includes('/')) {
                const parts = selectedDate.split('/');
                if (parts.length === 3) {
                    dbDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }



            Object.keys(activitiesData).forEach(key => {
                const tbody = document.getElementById(`tbody-${key}`);
                tbody.innerHTML = '';

                const data = activitiesData[key];

                kepkams.forEach(kepkam => {
                    // Robust Comparison using NIS (Unique ID)
                    // Both arrays now contain NIS from the 'pengurus' table
                    const record = data.find(item => {
                        const itemDate = item.tanggal ? item.tanggal.substring(0, 10) : ''; // YYYY-MM-DD
                        return String(item.nis) === String(kepkam.nis) && itemDate === dbDate;
                    });

                    let rowHtml = '';
                    if (record) {
                        rowHtml = `
                                                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                                                        <td class="py-3">${kepkam.nama}</td>
                                                                        <td class="py-3 font-semibold text-gray-600 pl-4">${kepkam.jml_santri}</td>
                                                                        <td class="py-3 text-green-500">${record.hadir}</td>
                                                                        <td class="py-3 text-yellow-500">${record.sakit}</td>
                                                                        <td class="py-3 text-blue-500">${record.izin}</td>
                                                                        <td class="py-3 text-red-500">${record.alfa}</td>
                                                                    </tr>
                                                                `;
                    } else {
                        rowHtml = `
                                                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors bg-red-50/50">
                                                                        <td class="py-3">${kepkam.nama}</td>
                                                                        <td class="py-3 font-semibold text-gray-600 pl-4">${kepkam.jml_santri}</td>
                                                                        <td colspan="4" class="py-3 text-center text-red-400 italic text-xs">
                                                                            Belum mengisi absensi
                                                                        </td>
                                                                    </tr>
                                                                `;
                    }
                    tbody.insertAdjacentHTML('beforeend', rowHtml);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Define Indonesian Locale
            $.fn.datepicker.dates['id'] = {
                days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
                daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                daysMin: ["Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
                months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                today: "Hari Ini",
                clear: "Kosongkan",
                format: "dd/mm/yyyy",
                titleFormat: "MM yyyy",
                weekStart: 1
            };

            const displayInput = $('#tanggal-display');
            const dateInput = $('#tanggal'); // The invisible overlay

            // Initial Date
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const dbDate = `${yyyy}-${mm}-${dd}`;

            // Format for Display
            const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
            displayInput.val(today.toLocaleDateString('id-ID', options));

            // Render Initial
            renderTables(dbDate);

            // Initialize Datepicker on Input (Popup Mode)
            dateInput.datepicker({
                language: 'id',
                autoclose: true,
                format: 'dd/mm/yyyy', // Internal format
                todayHighlight: true,
                orientation: "bottom auto",
                container: '#date-container' // Fix scroll issue
            }).on('changeDate', function (e) {
                const d = e.date;
                // Update Display Text
                displayInput.val(d.toLocaleDateString('id-ID', options));

                // Update Logic
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const isoDate = `${y}-${m}-${day}`;

                renderTables(isoDate);
            });

            // Set initial internal date so popup fits
            dateInput.datepicker('setDate', today);

            // Programmatic Trigger to ensure popup opens
            $('#date-container').on('click', function () {
                dateInput.datepicker('show');
            });
        });
    </script>
@endsection