<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;

class UserSeeder extends Seeder
{
    private const WORKSPACE_CODES = [
        'caissière' => 75804,
        'calviste' => 75804,
        'rayoniste' => 75804,
        'controleur' => 75804,
        'technicien de surface' => 75804,
        'patissier' => 182736,
        'boulanger' => 394857,
        'pointeur' => 527194,
        'enfourneur' => 639285,
        'glaciere' => 583492,
        'chef de production' => 948371,
        'dg' => 217634,
        'ddg' => 365982,
        "gestionnaire d'alimentation" => 365982,
        'pdg' => 592483,
        'vendeur_boulangerie' => 748596,
        'vendeur_patisserie' => 983214
    ];

    public function run(): void
    {
        $faker = Factory::create('fr_FR');

        $secteurs = ['production', 'vente', 'administration', 'glace','alimentation'];
        $roles = [
            ['role' => 'pdg', 'count' => 1],
            ['role' => 'dg', 'count' => 1],
            ['role' => 'ddg', 'count' => 1],
            ['role' => 'chef de production', 'count' => 2],
            ['role' => "gestionnaire d'alimentation", 'count' => 1],
            ['role' => 'technicien de surface', 'count' => 3],
            ['role' => 'glaciere', 'count' => 1],
            ['role' => 'patissier', 'count' => 10],
            ['role' => 'boulanger', 'count' => 10],
            ['role' => 'vendeur_boulangerie', 'count' => 5],
            ['role' => 'vendeur_patisserie', 'count' => 5],
            ['role' => 'caissière', 'count' => 3],
            ['role' => 'calviste', 'count' => 2],
            ['role' => 'rayoniste', 'count' => 2],
            ['role' => 'controleur', 'count' => 2],
            ['role' => 'pointeur', 'count' => 2],
            ['role' => 'enfourneur', 'count' => 2],
        ];

        // Calculer le nombre d'employés administratifs restants
        $totalSpecificRoles = array_sum(array_column($roles, 'count'));
        $adminCount = 100 - $totalSpecificRoles;
        // Ajouter les employés administratifs à la liste des rôles
        $roles[] = ['role' => 'employé administratif', 'count' => $adminCount];

        $counter = 1;
        foreach ($roles as $roleInfo) {
            for ($i = 0; $i < $roleInfo['count']; $i++) {
                $dateNaissance = Carbon::now()->subYears(rand(19, 30))->subDays(rand(0, 365));

                // Déterminer le secteur en fonction du rôle
                $secteur = match (strtolower($roleInfo['role'])) {
                    'pdg', 'dg', 'ddg','chef de production','gestionnaire d\'alimentation', => 'administration',
                   'patissier', 'boulanger', 'glaciere', 'pointeur', 'enfourneur' => 'production',
                   'vendeur boulangerie', 'vendeur pâtisserie' => 'vente',
                    'caissière', 'calviste', 'rayoniste', 'controleur' => 'alimentation',
                    'glaciere' => 'glace',
                    'technicien de surface' => 'production',
                    default => $secteurs[array_rand($secteurs)],
                };

                $gender = $faker->randomElement(['male', 'female']);
                $firstName = strtolower($faker->firstName($gender));
                $lastName = strtolower($faker->lastName());
                $fullName = $firstName . ' ' . $lastName;

                User::create([
                    'name' => $fullName,
                    'email' => str_replace(' ', '.', $fullName) . "@example.com",
                    'password' => Hash::make('anonymous'),
                    'date_naissance' => $dateNaissance,
                    'code_secret' => self::WORKSPACE_CODES[$roleInfo['role']] ?? rand(1000, 9999),
                    'secteur' => $secteur,
                    'role' => $roleInfo['role'],
                    'num_tel' => '0' . rand(600000000, 699999999),
                    'avance_salaire' => 0,
                    'annee_debut_service' => Carbon::now()->year,
                    'email_verified_at' => Carbon::now(),
                ]);
                $counter++;
            }
        }
    }
}
