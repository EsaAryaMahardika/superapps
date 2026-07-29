@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#1B2559]">Kelola Kelompok Santri</h2>
                <p class="text-[#A3AED0] text-sm mt-1">Kelompokkan santri untuk memudahkan absensi kegiatan</p>
            </div>
            <button type="button" class="btn btn-primary shadow-brand flex items-center gap-2" onclick="openCreateModal()">
                <i class="fa fa-plus"></i>
                <span>Buat Kelompok</span>
            </button>
        </div>

        <!-- Kelompok Cards -->
        @if($kelompok->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach($kelompok as $group)
                    <div class="card h-fit transition-all duration-300 hover:shadow-lg border-l-4 border-[#4318FF]">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-[#1B2559] text-lg">{{ $group->nama }}</h3>
                                @if($group->keterangan)
                                    <p class="text-xs text-[#A3AED0] mt-1">{{ $group->keterangan }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick='openEditModal(@json($group))'
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                    title="Edit Kelompok">
                                    <i class="fa fa-pencil text-xs"></i>
                                </button>
                                <form action="/kepkam/kelompok/{{ $group->id }}" method="POST"
                                    onsubmit="return confirm('Hapus kelompok ini? Santri di dalamnya akan menjadi tanpa kelompok.');"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors"
                                        title="Hapus Kelompok">
                                        <i class="fa fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#4318FF]/10 text-[#4318FF]">
                                <i class="fa fa-users mr-1.5"></i>
                                {{ $group->santri_count }} Santri
                            </span>
                            <button type="button" onclick="openAssignModal({{ $group->id }}, '{{ $group->nama }}')"
                                class="text-xs font-bold text-[#4318FF] hover:bg-[#4318FF]/10 px-3 py-1.5 rounded-lg transition-colors">
                                <i class="fa fa-user-plus mr-1"></i> Tambah Santri
                            </button>
                        </div>

                        <!-- Anggota List (collapsible) -->
                        @if($group->santri->count() > 0)
                            <div class="border-t border-gray-100 pt-3">
                                <button type="button" onclick="toggleMembers(this)"
                                    class="flex items-center justify-between w-full text-left">
                                    <span class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Anggota</span>
                                    <i class="fa fa-chevron-down text-xs text-[#A3AED0] transition-transform duration-200"></i>
                                </button>
                                <div class="hidden mt-2 max-h-40 overflow-y-auto custom-scrollbar space-y-1">
                                    @foreach($group->santri as $s)
                                        <div class="flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-gray-50 group">
                                            <span class="text-sm text-[#2B3674]">{{ $s->nama }}</span>
                                            <form action="/kepkam/kelompok/{{ $group->id }}/unassign" method="POST" class="m-0 hidden group-hover:block">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="santri[]" value="{{ $s->nis }}">
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors" title="Keluarkan">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-12 mb-8">
                <div class="w-16 h-16 rounded-full bg-[#4318FF]/10 flex items-center justify-center mx-auto mb-4">
                    <i class="fa fa-users text-2xl text-[#4318FF]"></i>
                </div>
                <h3 class="font-bold text-[#1B2559] mb-2">Belum Ada Kelompok</h3>
                <p class="text-sm text-[#A3AED0] mb-4">Buat kelompok untuk mengelompokkan santri Anda</p>
                <button type="button" onclick="openCreateModal()" class="btn btn-primary text-sm">
                    <i class="fa fa-plus mr-2"></i>Buat Kelompok Pertama
                </button>
            </div>
        @endif

        <!-- Santri Tanpa Kelompok -->
        @php
            $tanpaKelompok = $santri->filter(fn($s) => $s->kelompok_id === null);
        @endphp
        @if($tanpaKelompok->count() > 0)
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-[#1B2559]">
                        Santri Tanpa Kelompok
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                            {{ $tanpaKelompok->count() }}
                        </span>
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($tanpaKelompok as $s)
                        <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <span class="text-sm text-[#2B3674]">{{ $s->nama }}</span>
                            @if($kelompok->count() > 0)
                                <div class="relative">
                                    <select onchange="quickAssign(this, '{{ $s->nis }}')"
                                        class="text-xs bg-white border border-gray-200 rounded-lg px-2 py-1 text-[#4318FF] font-bold cursor-pointer focus:ring-1 focus:ring-[#4318FF] outline-none">
                                        <option value="">Pindahkan ke...</option>
                                        @foreach($kelompok as $group)
                                            <option value="{{ $group->id }}">{{ $group->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Create/Edit Kelompok -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="modalKelompok">
        <div class="absolute inset-0 bg-black/40" onclick="closeModal('modalKelompok')"></div>
        <div class="relative bg-white rounded-[20px] shadow-2xl w-full max-w-md z-10">
            <div class="border-b border-gray-100 p-6 flex items-center justify-between">
                <h5 class="text-xl font-bold text-[#1B2559]" id="modalKelompokTitle">Buat Kelompok Baru</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center"
                    onclick="closeModal('modalKelompok')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-6">
                <form id="formKelompok" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-[#1B2559] mb-2">Nama Kelompok</label>
                        <input type="text" name="nama" id="inputNama"
                            class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-12 px-5 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all"
                            placeholder="Contoh: Kelompok A, Halaqah 1, dll" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-[#1B2559] mb-2">Keterangan <span class="text-[#A3AED0] font-normal">(opsional)</span></label>
                        <input type="text" name="keterangan" id="inputKeterangan"
                            class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-12 px-5 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all"
                            placeholder="Deskripsi singkat kelompok">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn bg-gray-100 text-[#2B3674] hover:bg-gray-200" onclick="closeModal('modalKelompok')">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Santri -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="modalAssign">
        <div class="absolute inset-0 bg-black/40" onclick="closeModal('modalAssign')"></div>
        <div class="relative bg-white rounded-[20px] shadow-2xl w-full max-w-2xl z-10 max-h-[90vh] overflow-y-auto">
            <div class="border-b border-gray-100 p-6 flex items-center justify-between">
                <h5 class="text-xl font-bold text-[#1B2559]">Tambah Santri ke <span id="assignGroupName" class="text-[#4318FF]"></span></h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center"
                    onclick="closeModal('modalAssign')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-6">
                <form id="formAssign" method="POST">
                    @csrf
                    <!-- Search -->
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#4318FF]">
                            <i class="fa fa-search text-sm"></i>
                        </div>
                        <input type="text" id="searchAssign"
                            class="w-full h-11 pl-11 pr-4 rounded-xl text-sm border-0 bg-[#F4F7FE] ring-1 ring-gray-200 focus:ring-2 focus:ring-[#4318FF] text-[#2B3674] placeholder:text-[#A3AED0] transition-all"
                            placeholder="Cari nama santri...">
                    </div>

                    <!-- Santri List -->
                    <div class="max-h-72 overflow-y-auto custom-scrollbar border border-gray-100 rounded-xl p-3 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="assignContainer">
                            @foreach($santri as $s)
                                @php $sudahPunyaKelompok = $s->kelompok_id !== null; @endphp
                                <label class="assign-item flex items-center p-3 rounded-lg border border-transparent transition-all {{ $sudahPunyaKelompok ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-gray-50 cursor-pointer' }}">
                                    <input type="checkbox" name="santri[]" value="{{ $s->nis }}"
                                        class="w-4 h-4 text-[#4318FF] bg-gray-100 border-gray-300 rounded focus:ring-[#4318FF] mr-3"
                                        {{ $sudahPunyaKelompok ? 'disabled' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 assign-name">{{ $s->nama }}</span>
                                        @if($sudahPunyaKelompok)
                                            <span class="block text-[10px] text-[#A3AED0]">
                                                Sudah di: {{ $kelompok->firstWhere('id', $s->kelompok_id)->nama ?? '-' }}
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn bg-gray-100 text-[#2B3674] hover:bg-gray-200" onclick="closeModal('modalAssign')">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-user-plus mr-2"></i>Masukkan ke Kelompok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function openCreateModal() {
            document.getElementById('modalKelompokTitle').textContent = 'Buat Kelompok Baru';
            document.getElementById('formKelompok').action = '/kepkam/kelompok';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputKeterangan').value = '';
            // Remove hidden method field if exists
            const methodField = document.querySelector('#formKelompok input[name="_method"]');
            if (methodField) methodField.remove();
            openModal('modalKelompok');
        }

        function openEditModal(group) {
            document.getElementById('modalKelompokTitle').textContent = 'Edit Kelompok';
            document.getElementById('formKelompok').action = '/kepkam/kelompok/' + group.id;
            document.getElementById('inputNama').value = group.nama;
            document.getElementById('inputKeterangan').value = group.keterangan || '';
            // Add PUT method
            let methodField = document.querySelector('#formKelompok input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                document.getElementById('formKelompok').prepend(methodField);
            }
            methodField.value = 'PUT';
            openModal('modalKelompok');
        }

        function openAssignModal(groupId, groupName) {
            document.getElementById('assignGroupName').textContent = groupName;
            document.getElementById('formAssign').action = '/kepkam/kelompok/' + groupId + '/assign';
            // Reset checkboxes
            document.querySelectorAll('#assignContainer input[type="checkbox"]').forEach(cb => cb.checked = false);
            openModal('modalAssign');
        }

        function toggleMembers(btn) {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('i');
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        function quickAssign(select, nis) {
            const groupId = select.value;
            if (!groupId) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/kepkam/kelompok/' + groupId + '/assign';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const santriInput = document.createElement('input');
            santriInput.type = 'hidden';
            santriInput.name = 'santri[]';
            santriInput.value = nis;

            form.appendChild(csrf);
            form.appendChild(santriInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Search in assign modal
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchAssign');
            if (searchInput) {
                searchInput.addEventListener('keyup', function () {
                    const query = this.value.toLowerCase();
                    document.querySelectorAll('.assign-item').forEach(item => {
                        const name = item.querySelector('.assign-name').textContent.toLowerCase();
                        item.classList.toggle('hidden', !name.includes(query));
                    });
                });
            }
        });
    </script>
@endsection
