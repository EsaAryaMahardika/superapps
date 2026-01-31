@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <!-- Header Section -->
        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="text-xl md:text-2xl font-bold text-[#1B2559]">Rekapan Harian Absensi</h2>
            <p class="text-[#A3AED0] text-xs md:text-sm mt-1">Lihat rekapitulasi kehadiran santri per hari</p>
        </div>

        <!-- Date Filter (Moved Up) -->
        <div class="card mb-4">
            <form method="GET" action="/kepkam/rekap-harian" class="flex flex-col md:flex-row gap-3 md:gap-4 items-end">
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-bold text-[#1B2559] mb-2">Pilih Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') ?? date('Y-m-d') }}"
                        class="form-control w-full">
                </div>
                <button type="submit"
                    class="btn bg-[#4318FF] text-white hover:bg-[#3311CC] px-6 w-full md:w-auto flex justify-center items-center">
                    <i class="fa fa-search mr-2"></i>Tampilkan
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 w-full mb-6">
            {{-- Tombol Share --}}
            <button id="share-btn" onclick="shareAsPng()"
                class="btn bg-white border border-[#E0E5F2] text-[#2B3674] shadow-sm hover:bg-gray-50 flex items-center justify-center gap-2 w-full sm:w-auto">
                <i class="fa fa-share-alt"></i>
                <span>Bagikan Rekapan</span>
            </button>

            {{-- Tombol Download JPG --}}
            <button id="download-jpg-btn" onclick="downloadAsJpg()"
                class="btn btn-primary shadow-brand flex items-center justify-center gap-2 w-full sm:w-auto">
                <i class="fa fa-image"></i>
                <span>Download Rekapan</span>
            </button>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            async function renderPdfToCanvas() {
                // Fetch PDF
                const date = document.querySelector('input[name="tanggal"]').value;
                const url = `/kepkam/rekap-harian/download?tanggal=${date}`;
                const loadingTask = pdfjsLib.getDocument(url);
                const pdf = await loadingTask.promise;

                // Get first page
                const page = await pdf.getPage(1);

                // Set scale for high quality (4x for clearer text)
                const viewport = page.getViewport({ scale: 4 });

                // Prepare canvas
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page to canvas
                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;

                return { canvas, date };
            }

            async function downloadAsJpg() {
                const btn = document.getElementById('download-jpg-btn');
                const originalContent = btn.innerHTML;

                try {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Processing...</span>';

                    const { canvas, date } = await renderPdfToCanvas();

                    // Convert to PNG for better quality on WA
                    const link = document.createElement('a');
                    link.download = `Rekap_Harian_${date}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal mendownload gambar. Silakan coba lagi.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            }

            async function shareAsPng() {
                const btn = document.getElementById('share-btn');
                const originalContent = btn.innerHTML;

                if (!navigator.share) {
                    alert('Browser Anda tidak mendukung fitur share gambar secara langsung.');
                    return;
                }

                try {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Processing...</span>';

                    const { canvas, date } = await renderPdfToCanvas();

                    canvas.toBlob(async (blob) => {
                        const file = new File([blob], `Rekap_Harian_${date}.png`, { type: 'image/png' });

                        try {
                            await navigator.share({
                                files: [file],
                                title: 'Rekapan Harian Absensi',
                                text: `Rekap Absensi Tanggal ${date}`
                            });
                        } catch (shareError) {
                            if (shareError.name !== 'AbortError') {
                                console.error('Error sharing:', shareError);
                            }
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                        }
                    }, 'image/png');

                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal memproses gambar untuk dibagikan.');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            }
        </script>



        <!-- Recap Table -->
        <div class="card">
            <div class="mb-3 md:mb-4 pb-3 md:pb-4 border-b-2 border-gray-300 text-center">
                <h2 class="text-base md:text-xl font-bold text-[#1B2559] mb-1 md:mb-2">Absensi Harian Santri</h2>
                <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-2 md:mb-1">Pondok Pesantren An-Nur II
                    "Al-Murtadlo"</h3>
                <p class="text-xs md:text-sm text-[#2B3674] mb-1">
                    <span class="font-semibold">Kepala Kamar:</span> {{ $kepalaKamar }}
                </p>
                <p class="text-xs md:text-sm text-[#2B3674]">
                    <span class="font-semibold">Tanggal:</span>
                    {{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse" style="text-align: center;">
                    <thead>
                        <tr class="bg-gray-50">
                            <th
                                class="text-[10px] md:text-xs uppercase text-[#A3AED0] font-bold p-2 md:p-3 border-b-2 border-gray-200 sticky left-0 bg-gray-50 z-20 text-center w-[40px] md:w-[50px] min-w-[40px] md:min-w-[50px]">
                                No
                            </th>
                            <th
                                class="text-[10px] md:text-xs uppercase text-[#A3AED0] font-bold p-2 md:p-3 border-b-2 border-gray-200 sticky left-[40px] md:left-[50px] bg-gray-50 z-20 min-w-[120px] md:min-w-[150px] text-left border-r border-gray-200 shadow-sm">
                                Nama Santri
                            </th>
                            @foreach($activities as $activity)
                                <th
                                    class="text-[10px] md:text-xs uppercase text-[#A3AED0] font-bold p-2 md:p-3 border-b-2 border-gray-200 text-center min-w-[80px] md:min-w-[100px]">
                                    {{ $activity['name'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-sm">
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
                                'A' => 'Alfa',
                            ];
                        @endphp

                        @forelse($rekapData as $index => $santri)
                            <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                <td
                                    class="p-2 md:p-3 text-[#2B3674] text-xs md:text-sm font-medium sticky left-0 bg-white z-20 text-center w-[40px] md:w-[50px] min-w-[40px] md:min-w-[50px]">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="p-2 md:p-3 text-[#2B3674] text-xs md:text-sm font-medium sticky left-[40px] md:left-[50px] bg-white z-20 text-left border-r border-gray-100 shadow-sm">
                                    {{ $santri['nama'] }}
                                </td>
                                @foreach($activities as $activity)
                                    <td class="p-2 md:p-3" style="text-align: center; vertical-align: middle;">
                                        @if($santri['attendance'][$activity['name']] !== '-')
                                            <span
                                                class="inline-block px-2 md:px-3 rounded-full text-[10px] md:text-xs font-bold {{ $statusBadge[$santri['attendance'][$activity['name']]] ?? 'bg-gray-100 text-gray-600' }}"
                                                style="display: inline-flex; align-items: center; justify-content: center; height: 20px; line-height: 20px; padding-top: 0; padding-bottom: 0;">
                                                {{ $statusLabel[$santri['attendance'][$activity['name']]] ?? '-' }}
                                            </span>
                                        @else
                                            <span class="text-[#A3AED0] text-[10px] md:text-xs">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($activities) + 2 }}" class="p-8 text-center text-[#A3AED0] italic">
                                    Belum ada data santri
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection