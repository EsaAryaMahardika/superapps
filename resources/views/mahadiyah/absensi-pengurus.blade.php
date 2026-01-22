@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-[#1B2559]">Absensi Pengurus</h2>
            <a href="/mahadiyah/absen-pengurus"
                class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40">
                <i class="fa fa-plus mr-2"></i>Buat Absensi
            </a>
        </div>

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

            <!-- Anchor for Datepicker -->
            <input type="text" id="tanggal" class="absolute bottom-0 left-0 w-full h-px opacity-0 p-0 border-0 -z-10"
                readonly>
        </div>

        <!-- Activity Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $activityList = [
                    ['id' => 'yasinan', 'title' => 'Absensi Yasinan', 'data' => $yasinan],
                    ['id' => 'bandongan', 'title' => 'Absensi Bandongan', 'data' => $bandongan],
                    ['id' => 'wirid', 'title' => 'Absensi Wirid', 'data' => $wirid],
                ];
            @endphp

            @foreach($activityList as $activity)
                <div
                    class="card h-fit transition-all duration-300 hover:shadow-lg hover:border-blue-100 border border-transparent">
                    <h3 class="text-lg font-bold text-[#1B2559] mb-4">{{ $activity['title'] }}</h3>

                    <!-- Statistics Summary (Clickable) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4" id="stats-{{ $activity['id'] }}">
                        <div class="bg-green-50 border border-green-100 rounded-lg p-2.5 cursor-pointer hover:bg-green-100 transition-colors"
                            onclick="showStatusList('{{ $activity['id'] }}', 'H')">
                            <p class="text-[10px] text-green-600 font-medium mb-0.5">Hadir</p>
                            <p class="text-xl font-bold text-green-700 leading-none" id="count-hadir-{{ $activity['id'] }}">0
                            </p>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-2.5 cursor-pointer hover:bg-yellow-100 transition-colors"
                            onclick="showStatusList('{{ $activity['id'] }}', 'S')">
                            <p class="text-[10px] text-yellow-600 font-medium mb-0.5">Sakit</p>
                            <p class="text-xl font-bold text-yellow-700 leading-none" id="count-sakit-{{ $activity['id'] }}">0
                            </p>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-2.5 cursor-pointer hover:bg-blue-100 transition-colors"
                            onclick="showStatusList('{{ $activity['id'] }}', 'I')">
                            <p class="text-[10px] text-blue-600 font-medium mb-0.5">Izin</p>
                            <p class="text-xl font-bold text-blue-700 leading-none" id="count-izin-{{ $activity['id'] }}">0</p>
                        </div>
                        <div class="bg-red-50 border border-red-100 rounded-lg p-2.5 cursor-pointer hover:bg-red-100 transition-colors"
                            onclick="showStatusList('{{ $activity['id'] }}', 'A')">
                            <p class="text-[10px] text-red-600 font-medium mb-0.5">Alfa</p>
                            <p class="text-xl font-bold text-red-700 leading-none" id="count-alfa-{{ $activity['id'] }}">0</p>
                        </div>
                    </div>

                    <!-- Filtered Name List (Hidden by default) -->
                    <div id="name-list-{{ $activity['id'] }}" class="hidden mb-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-sm font-semibold text-[#1B2559]" id="list-title-{{ $activity['id'] }}">Daftar
                                    Nama</h4>
                                <button onclick="hideStatusList('{{ $activity['id'] }}')"
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div id="filtered-names-{{ $activity['id'] }}" class="space-y-2 max-h-64 overflow-y-auto">
                                <!-- JS Populated -->
                            </div>
                        </div>
                    </div>


                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script>
        const activitiesData = {
            yasinan: @json($yasinan),
            bandongan: @json($bandongan),
            wirid: @json($wirid)
        };

        const statusLabel = {
            'H': 'Hadir',
            'S': 'Sakit',
            'I': 'Izin',
            'A': 'Alpa'
        };

        const statusColor = {
            'H': 'text-green-500',
            'S': 'text-yellow-500',
            'I': 'text-blue-500',
            'A': 'text-red-500'
        };

        let currentSelectedDate = null;

        function showStatusList(activityId, status) {
            const nameListDiv = document.getElementById(`name-list-${activityId}`);
            const filteredNamesDiv = document.getElementById(`filtered-names-${activityId}`);
            const listTitle = document.getElementById(`list-title-${activityId}`);

            // Update title
            const statusNames = { 'H': 'Hadir', 'S': 'Sakit', 'I': 'Izin', 'A': 'Alfa' };
            listTitle.textContent = `Daftar ${statusNames[status]}`;

            // Filter data by status and date
            const data = activitiesData[activityId];
            filteredNamesDiv.innerHTML = '';

            let count = 0;
            data.forEach(item => {
                let itemDate = item.tanggal;
                if (itemDate && itemDate.includes('-')) {
                    itemDate = itemDate.replace(/-/g, '/');
                }

                if (itemDate === currentSelectedDate && item.status === status) {
                    count++;
                    const nameHtml = `
                                <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-[#F4F7FE] flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-user text-[#4318FF] text-xs"></i>
                                    </div>
                                    <p class="text-sm font-medium text-[#1B2559]">${item.pengurus?.nama || '-'}</p>
                                </div>
                            `;
                    filteredNamesDiv.insertAdjacentHTML('beforeend', nameHtml);
                }
            });

            if (count === 0) {
                filteredNamesDiv.innerHTML = '<p class="text-sm text-gray-400 italic text-center py-4">Tidak ada data</p>';
            }

            // Show the list
            nameListDiv.classList.remove('hidden');
        }

        function hideStatusList(activityId) {
            const nameListDiv = document.getElementById(`name-list-${activityId}`);
            nameListDiv.classList.add('hidden');
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

            currentSelectedDate = selectedDate; // Store for badge clicks

            Object.keys(activitiesData).forEach(key => {
                const data = activitiesData[key];

                // Calculate statistics
                const stats = { H: 0, S: 0, I: 0, A: 0 };
                data.forEach(item => {
                    let itemDate = item.tanggal;
                    if (itemDate && itemDate.includes('-')) {
                        itemDate = itemDate.replace(/-/g, '/');
                    }
                    if (itemDate === selectedDate && item.status) {
                        stats[item.status] = (stats[item.status] || 0) + 1;
                    }
                });

                // Update stat counters
                document.getElementById(`count-hadir-${key}`).textContent = stats.H || 0;
                document.getElementById(`count-sakit-${key}`).textContent = stats.S || 0;
                document.getElementById(`count-izin-${key}`).textContent = stats.I || 0;
                document.getElementById(`count-alfa-${key}`).textContent = stats.A || 0;
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
            const dateInput = $('#tanggal');

            // Initial Date
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const dbDate = `${dd}/${mm}/${yyyy}`;

            // Format for Display
            const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
            displayInput.val(today.toLocaleDateString('id-ID', options));

            // Render Initial
            renderTables(dbDate);

            // Initialize Datepicker
            dateInput.datepicker({
                language: 'id',
                autoclose: true,
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                orientation: "bottom auto",
                container: '#date-container'
            }).on('changeDate', function (e) {
                const d = e.date;
                displayInput.val(d.toLocaleDateString('id-ID', options));

                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const formattedDate = `${day}/${m}/${y}`;

                renderTables(formattedDate);
            });

            dateInput.datepicker('setDate', today);

            // Programmatic Trigger
            $('#date-container').on('click', function () {
                dateInput.datepicker('show');
            });
        });
    </script>
@endsection