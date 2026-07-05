@extends('layouts.admin')

@section('title', 'Add Responder')

@section('content')
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:justify-between xl:items-center w-full">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Add Responder</h1>
        <p class="text-slate-500">Create a new responder profile for the admin panel.</p>
    </div>
    <a href="{{ route('admin.responders') }}" class="px-5 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Responders
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 w-full max-w-full">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.responders.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="user_id" class="block text-sm font-medium text-slate-700">User</label>
            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" required>
                <option value="">Select user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="agency_id" class="block text-sm font-medium text-slate-700">Agency</label>
            <select id="agency_id" name="agency_id" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" required>
                <option value="">Select agency</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="badge_number" class="block text-sm font-medium text-slate-700">Badge Number</label>
            <input id="badge_number" name="badge_number" type="text" value="{{ old('badge_number') }}" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" placeholder="Badge number">
        </div>

        <div>
            <label for="vehicle_info" class="block text-sm font-medium text-slate-700">Vehicle Info</label>
            <input id="vehicle_info" name="vehicle_info" type="text" value="{{ old('vehicle_info') }}" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none" placeholder="Vehicle make, plate, or unit">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Availability Status</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="busy" {{ old('status') == 'busy' ? 'selected' : '' }}>Busy</option>
                    <option value="on_duty" {{ old('status') == 'on_duty' ? 'selected' : '' }}>On Duty</option>
                </select>
            </div>
            <div>
                <label for="is_on_duty" class="block text-sm font-medium text-slate-700">On Duty</label>
                <select id="is_on_duty" name="is_on_duty" class="mt-1 block w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    <option value="0" {{ old('is_on_duty') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_on_duty') == '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full rounded-xl bg-blue-600 text-white px-4 py-3 text-sm font-semibold hover:bg-blue-700 transition">Create Responder</button>
    </form>
</div>
@endsection