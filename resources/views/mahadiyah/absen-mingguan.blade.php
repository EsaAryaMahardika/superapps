@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-[#1B2559]">Absensi Mingguan</h2>
        </div>

        <!-- Month Selector Card -->
        <div class="card mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#F4F7FE] flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-calendar text-[#4318FF] text-xl"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-[#1B2559] mb-2">Pilih Bulan</label>
                    <select name="bulan" id="bulan"
                        class="w-full sm:w-64 px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-[#1B2559] font-medium">
                        <option value="">Pilih Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-[#1B2559]">Data Pelanggaran per Kepala Kamar</h3>
                <p class="text-sm text-[#A3AED0] mt-1">Rekap jumlah pelanggaran santri berdasarkan Kepala Kamar</p>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="text-left text-sm text-[#A3AED0] border-b border-gray-100">
                            <th class="pb-3 font-semibold sticky left-0 bg-white z-10">Kepala Kamar</th>
                            <th class="pb-3 font-semibold text-center">Tidak Jamaah</th>
                            <th class="pb-3 font-semibold text-center">Ghosob</th>
                            <th class="pb-3 font-semibold text-center">Mencuri</th>
                            <th class="pb-3 font-semibold text-center">Ocol</th>
                            <th class="pb-3 font-semibold text-center">Over Gurau</th>
                            <th class="pb-3 font-semibold text-center">Begadang</th>
                            <th class="pb-3 font-semibold text-center">Tidak Roan</th>
                            <th class="pb-3 font-semibold text-center">Bolos Sekolah</th>
                            <th class="pb-3 font-semibold text-center">Merokok</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 sticky left-0 bg-white">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center">
                                        <i class="fa fa-user text-[#4318FF]"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[#1B2559]">Rizky Wildan Habibi</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 font-bold text-sm">0</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 font-bold text-sm">0</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 font-bold text-sm">0</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                            <td class="py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 font-bold text-sm">10</span>
                            </td>
                        </tr>
                        {{-- Dynamic data will be populated here --}}
                    </tbody>
                </table>
            </div>

            <!-- Empty State (if no data) -->
            <div class="hidden py-12 text-center" id="empty-state">
                <div class="w-16 h-16 rounded-full bg-[#F4F7FE] flex items-center justify-center mx-auto mb-4">
                    <i class="fa fa-inbox text-[#A3AED0] text-2xl"></i>
                </div>
                <p class="text-[#A3AED0] font-medium">Tidak ada data untuk bulan yang dipilih</p>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Month selector change handler
        document.getElementById('bulan').addEventListener('change', function () {
            const selectedMonth = this.value;
            console.log('Selected month:', selectedMonth);
            // TODO: Fetch data based on selected month
        });
    </script>
@endsection