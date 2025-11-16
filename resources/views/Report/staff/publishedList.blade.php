@extends('layout.staff')

@section('title', 'Published Reports - PadangPro Staff')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Published Reports</h1>
    <p class="mt-2 text-lime-100">View all report configurations saved by administrators and staff.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    @if($savedReports->isEmpty())
        <div class="text-center py-12 px-6 bg-slate-50 rounded-lg">
            <i class="bi bi-file-earmark-bar-graph text-6xl text-gray-300"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Published Reports</h3>
            <p class="mt-2 text-gray-500">No reports have been published yet.</p>
        </div>
    @else
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider w-2/5">Report Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Report Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Published By</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($savedReports as $report)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900">{{ $report->report_Title }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $report->publisher->full_name ?? 'Unknown' }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                {{-- =============================================== --}}
                                {{-- THIS IS THE CORRECTED LINK COLOR --}}
                                {{-- =============================================== --}}
                                <a href="{{ route('staff.reports.show', array_merge(['report_type' => $report->report_type, 'view_only' => 'true'], (array) $report->parameters)) }}" class="text-indigo-600 hover:text-indigo-900 ">
                                    View Report
                                </a>
                                {{-- =============================================== --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-8">
            {{ $savedReports->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for success messages --}}
@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#166534', // green-800
            confirmButtonText: 'Okay'
        });
    </script>
@endif
@endpush