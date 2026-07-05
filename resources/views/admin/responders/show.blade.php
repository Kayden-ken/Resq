@extends('layouts.admin')

@section('title', 'Responder Details')

@section('content')
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:justify-between xl:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Responder Details</h1>
        <p class="text-slate-500">View and manage the responder profile.</p>
    </div>
    <a href="{{ route('admin.responders') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Responders
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-4xl">
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <p class="text-sm text-slate-500">Name</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $responder->user?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Email</p>
            <p class="mt-2 text-slate-800">{{ $responder->user?->email ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Agency</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $responder->agency?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Badge Number</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $responder->badge_number ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Status</p>
            <p class="mt-2 font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $responder->status)) }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">On Duty</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $responder->is_on_duty ? 'Yes' : 'No' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Vehicle</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $responder->vehicle_info ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.responders.edit', $responder->id) }}" class="rounded-xl bg-blue-600 text-white px-5 py-3 hover:bg-blue-700 transition inline-flex items-center gap-2">
            <i class="fas fa-edit"></i> Edit Responder
        </a>
    </div>
</div>
@endsection