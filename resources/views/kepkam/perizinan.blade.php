@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h3 class="text-2xl font-bold text-[#1B2559]">Perizinan Santri</h3>
                <p class="text-[#A3AED0] text-sm mt-1">Kelola izin pulang dan keluar santri</p>
            </div>
            <button type="button" class="btn btn-primary shadow-brand flex items-center gap-2" data-toggle="modal"
                data-target="#add">
                <i class="fa fa-plus"></i>
                <span>Buat Izin</span>
            </button>
        </div>

        <!-- Main Content -->
        <div class="card p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#1B2559]">Riwayat Perizinan</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">Nama Santri</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">Jenis</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">Alasan</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">Durasi Izin</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50">Status</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] bg-gray-50/50 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perizinan as $item)
                            <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <td class="px-6 py-4 text-sm font-bold text-[#2B3674]">
                                    {{ $item->santri->nama ?? "-" }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->jenis == 'P')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-600">Pulang</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-600">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-[#2B3674]">
                                    {{ $item->alasanizin->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-[#A3AED0]">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[#2B3674] font-medium">{{ date('d/m/Y', strtotime($item->berangkat)) }}</span>
                                        <span class="text-xs">s.d {{ date('d/m/Y', strtotime($item->es_kembali)) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->status == 5 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $item->statusizin->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status == 5)
                                        <button
                                            class="btn bg-[#4318FF] text-white hover:bg-[#3311CC] py-2 px-3 text-xs lapor shadow-brand"
                                            data-nis="{{ $item->santri->nis }}">
                                            Lapor Kembali
                                        </button>
                                    @else
                                        <span class="text-gray-300 text-xs italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-8 text-gray-400 text-sm">
                                    Belum ada data perizinan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Buat Izin -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content !rounded-[20px] !border-none !shadow-2xl">
                <div class="modal-header border-b border-gray-100 p-6">
                    <h5 class="modal-title text-xl font-bold text-[#1B2559]">Buat Izin Baru</h5>
                    <button type="button" class="close opacity-50 hover:opacity-100 transition-opacity" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <form action="/perizinan" method="post" id="formPerizinan">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Jenis Izin -->
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Jenis Izin</label>
                                <div class="relative">
                                    <select
                                        class="form-control appearance-none w-full bg-gray-50 border border-gray-200 text-[#2B3674] text-sm rounded-lg focus:ring-[#4318FF] focus:border-[#4318FF] block p-2.5 pr-8"
                                        name="jenis" required>
                                        <option value="" disabled selected>Pilih Jenis Izin...</option>
                                        <option value="P">Pulang</option>
                                        <option value="K">Keluar</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#A3AED0]">
                                        <i class="fa fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Alasan -->
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Alasan</label>
                                <div class="relative">
                                    <select
                                        class="form-control appearance-none w-full bg-gray-50 border border-gray-200 text-[#2B3674] text-sm rounded-lg focus:ring-[#4318FF] focus:border-[#4318FF] block p-2.5 pr-8"
                                        name="alasan" required>
                                        <option value="" disabled selected>Pilih Alasan...</option>
                                        @foreach ($alasan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#A3AED0]">
                                        <i class="fa fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal Kembali -->
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Estimasi Kembali</label>
                                <input type="date"
                                    class="bg-gray-50 border border-gray-200 text-[#2B3674] text-sm rounded-lg focus:ring-[#4318FF] focus:border-[#4318FF] block w-full p-2.5"
                                    name="kembali" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-[#1B2559] mb-2">Pilih Santri</label>
                            <!-- Search Box -->
                            <div class="relative mb-3">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchSantri"
                                    class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#4318FF] focus:border-[#4318FF] block w-full pl-10 p-3 shadow-sm"
                                    placeholder="Cari nama santri...">
                            </div>

                            <!-- Santri List (Single Select) -->
                            <div
                                class="max-h-60 overflow-y-auto border border-gray-100 rounded-xl bg-white shadow-inner p-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="santriContainer">
                                    @foreach($santri as $s)
                                        <label
                                            class="santri-item flex items-center p-3 rounded-lg border border-transparent hover:bg-gray-50 cursor-pointer transition-all peer-checked:border-[#4318FF] peer-checked:bg-blue-50/50">
                                            <input type="radio" name="nis" value="{{ $s->nis }}"
                                                class="w-4 h-4 text-[#4318FF] bg-gray-100 border-gray-300 focus:ring-[#4318FF] mr-3"
                                                required>
                                            <span class="text-sm font-medium text-gray-900 santri-name">{{ $s->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-t border-gray-100 mt-6 pt-6 px-0 pb-0 flex justify-end">
                            <button type="button" class="btn bg-gray-100 text-[#2B3674] hover:bg-gray-200"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary ml-2">Simpan Izin</button>
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

            // Add visual toggle state to parent label when radio changes
            const radios = document.querySelectorAll('input[name="nis"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Reset all labels
                    radios.forEach(r => {
                        const l = r.closest('label');
                        l.classList.remove('border-[#4318FF]', 'bg-indigo-50');
                        l.classList.add('border-transparent');
                    });

                    // Set active label
                    if (this.checked) {
                        const label = this.closest('label');
                        label.classList.add('border-[#4318FF]', 'bg-indigo-50');
                        label.classList.remove('border-transparent');
                    }
                });
            });

            // Lapor Kembali AJAX logic (Previous implementation adapted)
            $(document).on('click', '.lapor', function () {
                if (!confirm('Apakah santri ini benar-benar sudah kembali?')) return;

                let nis = $(this).data('nis');
                $.ajax({
                    url: `/lapor/${nis}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: 6 // Status Kembali/Selesai
                    },
                    success: function (data) {
                        window.location.reload();
                    },
                    error: function (xhr) {
                        alert("Terjadi kesalahan saat memperbarui status.");
                    }
                })
            });
        });
    </script>
@endsection