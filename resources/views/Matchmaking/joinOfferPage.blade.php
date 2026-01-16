@extends('layout.customer')

@section('title', 'Request to Join')

@section('content')
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="px-8">
            <div class="text-white font-bold text-2xl">Request to Join</div>
            <div class="text-white/80 text-sm mt-1">
                Send a short note to the organizer to join this match.
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 pb-12">
        {{-- Top actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Join Advertisement</h2>
                <p class="text-sm text-slate-500 mt-1">
                    A friendly note increases approval chances 🙂
                </p>
            </div>

            <a href="{{ route('matchmaking.other') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2
                      text-sm font-semibold text-slate-700 border border-slate-200 shadow-sm
                      hover:bg-slate-100 transition w-full sm:w-auto">
                <i class="bi bi-arrow-left"></i>
                Back to Matches
            </a>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            {{-- Ad summary --}}
            <div class="bg-slate-50 border-b border-slate-200 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                            You are requesting to join
                        </p>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                            {{ $ad->ads_Name ?? 'Untitled Ad' }}
                        </h3>
                    </div>

                    @if(!empty($ad->ads_Status) && strtolower($ad->ads_Status) === 'filled')
                        <span class="inline-flex items-center gap-2 rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-bold">
                            <i class="bi bi-x-circle-fill"></i> Filled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-bold">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </span>
                    @endif
                </div>

                {{-- Ad info grid --}}
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl bg-white border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-500 uppercase">Type</div>
                        <div class="mt-1 font-semibold text-slate-800">
                            {{ $ad->ads_Type ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-white border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-500 uppercase">Slot Time</div>
                        <div class="mt-1 font-semibold text-slate-800">
                            {{ !empty($ad->ads_SlotTime)
                                ? \Carbon\Carbon::parse($ad->ads_SlotTime)->format('D, M j | h:i A')
                                : 'N/A' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-white border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-500 uppercase">Price</div>
                        <div class="mt-1 font-semibold text-slate-800">
                            RM {{ number_format($ad->ads_Price ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="p-6">
                <form method="POST" action="{{ route('matchmaking.joinStore', $ad->adsID) }}">
                    @csrf

                    <div>
                        <label for="note" class="block text-sm font-bold text-slate-700">
                            Your Note to Organizer
                        </label>
                        <p class="text-xs text-slate-500 mt-1">
                            Example: “Hi, I’m available at that time. I play CM and would love to join.”
                        </p>

                        {{-- ✅ Auto-fill note --}}
                        <textarea id="note"
                                  name="note"
                                  rows="7"
                                  required
                                  placeholder="Write your message here..."
                                  class="mt-3 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3
                                         text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                                         placeholder:text-slate-400">{{ old('note', $defaultNote ?? '') }}</textarea>

                        @error('note')
                            <p class="text-red-600 text-sm mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <a href="{{ route('matchmaking.other') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl
                                  bg-white border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700
                                  shadow-sm hover:bg-slate-100 transition">
                            <i class="bi bi-x-circle"></i>
                            Cancel
                        </a>

                        <button type="submit"
                                class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 rounded-xl
                                       bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5
                                       text-sm font-semibold shadow-md transition">
                            <i class="bi bi-send-fill"></i>
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tip --}}
        <div class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>
                <div>
                    <div class="font-bold text-indigo-900">Tip</div>
                    <div class="text-sm text-indigo-900/80 mt-1">
                        Keep your note short, mention your position or skill level, and confirm availability.
                        Organizers usually approve faster.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
