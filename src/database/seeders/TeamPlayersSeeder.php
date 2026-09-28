<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Player;
use App\Models\PlayerPayment;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TeamPlayersSeeder extends Seeder
{
    public function run(): void
    {
        // Tražimo klub (FK Djura ili prvi iz baze)
        $club = Club::first();

        if (!$club) {
            $this->command->error('Nijedan klub nije pronađen u bazi.');
            return;
        }

        // Dohvatamo sve timove tog kluba
        $teams = Team::where('club_id', $club->id)->get();

        if ($teams->isEmpty()) {
            $this->command->error('Nema timova dodeljenih ovom klubu.');
            return;
        }

        $positions = ['GK', 'CB', 'LB', 'RB', 'CDM', 'CM', 'CAM', 'LM', 'RM', 'ST', 'LW', 'RW'];

        $firstNames = [
            'Marko', 'Nikola', 'Luka', 'Stefan', 'Miloš', 'Nemanja', 'Aleksandar', 
            'Dušan', 'Uroš', 'Lazar', 'Filip', 'Vuk', 'Pavle', 'Ognjen', 'Milan', 
            'Strahinja', 'Đorđe', 'Igor', 'Bojan', 'Viktor'
        ];

        $lastNames = [
            'Petrović', 'Jovanović', 'Nikolić', 'Marković', 'Đorđević', 'Stojanović', 
            'Ilić', 'Stanković', 'Pavlović', 'Milošević', 'Teodorić', 'Kovačević', 
            'Popović', 'Vasić', 'Lazić', 'Gajić', 'Vuković', 'Cvetković'
        ];

        $currentPeriod = date('Y-m');

        foreach ($teams as $team) {
            $this->command->info("Dodavanje 15 igrača u tim: {$team->name}...");

            for ($i = 1; $i <= 15; $i++) {
                $firstName = Arr::random($firstNames);
                $lastName = Arr::random($lastNames);
                $position = Arr::random($positions);
                $jerseyNumber = rand(1, 99);
                $height = rand(160, 200); // visina igrača u cm
                $dateOfBirth = date('Y-m-d', strtotime('-'.rand(18, 35).' years')); // datum rođenja igrača

                // 1. Kreiranje igrača
                $player = Player::create([
                    'club_id' => $club->id,
                    'team_id' => $team->id,
                    'name' => "{$firstName} {$lastName}",
                    'email' => strtolower("{$firstName}.{$lastName}.{$team->id}.{$i}@testmail.com"),
                    'jersey_number' => $jerseyNumber,
                    'primary_position' => $position,
                    'height' => $height,
                    'date_of_birth' => $dateOfBirth,
                ]);

                // 2. Kreiranje nasumične članarine/isplate za tekući mesec za finansije
                $statuses = ['paid', 'pending', 'overdue'];
                $status = Arr::random($statuses);

                // Mesečna članarina za akademiju ili honorar za prvotimce
                $amount = str_contains(strtolower($team->name), 'djura') ? 15000 : 3000;

                PlayerPayment::create([
                    'club_id' => $club->id,
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'type' => str_contains(strtolower($team->name), 'djura') ? 'stipend' : 'membership',
                    'period' => $currentPeriod,
                    'amount' => $amount,
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? now() : null,
                ]);
            }
        }

        $this->command->info('Uspešno ubaciivani igrači i članarine za sve timove!');
    }
}