@extends('layouts.admin')

@section('title', 'Emergency Request Details')

@section('content')
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:justify-between xl:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Emergency Request Details</h1>
        <p class="text-slate-500">View details and deploy a responder for this request.</p>
    </div>
    <a href="{{ route('admin.requests') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Requests
    </a>
</div>

<div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-sm text-slate-500">Incident Number</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $emergencyRequest->incident_number }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Status</p>
                <p class="mt-2 font-semibold text-slate-800">{{ ucfirst($emergencyRequest->status) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Type</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $emergencyRequest->emergencyType?->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Requester</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $emergencyRequest->requester?->name ?? 'N/A' }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm text-slate-500">Location</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $emergencyRequest->address }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm text-slate-500">Description</p>
                <p class="mt-2 text-slate-700">{{ $emergencyRequest->description }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm text-slate-500">Severity</p>
                <p class="mt-2 font-semibold text-slate-800">{{ ucfirst($emergencyRequest->severity) }}</p>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold text-slate-800">Request History</h2>
            <div class="mt-4 space-y-3">
                @forelse($emergencyRequest->history as $history)
                <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                    <p class="text-sm text-slate-500">{{ $history->created_at->format('M d, Y H:i') }}</p>
                    <p class="mt-2 font-semibold text-slate-800">{{ ucfirst($history->status) }}</p>
                    <p class="mt-1 text-slate-600">{{ $history->notes }}</p>
                </div>
                @empty
                <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50 text-slate-500">
                    No history records found.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <aside class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-800">Deploy Responder</h2>
            <p class="text-sm text-slate-500">Choose an available responder for this incident.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.requests.assign', $emergencyRequest->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="responder_id" class="block text-sm font-medium text-slate-700">Responder</label>
                <select id="responder_id" name="responder_id" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" required>
                    <option value="">Select responder</option>
                    @foreach($availableResponders as $responder)
                        <option value="{{ $responder->id }}">{{ $responder->user?->name ?? 'Responder #' . $responder->id }} — {{ $responder->agency?->name ?? 'No Agency' }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full rounded-xl bg-red-600 text-white px-4 py-3 text-sm font-semibold hover:bg-red-700 transition">Deploy Responder</button>
        </form>

        @if($availableResponders->isEmpty())
            <p class="mt-4 text-sm text-slate-500">No available responders match this request type right now.</p>
        @endif
    </aside>
</div>
@endsection