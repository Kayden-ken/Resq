<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\EmergencyRequest;
use App\Models\EmergencyType;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activeRequests = collect();
        $announcements = collect();
        $emergencyTypes = collect();
        $facilities = collect();

        if ($user) {
            $activeRequests = EmergencyRequest::where('requester_id', $user->id)
                ->with(['emergencyType'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            $announcements = $this->safeQuery(function () {
                return Announcement::active()->whereIn('target_audience', ['all', 'users'])->orderByDesc('created_at')->take(5)->get();
            });

            $emergencyTypes = $this->safeQuery(function () {
                return EmergencyType::where('is_active', true)->orderBy('priority')->take(10)->get();
            });

            $facilities = $this->safeQuery(function () {
                return Facility::where('is_active', true)->orderBy('name')->take(6)->get();
            });
        }

        $activeRequests = $activeRequests instanceof \Illuminate\Support\Collection ? $activeRequests : collect($activeRequests);
        $announcements = $announcements instanceof \Illuminate\Support\Collection ? $announcements : collect($announcements);
        $emergencyTypes = $emergencyTypes instanceof \Illuminate\Support\Collection ? $emergencyTypes : collect($emergencyTypes);
        $facilities = $facilities instanceof \Illuminate\Support\Collection ? $facilities : collect($facilities);

        return view('home', compact('user', 'activeRequests', 'announcements', 'emergencyTypes', 'facilities'));
    }

    private function safeQuery(callable $callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Base table or view not found')) {
                return collect();
            }

            throw $e;
        }
    }
}
