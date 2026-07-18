<div class="pengurus-item flex items-center justify-between p-3 rounded-xl bg-[#F4F7FE] hover:bg-[#EEF2FF] transition-colors"
    data-search="{{ strtolower($p->nama . ' ' . $p->nis . ' ' . ($jabNama ?? '') . ' ' . ($divNama ?? '')) }}">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
            <i class="fa fa-user text-[#4318FF] text-xs"></i>
        </div>
        <div>
            <p class="font-semibold text-[#1B2559] text-sm">{{ $p->nama }}</p>
            <p class="text-xs text-[#A3AED0] font-mono">{{ $p->nis }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button
            onclick="openEditPengurus('{{ $p->nis }}','{{ addslashes($p->nama) }}',{{ $p->jabatan_id ?? 'null' }})"
            class="w-7 h-7 rounded-lg bg-white hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center shadow-sm"
            title="Edit">
            <i class="fa fa-pen text-xs"></i>
        </button>
        <button
            onclick="confirmDeletePengurus('{{ $p->nis }}','{{ addslashes($p->nama) }}')"
            class="w-7 h-7 rounded-lg bg-white hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center shadow-sm"
            title="Hapus">
            <i class="fa fa-trash text-xs"></i>
        </button>
    </div>
</div>
