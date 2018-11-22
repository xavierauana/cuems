<?php

use App\Position;
use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $positions = [
            "Advanced Practice Nurse",
            "Associate Consultant",
            "Chief Executive",
            "Chief of Service",
            "Consultant",
            "Dietitian",
            "Doctor",
            "Enrolled Nurse",
            "Head/Chairman",
            "Medical Officer",
            "Medical Practitioner",
            "Pharmacist",
            "Physiotherapist",
            "Professor",
            "Registered Nurse",
            "Researcher",
            "Resident",
            "Resident Specialist",
            "Student",
            "Trainee",
            "Others",
        ];

        foreach ($positions as $position) {
            Position::create([
                'name' => $position
            ]);
        }
    }
}
