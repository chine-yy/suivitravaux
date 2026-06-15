<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des équipes
    |--------------------------------------------------------------------------
    |
    | Configuration pour la gestion des équipes dans l'application
    |
    */

    // Statuts possibles pour une équipe
    'statuts' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspendu',
    ],

    // Nombre maximum de membres par équipe
    'max_members' => 20,

    // Permissions requises pour les actions
    'permissions' => [
        'create' => 'equipes.create',
        'read' => 'equipes.index',
        'update' => 'equipes.update',
        'delete' => 'equipes.delete',
    ],

    // Messages de validation
    'validation_messages' => [
        'nom' => [
            'required' => 'Le nom de l\'équipe est obligatoire.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ],
        'projet_id' => [
            'required' => 'Le projet est obligatoire.',
            'exists' => 'Le projet sélectionné n\'existe pas.',
        ],
        'users' => [
            'required' => 'Au moins un membre doit être sélectionné.',
            'array' => 'Les membres doivent être un tableau.',
            'min' => 'Au moins un membre doit être sélectionné.',
            'exists' => 'Un ou plusieurs membres sélectionnés n\'existent pas.',
        ],
    ],

    // Messages de succès
    'success_messages' => [
        'create' => 'L\'équipe a été créée avec succès !',
        'update' => 'L\'équipe a été modifiée avec succès !',
        'delete' => 'L\'équipe a été supprimée avec succès !',
    ],

    // Messages d'erreur
    'error_messages' => [
        'project_inactive' => 'Le projet sélectionné n\'existe pas ou n\'est plus actif.',
        'max_members' => 'Le nombre maximum de membres par équipe est de :max.',
    ],
];
