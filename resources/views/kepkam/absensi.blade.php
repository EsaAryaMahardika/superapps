@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#1B2559]">Data Absensi</h2>
                <p class="text-[#A3AED0] text-sm mt-1">Kelola dan pantau absensi santri harian</p>
            </div>
            <button class="btn btn-primary shadow-brand flex items-center gap-2" data-toggle="modal" data-target="#add">
                <i class="fa fa-plus"></i>
                <span>Buat Absensi</span>
            </button>
        </div>

        <!-- Pending (Belum Diabsen) Section -->
        @if(count($pending) > 0)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-[#1B2559] mb-4">Aktivitas Hari Ini (Belum Diabsen)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($pending as $item)
                        <div class="card cursor-pointer hover:shadow-lg transition-all group border-l-4 border-[#EE5D50]"
                            onclick="openModalWithActivity('{{ $item['id'] }}')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-[#1B2559] group-hover:text-[#4318FF] transition-colors">
                                        {{ $item['title'] }}
                                    </h4>
                                    <span class="text-xs text-[#EE5D50] font-medium">Belum Diisi</span>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center text-[#A3AED0] group-hover:bg-[#4318FF] group-hover:text-white transition-all">
                                    <i class="fa fa-plus"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Completed (Sudah Diabsen) Section -->
        <div class="mb-4">
            @if(count($completed) > 0)
                <h3 class="text-lg font-bold text-[#1B2559] mb-4">Sudah Diabsen ({{ $today }})</h3>
                <div class="grid grid-cols-1 gap-4">
                    @php
                        $statusBadge = [
                            'H' => 'bg-green-100 text-green-600',
                            'S' => 'bg-yellow-100 text-yellow-600',
                            'I' => 'bg-blue-100 text-blue-600',
                            'A' => 'bg-red-100 text-red-600',
                        ];
                        $statusLabel = [
                            'H' => 'Hadir',
                            'S' => 'Sakit',
                            'I' => 'Izin',
                            'A' => 'Alpa',
                        ];
                    @endphp

                    @foreach($completed as $agenda)
                        <div class="card h-fit transition-all duration-300 border-l-4 border-[#05CD99]">
                            <div class="flex justify-between items-center p-1">
                                <div class="flex items-center gap-3 cursor-pointer flex-grow" onclick="toggleAgenda(this)">
                                    <h3 class="text-lg font-bold text-[#1B2559] m-0">{{ $agenda['title'] }}</h3>
                                    <span class="px-2 py-1 rounded-md bg-[#05CD99]/10 text-[#05CD99] text-xs font-bold">Sudah Diisi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                     <button type="button" 
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                        onclick='editActivity("{{ $agenda['id'] }}", @json($agenda['statuses']))'
                                        title="Edit Absensi">
                                        <i class="fa fa-pencil text-xs"></i>
                                    </button>
                                    <form action="/kepkam/absensi/{{ $agenda['id'] }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus absensi ini? Data yang dihapus tidak dapat dikembalikan.');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors"
                                            title="Hapus Absensi">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    <button type="button" onclick="toggleAgenda(this)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-[#4318FF] transition-all duration-200 group">
                                        <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300 group-hover:translate-y-0.5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-hidden hidden agenda-content mt-4 border-t border-gray-100 pt-4">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr>
                                                <th class="text-xs uppercase text-[#A3AED0] font-bold p-3">Nama Santri</th>
                                                <th class="text-xs uppercase text-[#A3AED0] font-bold p-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm">
                                            @forelse($agenda['data'] as $item)
                                                <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                                    <td class="p-3 text-[#2B3674]">{{ $item->santri->nama ?? "-" }}</td>
                                                    <td class="p-3">
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-bold {{ $statusBadge[$item->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                            {{ $statusLabel[$item->status] ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="p-4 text-center text-[#A3AED0] italic">Belum ada data absensi
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-[#A3AED0]">Belum ada kegiatan yang diabsen hari ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Buat Absensi -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content !rounded-[20px] !border-none !shadow-2xl">
                <div class="modal-header border-b border-gray-100 p-6">
                    <h5 class="modal-title text-xl font-bold text-[#1B2559]">Buat Absensi Baru</h5>
                    <button type="button" class="close opacity-50 hover:opacity-100 transition-opacity" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <form action="/kepkam/absen" method="post" id="formAbsensi">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Jenis Kegiatan</label>
                                <div class="relative">
                                    <select class="form-control appearance-none" name="kegiatan" id="kegiatan" required>
                                        <option value="" disabled selected>Pilih kegiatan...</option>
                                        @foreach ($kegiatan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#A3AED0]">
                                        <i class="fa fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                <span class="text-xs text-red-500 mt-1 hidden" id="kegiatan-error">Wajib dipilih</span>
                            </div>
                            <div class="form-group">
                                <label class="block text-sm font-bold text-[#1B2559] mb-2">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal-absen" name="tanggal" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
                                <h4 class="text-sm font-bold text-[#1B2559] uppercase tracking-wider my-auto">Daftar Santri
                                </h4>
                                <div class="relative w-full md:w-72">
                                    <input type="text" id="searchSantri"
                                        class="w-full h-11 pl-11 pr-4 rounded-xl text-sm border-0 bg-white shadow-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-[#4318FF] text-[#2B3674] placeholder:text-[#A3AED0] transition-all"
                                        placeholder="Cari nama santri...">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#4318FF]">
                                        <i class="fa fa-search text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-y-auto max-h-[400px] custom-scrollbar">
                                <table class="w-full">
                                    <thead class="sticky top-0 bg-gray-50 z-10">
                                        <tr>
                                            <th class="text-left text-xs text-[#A3AED0] font-bold pb-2">Nama Santri</th>
                                            <th class="text-right text-xs text-[#A3AED0] font-bold pb-2 w-auto">
                                                <div class="flex flex-col items-end gap-1 px-1">
                                                    <span class="text-[10px] uppercase tracking-wider">Setel Semua</span>
                                                    <div class="flex gap-1">
                                                        <button type="button" onclick="setAll('H')"
                                                            class="text-[10px] w-6 h-6 rounded bg-green-100 text-green-600 hover:bg-green-200 font-bold transition-colors"
                                                            title="Set Semua Hadir">H</button>
                                                        <button type="button" onclick="setAll('S')"
                                                            class="text-[10px] w-6 h-6 rounded bg-yellow-100 text-yellow-600 hover:bg-yellow-200 font-bold transition-colors"
                                                            title="Set Semua Sakit">S</button>
                                                        <button type="button" onclick="setAll('I')"
                                                            class="text-[10px] w-6 h-6 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 font-bold transition-colors"
                                                            title="Set Semua Izin">I</button>
                                                        <button type="button" onclick="setAll('A')"
                                                            class="text-[10px] w-6 h-6 rounded bg-red-100 text-red-600 hover:bg-red-200 font-bold transition-colors"
                                                            title="Set Semua Alpha">A</button>
                                                    </div>
                                                </div>
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($santri as $item)
                                            <tr class="border-b border-gray-100 last:border-0 group santri-row">
                                                <td class="py-3 text-sm font-medium text-[#2B3674] santri-name">
                                                    {{ $item->nama }}
                                                </td>
                                                <td class="py-3 text-right">
                                                    <div
                                                        class="inline-flex bg-white rounded-lg shadow-sm border border-gray-200 p-0.5">
                                                        @foreach (['H' => 'H', 'S' => 'S', 'I' => 'I', 'A' => 'A'] as $kode => $label)
                                                            <label class="cursor-pointer relative mb-0">
                                                                <input type="radio" name="santri[{{ $item->nis }}]"
                                                                    value="{{ $kode }}" class="peer sr-only" {{ $kode == 'H' ? 'checked' : '' }}>
                                                                <span
                                                                    class="block px-3 py-1 text-xs font-bold rounded-md transition-all
                                                                                                                                                peer-checked:text-white
                                                                                                                                                {{ $kode == 'H' ? 'text-green-500 peer-checked:bg-green-500' : '' }}
                                                                                                                                                {{ $kode == 'S' ? 'text-yellow-500 peer-checked:bg-yellow-500' : '' }}
                                                                                                                                                {{ $kode == 'I' ? 'text-blue-500 peer-checked:bg-blue-500' : '' }}
                                                                                                                                                {{ $kode == 'A' ? 'text-red-500 peer-checked:bg-red-500' : '' }}
                                                                                                                                                hover:bg-gray-50
                                                                                                                                            ">
                                                                    {{ $label }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="w-full mt-4">
                            <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100">
                                <div class="flex justify-between items-center mb-3 cursor-pointer"
                                    onclick="document.getElementById('advancedOptions').classList.toggle('hidden'); document.getElementById('advancedChevron').classList.toggle('rotate-180');">
                                    <div class="flex items-center gap-2 text-[#4318FF]">
                                        <i class="fa fa-cubes"></i>
                                        <span class="text-sm font-bold">Terapkan ke kegiatan lain</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-[#A3AED0] font-normal"
                                            id="selectedCountLabel">Opsional</span>
                                        <i class="fa fa-chevron-down text-xs text-[#4318FF] transition-transform duration-200"
                                            id="advancedChevron"></i>
                                    </div>
                                </div>

                                <div id="advancedOptions" class="hidden transition-all pt-2 border-t border-indigo-100/50">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-[10px] uppercase font-bold text-[#A3AED0] tracking-wider">Pilih
                                            Kegiatan</span>
                                        <button type="button"
                                            class="text-xs font-bold text-[#4318FF] hover:bg-indigo-100 px-2 py-1 rounded transition-colors"
                                            onclick="toggleAllActivities()" id="btnSelectAll">
                                            Pilih Semua
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($activities as $act)
                                            @php
                                                $isCompleted = in_array($act['id'], $completedIds ?? []);
                                            @endphp
                                            <label class="{{ $isCompleted ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }} relative">
                                                <input type="checkbox" name="additional_activities[]" value="{{ $act['id'] }}"
                                                    class="activity-checkbox peer sr-only" onchange="updateSelectState()"
                                                    {{ $isCompleted ? 'disabled' : '' }}>
                                                <div
                                                    class="p-2.5 rounded-lg border-2 transition-all duration-200 text-center
                                                                {{ $isCompleted ? 'bg-gray-100 border-transparent text-[#A3AED0]' : 'bg-white border-transparent text-[#A3AED0] peer-checked:border-[#4318FF] peer-checked:bg-[#4318FF] peer-checked:text-white peer-checked:shadow-md hover:bg-gray-50' }}">
                                                    <span class="text-xs font-bold block truncate">
                                                        {{ str_replace(['Absensi ', 'Ngaji '], '', $act['title']) }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-t border-gray-100 pt-4 px-0 pb-0 flex justify-end">
                            <div>
                                <button type="button" class="btn bg-gray-100 text-[#2B3674] hover:bg-gray-200"
                                    data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary ml-2">Simpan Absensi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function toggleAgenda(trigger) {
            const card = trigger.closest('.card');
            const content = card.querySelector('.agenda-content');
            const icon = card.querySelector('.fa-chevron-down');

            content.classList.toggle('hidden');
            if (content.classList.contains('hidden')) {
                icon.classList.remove('rotate-180');
            } else {
                icon.classList.add('rotate-180');
            }
        }

        function setAll(status) {
            // Find all radio buttons with the given value (status)
            // The name attribute structure is santri[NIS]
            const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            radios.forEach(radio => {
                const row = radio.closest('tr');
                if (!row.classList.contains('hidden')) {
                    radio.checked = true;
                }
            });
        }

        function openModalWithActivity(activityId) {
            $('#add').modal('show');
            const select = document.getElementById('kegiatan');
            select.value = activityId;
            // Trigger change event to clear validation errors helper
            select.dispatchEvent(new Event('change'));
        }

        function toggleAllActivities() {
            const checkboxes = document.querySelectorAll('.activity-checkbox');
            const btn = document.getElementById('btnSelectAll');
            
            // Check if any is UNCHECKED. If so, we want to SELECT ALL.
            const anyUnchecked = Array.from(checkboxes).some(c => !c.checked);
            
            checkboxes.forEach(c => {
                c.checked = anyUnchecked;
            });
            
            updateSelectState();
        }

        function updateSelectState() {
             const checkboxes = document.querySelectorAll('.activity-checkbox:checked');
             const label = document.getElementById('selectedCountLabel');
             const btn = document.getElementById('btnSelectAll');
             
             // Update visual feedback for all checkboxes
             document.querySelectorAll('.activity-checkbox').forEach(checkbox => {
                 const labelDiv = checkbox.parentElement.querySelector('div');
                 if (checkbox.checked && !checkbox.disabled) {
                     // Checked state - blue background, white text
                     labelDiv.classList.remove('bg-white', 'text-[#A3AED0]', 'border-transparent');
                     labelDiv.classList.add('bg-[#4318FF]', 'text-white', 'border-[#4318FF]', 'shadow-md');
                 } else if (!checkbox.disabled) {
                     // Unchecked state - white background, gray text
                     labelDiv.classList.remove('bg-[#4318FF]', 'text-white', 'border-[#4318FF]', 'shadow-md');
                     labelDiv.classList.add('bg-white', 'text-[#A3AED0]', 'border-transparent');
                 }
             });
             
             if(checkboxes.length > 0) {
                 label.textContent = checkboxes.length + ' Dipilih';
                 label.classList.add('text-[#4318FF]', 'font-bold');
                 label.classList.remove('text-[#A3AED0]', 'font-normal');
             } else {
                 label.textContent = 'Opsional';
                 label.classList.add('text-[#A3AED0]', 'font-normal');
                 label.classList.remove('text-[#4318FF]', 'font-bold');
             }

             // Update Select All Button Text
             const total = document.querySelectorAll('.activity-checkbox').length;
             if (checkboxes.length === total) {
                 btn.textContent = 'Hapus Semua';
             } else {
                 btn.textContent = 'Pilih Semua';
             }
        }

        function editActivity(activityId, statuses) {
             openModalWithActivity(activityId);
             
             // Loop through statuses (nis => status)
             // statuses is an object/array: { "123": "H", "124": "S" }
             for (const [nis, status] of Object.entries(statuses)) {
                 // Find the radio button for this student with this status
                 // Name format: santri[NIS]
                 const radio = document.querySelector(`input[name="santri[${nis}]"][value="${status}"]`);
                 if (radio) {
                     radio.checked = true;
                 }
             }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formAbsensi');
            const kegiatan = document.getElementById('kegiatan');
            const error = document.getElementById('kegiatan-error');

            // Attach listeners to checkboxes dynamically
            document.querySelectorAll('.activity-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectState);
            });

            // Reset checkboxes when modal is hidden
            $('#add').on('hidden.bs.modal', function () {
                document.querySelectorAll('.activity-checkbox').forEach(cb => {
                    cb.checked = false;
                });
                updateSelectState();
                document.getElementById('advancedOptions').classList.add('hidden');
                document.getElementById('advancedChevron').classList.remove('rotate-180');
            });

            // Trigger initial check when modal is shown
            $('#add').on('shown.bs.modal', function () {
                const tanggalInput = document.getElementById('tanggal-absen');
                if (tanggalInput && tanggalInput.value) {
                    // Trigger change event to load initial state
                    tanggalInput.dispatchEvent(new Event('change'));
                }
            });

            form.addEventListener('submit', function (e) {
                if (!kegiatan.value) {
                    e.preventDefault();
                    error.classList.remove('hidden');
                    kegiatan.classList.add('border-red-500');
                } else {
                    error.classList.add('hidden');
                    kegiatan.classList.remove('border-red-500');
                }
            });

            kegiatan.addEventListener('change', function () {
                if (kegiatan.value) {
                    error.classList.add('hidden');
                    kegiatan.classList.remove('border-red-500');
                }
            });

            // Date change listener - update completed activities
            const tanggalInput = document.getElementById('tanggal-absen');
            if (tanggalInput) {
                tanggalInput.addEventListener('change', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                        // Fetch completed activities for selected date
                        fetch(`/kepkam/absensi/check-completed?date=${selectedDate}`)
                            .then(response => response.json())
                            .then(data => {
                                // Update checkboxes based on completed activities
                                document.querySelectorAll('.activity-checkbox').forEach(checkbox => {
                                    const activityId = checkbox.value;
                                    const label = checkbox.closest('label');
                                    const labelDiv = label.querySelector('div');
                                    const labelSpan = label.querySelector('span');
                                    
                                    if (data.completedIds.includes(activityId)) {
                                        checkbox.disabled = true;
                                        checkbox.checked = false;
                                        label.classList.add('cursor-not-allowed', 'opacity-50');
                                        label.classList.remove('cursor-pointer');
                                        labelDiv.classList.add('bg-gray-100');
                                        labelDiv.classList.remove('hover:bg-gray-50');
                                        
                                        // Update text to show (Selesai)
                                        const originalText = labelSpan.textContent.replace(' (Selesai)', '').trim();
                                        labelSpan.textContent = originalText + ' (Selesai)';
                                    } else {
                                        checkbox.disabled = false;
                                        label.classList.remove('cursor-not-allowed', 'opacity-50');
                                        label.classList.add('cursor-pointer');
                                        labelDiv.classList.remove('bg-gray-100');
                                        labelDiv.classList.add('hover:bg-gray-50');
                                        
                                        // Remove (Selesai) text
                                        const originalText = labelSpan.textContent.replace(' (Selesai)', '').trim();
                                        labelSpan.textContent = originalText;
                                    }
                                });
                                updateSelectState();
                            })
                            .catch(error => console.error('Error fetching completed activities:', error));
                    }
                });
            }

            // Search Functionality
            const searchInput = document.getElementById('searchSantri');
            const santriRows = document.querySelectorAll('.santri-row');

            searchInput.addEventListener('keyup', function () {
                const query = this.value.toLowerCase();

                santriRows.forEach(row => {
                    const name = row.querySelector('.santri-name').textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection