@extends('layout.admin')

@section('title', 'Published Reports - PadangPro Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Published Reports</h1>
    <p class="mt-2 text-amber-100">View all report configurations saved by administrators.</p>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    @if($savedReports->isEmpty())
        <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
            <i class="bi bi-file-earmark-bar-graph text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">No reports have been published yet.</p>
        </div>
    @else

        {{-- =============================  
             DESKTOP TABLE VIEW (≥640px)
        ============================== --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">Report Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published By</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($savedReports as $report)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900">
                            {{ $report->report_Title }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->publisher->full_name ?? 'Unknown' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('admin.reports.show', array_merge(['report_type' => $report->report_type, 'view_only' => 'true'], (array)$report->parameters)) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                View Report
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- =============================  
                MOBILE CARD VIEW (<640px)
        ============================== --}}
        <div class="sm:hidden space-y-4 mt-4">
            @foreach($savedReports as $report)
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">

                <p class="text-xs text-gray-500 uppercase">Report Name</p>
                <p class="font-bold text-gray-900 mb-2">
                    {{ $report->report_Title }}
                </p>

                <p class="text-xs text-gray-500 uppercase">Report Type</p>
                <p class="text-gray-800 mb-2">
                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                </p>

                <p class="text-xs text-gray-500 uppercase">Published By</p>
                <p class="text-gray-800 mb-4">
                    {{ $report->publisher->full_name ?? 'Unknown' }}
                </p>

                <div class="text-right">
                    <a href="{{ route('admin.reports.show', array_merge(['report_type' => $report->report_type, 'view_only' => 'true'], (array)$report->parameters)) }}"
                       class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold shadow hover:bg-indigo-700 transition">
                       View Report
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $savedReports->links() }}
        </div>

    @endif
</div>
@endsection

{{-- SweetAlert2 for success messages --}}
@if(session('success'))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'Okay'
        });
    </script>
    @endpush
@endif
