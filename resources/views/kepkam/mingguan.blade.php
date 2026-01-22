@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h3 class="text-2xl font-bold text-[#1B2559]">Absensi Mingguan</h3>
                <p class="text-[#A3AED0] text-sm mt-1">Rekap pelanggaran santri minggu ini</p>
            </div>
            <button type="button" class="btn btn-primary shadow-brand flex items-center gap-2" data-toggle="modal"
                data-target="#add">
                <i class="fa fa-plus"></i>
                <span>Buat Absensi</span>
            </button>
        </div>

        <!-- Main Content -->
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#1B2559]">Riwayat Pelanggaran</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">
                                Nama Santri
                            </th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">
                                Pelanggaran
                            </th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">
                                Tanggal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mingguan as $item)
                            <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <td class="px-6 py-4 text-sm font-bold text-[#2B3674]">
                                    {{ $item->santri->nama ?? "-" }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $item->larangan->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#A3AED0] font-medium">
                                    {{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center p-8 text-gray-400 text-sm">
                                    Belum ada data pelanggaran minggu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Buat Absensi -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content !rounded-[20px] !border-none !shadow-2xl">
                <div class="modal-header border-b border-gray-100 p-6">
                    <h5 class="modal-title text-xl font-bold text-[#1B2559]">Buat Absensi Mingguan</h5>
                    <button type="button" class="close opacity-50 hover:opacity-100 transition-opacity" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <form action="/kepkam/mingguan" method="post" id="formMingguan">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Jenis Pelanggaran</label>
                                <div class="relative">
                                    <select
                                        class="form-control appearance-none w-full bg-gray-50 border border-gray-200 text-[#2B3674] text-sm rounded-lg focus:ring-[#4318FF] focus:border-[#4318FF] block p-2.5 pr-8"
                                        name="larangan" required>
                                        <option value="" disabled selected>Pilih pelanggaran...</option>
                                        @foreach ($larangan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#A3AED0]">
                                        <i class="fa fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Tanggal</label>
                                <input type="text"
                                    class="bg-gray-50 border border-gray-200 text-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed"
                                    name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-[#1B2559] mb-2">Pilih Santri yang Melanggar</label>
                            <!-- Search Box -->
                            <div class="relative mb-3">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchSantri"
                                    class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#4318FF] focus:border-[#4318FF] block w-full pl-10 p-3 shadow-sm"
                                    placeholder="Cari nama santri...">
                            </div>

                            <!-- Santri List -->
                            <div
                                class="max-h-60 overflow-y-auto border border-gray-100 rounded-xl bg-white shadow-inner p-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="santriContainer">
                                    @foreach($santri as $s)
                                        <label
                                            class="santri-item flex items-center p-3 rounded-lg border border-transparent hover:bg-gray-50 cursor-pointer transition-all peer-checked:border-[#4318FF] peer-checked:bg-blue-50/50">
                                            <input type="checkbox" name="santri[]" value="{{ $s->nis }}"
                                                class="w-4 h-4 text-[#4318FF] bg-gray-100 border-gray-300 rounded focus:ring-[#4318FF] mr-3">
                                            <span class="text-sm font-medium text-gray-900 santri-name">{{ $s->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-t border-gray-100 mt-6 pt-6 px-0 pb-0 flex justify-end">
                            <button type="button" class="btn bg-gray-100 text-[#2B3674] hover:bg-gray-200"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary ml-2">Simpan Absensi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Search Filter
            const searchInput = document.getElementById('searchSantri');
            const santriItems = document.querySelectorAll('.santri-item');

            searchInput.addEventListener('keyup', function () {
                const query = this.value.toLowerCase();

                santriItems.forEach(item => {
                    const name = item.querySelector('.santri-name').textContent.toLowerCase();
                    if (name.includes(query)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });

            // Add visual toggle state to parent label when checkbox changes
            const checkboxes = document.querySelectorAll('input[name="santri[]"]');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const label = this.closest('label');
                    if (this.checked) {
                        label.classList.add('border-[#4318FF]', 'bg-indigo-50');
                        label.classList.remove('border-transparent');
                    } else {
                        label.classList.remove('border-[#4318FF]', 'bg-indigo-50');
                        label.classList.add('border-transparent');
                    }
                });
            });
        });
    </script>
@endsection