<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedEmergencyTypes();
        $this->seedEmergencyAgencies();
        $this->seedFacilities();
        $this->seedAdminUser();
    }

    private function seedEmergencyTypes()
    {
        $types = [
            [
                'name' => 'Medical Emergency',
                'slug' => 'medical',
                'description' => 'Medical emergencies including heart attacks, injuries, etc.',
                'icon' => '🚑',
                'color' => '#e74c3c',
                'agency_type' => 'ambulance',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Fire',
                'slug' => 'fire',
                'description' => 'Fire emergencies, building fires, wildfires',
                'icon' => '🚒',
                'color' => '#e67e22',
                'agency_type' => 'fire',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Crime / Police',
                'slug' => 'crime',
                'description' => 'Criminal activities, theft, assault',
                'icon' => '🚓',
                'color' => '#3498db',
                'agency_type' => 'police',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Vehicular Accident',
                'slug' => 'accident',
                'description' => 'Car accidents, traffic collisions',
                'icon' => '🚗',
                'color' => '#9b59b6',
                'agency_type' => 'rescue',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Flood',
                'slug' => 'flood',
                'description' => 'Flooding and water emergencies',
                'icon' => '🌊',
                'color' => '#1abc9c',
                'agency_type' => 'rescue',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Earthquake',
                'slug' => 'earthquake',
                'description' => 'Earthquake emergencies',
                'icon' => '🌎',
                'color' => '#f39c12',
                'agency_type' => 'rescue',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Landslide',
                'slug' => 'landslide',
                'description' => 'Landslide emergencies',
                'icon' => '🌋',
                'color' => '#8e44ad',
                'agency_type' => 'rescue',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Typhoon / Storm',
                'slug' => 'storm',
                'description' => 'Typhoon and severe storm emergencies',
                'icon' => '🌪️',
                'color' => '#34495e',
                'agency_type' => 'rescue',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Electrical Hazard',
                'slug' => 'electrical',
                'description' => 'Electrical hazards',
                'icon' => '⚡',
                'color' => '#f1c40f',
                'agency_type' => 'fire',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Missing Person',
                'slug' => 'missing',
                'description' => 'Missing person cases',
                'icon' => '🧒',
                'color' => '#e91e63',
                'agency_type' => 'police',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Building Collapse',
                'slug' => 'collapse',
                'description' => 'Building collapse emergencies',
                'icon' => '🏚️',
                'color' => '#795548',
                'agency_type' => 'rescue',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Hazardous Materials',
                'slug' => 'hazmat',
                'description' => 'Hazardous material incidents',
                'icon' => '☣️',
                'color' => '#607d8b',
                'agency_type' => 'fire',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Other Emergency',
                'slug' => 'other',
                'description' => 'Other emergencies',
                'icon' => '❓',
                'color' => '#95a5a6',
                'agency_type' => 'dispatch',
                'priority' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DB::table('emergency_types')->updateOrInsert(
                ['slug' => $type['slug']],
                $type
            );
        }
    }

    private function seedEmergencyAgencies()
    {
        $agencies = [
            [
                'name' => 'National Police',
                'type' => 'police',
                'phone' => '911',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'National Police',
                'emergency_type_id' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Fire Department',
                'type' => 'fire',
                'phone' => '912',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'Fire Department',
                'emergency_type_id' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Medical Services',
                'type' => 'ambulance',
                'phone' => '913',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'Emergency Medical Services',
                'emergency_type_id' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Coast Guard',
                'type' => 'rescue',
                'phone' => '914',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'Coast Guard',
                'emergency_type_id' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Hospital',
                'type' => 'hospital',
                'phone' => '915',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'Municipal Hospital',
                'emergency_type_id' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Dispatch Center',
                'type' => 'dispatch',
                'phone' => '916',
                'email' => null,
                'address' => 'Manila City',
                'latitude' => null,
                'longitude' => null,
                'website' => null,
                'description' => 'Emergency Dispatch Center',
                'emergency_type_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($agencies as $agency) {
            DB::table('emergency_agencies')->updateOrInsert(
                ['name' => $agency['name']],
                $agency
            );
        }
    }

    private function seedFacilities()
    {
        $facilities = [
            [
                'name' => 'Central Hospital',
                'type' => 'hospital',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'address' => 'Manila City',
                'phone' => '911',
                'is_active' => true,
            ],
            [
                'name' => 'Fire Station No. 1',
                'type' => 'fire_station',
                'latitude' => 14.5895,
                'longitude' => 120.9822,
                'address' => 'Manila City',
                'phone' => '912',
                'is_active' => true,
            ],
            [
                'name' => 'Police Station',
                'type' => 'police_station',
                'latitude' => 14.6095,
                'longitude' => 120.9862,
                'address' => 'Manila City',
                'phone' => '911',
                'is_active' => true,
            ],
        ];

        foreach ($facilities as $facility) {
            DB::table('facilities')->updateOrInsert(
                ['name' => $facility['name']],
                $facility
            );
        }
    }

    private function seedAdminUser()
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@resq.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'phone' => '+639123456789',
                'user_type' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}