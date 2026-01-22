@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-[#1B2559]">Buat Absensi Pengurus</h2>
            <div class="flex gap-3">
                <a href="/mahadiyah/absensi-pengurus"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                    <i class="fa fa-times mr-2"></i>Batal
                </a>
                <button type="submit" form="formAbsensi"
                    class="px-6 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold transition-all shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40">
                    <i class="fa fa-save mr-2"></i>Simpan Absensi
                </button>
            </div>
        </div>

        <form action="/mahadiyah/absen-pengurus" method="post" id="formAbsensi">
            @csrf

            <!-- Form Header Card -->
            <div class="card mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-[#1B2559] mb-2">
                            <i class="fa fa-clipboard-list mr-2 text-[#4318FF]"></i>Jenis Kegiatan
                        </label>
                        <select
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-[#1B2559] font-medium"
                            name="kegiatan" required>
                            @foreach ($kegiatan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#1B2559] mb-2">
                            <i class="fa fa-calendar mr-2 text-[#4318FF]"></i>Tanggal
                        </label>
                        <div class="relative">
                            <input type="text" id="tanggal-absen" data-provide="datepicker" data-date-autoclose="true"
                                data-date-format="dd-mm-yyyy"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-[#1B2559] font-medium cursor-pointer"
                                name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa fa-calendar-alt text-[#A3AED0]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table Card -->
            <div class="card">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    <h3 class="text-lg font-bold text-[#1B2559]">Daftar Pengurus</h3>

                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <!-- Search Input -->
                        <div class="relative flex-1 sm:flex-initial">
                            <input type="text" id="searchInput" placeholder="Cari nama pengurus..."
                                class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm">
                            <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button type="button" onclick="setAllStatus('H')"
                                class="px-3 py-2 text-xs font-semibold rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors whitespace-nowrap">
                                <i class="fa fa-check mr-1"></i>Semua Hadir
                            </button>
                            <button type="button" onclick="setAllStatus('A')"
                                class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors whitespace-nowrap">
                                <i class="fa fa-times mr-1"></i>Semua Alpa
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0] border-b border-gray-100">
                                <th class="pb-3 font-semibold">Nama Pengurus</th>
                                <th class="pb-3 font-semibold">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($pengurus as $item)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center">
                                                <i class="fa fa-user text-[#4318FF]"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-[#1B2559]">{{ $item->nama }}</p>
                                                <p class="text-xs text-gray-400">NIS: {{ $item->nis }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <select name="pengurus[{{ $item->nis }}]"
                                            class="status-select px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all font-medium min-w-[150px]">
                                            @foreach (['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $kode => $label)
                                                <option value="{{ $kode }}" {{ $kode == 'H' ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        // Initialize datepicker
        $('#tanggal-absen').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        });

        function setAllStatus(status) {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = status;
                updateSelectColor(select); // Update color after changing value
            });
        }

        // Add color coding to selects based on value
        document.querySelectorAll('.status-select').forEach(select => {
            updateSelectColor(select);
            select.addEventListener('change', function () {
                updateSelectColor(this);
            });
        });

        function updateSelectColor(select) {
            const colors = {
                'H': 'text-green-600 border-green-200 bg-green-50',
                'S': 'text-yellow-600 border-yellow-200 bg-yellow-50',
                'I': 'text-blue-600 border-blue-200 bg-blue-50',
                'A': 'text-red-600 border-red-200 bg-red-50'
            };

            // Remove all color classes
            select.className = select.className.replace(/text-\w+-\d+|border-\w+-\d+|bg-\w+-\d+/g, '').trim();

            // Add base classes
            select.className += ' status-select px-4 py-2.5 rounded-lg border focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all font-medium min-w-[150px]';

            // Add color classes
            if (colors[select.value]) {
                select.className += ' ' + colors[select.value];
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = row.querySelector('td p.font-semibold').textContent.toLowerCase();
                const nis = row.querySelector('td p.text-xs').textContent.toLowerCase();

                if (name.includes(searchTerm) || nis.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection