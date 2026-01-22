@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <h2 class="text-2xl font-bold mb-6 text-[#1B2559]">Absensi Kegiatan Santri</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            <!-- Subuh -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Jamaah Subuh</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($subuh as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td colspan="4" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dhuhur -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Jamaah Dhuhur</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($dhuhur as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td colspan="4" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Asar -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Jamaah Ashar</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($ashar as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td colspan="4" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Maghrib -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Jamaah Maghrib</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($maghrib as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td colspan="4" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Isya -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Jamaah Isya</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($isya as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td colspan="4" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $item->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Waqiah -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Waqiah</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($waqiah as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td colspan="3" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ngaji Sore -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Ngaji Sore</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($ngasore as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td colspan=" 5" class="py-3 text-center">
                                            <a href=" /kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600
                                                                px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ngaji Malam -->
            <div class="card h-fit transition-all duration-300">
                <div class="flex justify-between items-center mb-4 cursor-pointer" onclick="toggleAgenda(this)">
                    <h3 class="text-lg font-bold text-[#1B2559]">Ngaji Malam</h3>
                    <i class="fa fa-chevron-down text-[#A3AED0] transition-transform duration-300"></i>
                </div>
                <div class="overflow-x-auto hidden agenda-content">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0]">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Hadir</th>
                                <th class="pb-2">Sakit</th>
                                <th class="pb-2">Izin</th>
                                <th class="pb-2">Alfa</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-[#2B3674]">
                            @foreach ($ngamalam as $item)
                                @if(isset($item->is_filled) && !$item->is_filled)
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td colspan="5" class="py-3 text-center">
                                            <a href="/kepkam/absensi"
                                                class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-200 transition-colors">
                                                Silahkan isi absen
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3">{{ $item->tanggal }}</td>
                                        <td class="py-3 text-green-500">{{ $item->hadir }}</td>
                                        <td class="py-3 text-yellow-500">{{ $item->sakit }}</td>
                                        <td class="py-3 text-blue-500">{{ $item->izin }}</td>
                                        <td class="py-3 text-red-500">{{ $item->alfa }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
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
    </script>
@endsection