<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser le cache des permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Entités du système
        $entities = [
            'users',
            'departements',
            'formations',
            'ues',
            'ecues',
            'etudiants',
            'inscriptions',
            'notes',
           
            'ue_ecue',
        ];

        // Actions possibles
        $actions = ['view', 'create', 'edit', 'delete'];

        // Créer toutes les permissions
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$entity}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Créer les trois rôles
        $admin = Role::firstOrCreate([
            'name' => 'administrateur',
            'guard_name' => 'web',
        ]);

        $directeur = Role::firstOrCreate([
            'name' => 'directeur_etudes',
            'guard_name' => 'web',
        ]);

        $secretaire = Role::firstOrCreate([
            'name' => 'secretaire',
            'guard_name' => 'web',
        ]);

        // ADMINISTRATEUR : toutes les permissions
        $admin->syncPermissions(Permission::all());

        // DIRECTEUR DES ÉTUDES :
        // gestion pédagogique et suivi des résultats
        $directeur->syncPermissions([
            
            'formations.view',
            'formations.create',
            'formations.edit',

            'ues.view',
            'ues.create',
            'ues.edit',
            'ues.delete',

            'ecues.view',
            'ecues.create',
            'ecues.edit',
            'ecues.delete',

            'etudiants.view',
            'inscriptions.view',
            'inscriptions.edit',

            'notes.view',
            'notes.create',
            'notes.edit',

            'ue_ecue.view',
            'ue_ecue.create',
             'ue_ecue.edit',
            'ue_ecue.delete',
        ]);

        // SECRÉTAIRE :
        // gestion administrative des dossiers
        $secretaire->syncPermissions([
            'ue_ecue.view',
            'ue_ecue.create',
            'ue_ecue.edit',
            'ue_ecue.delete',

            'formations.view',
            'ues.view',
            'ecues.view',

            'etudiants.view',
            'etudiants.create',
            'etudiants.edit',

            'inscriptions.view',
            'inscriptions.create',
            'inscriptions.edit',

            'note.view',
            'note.edit',
            'note.create',
            'note.delete',

        ]);

        // Réinitialiser le cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
