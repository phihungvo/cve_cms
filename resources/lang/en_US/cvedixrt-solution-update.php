<?php return [
    'meta-title' => 'Cvedixrt-Solution > Update',
    'btn-delete' => 'Delete',
    'btn-force-delete' => 'Force Delete',
    'btn-restore' => 'Restore',
    'btn-save' => 'Save',
    'btn-cancel' => 'Cancel',
    'error' => [
        'not-found' => 'Cvedixrt Solution Update not found.',
        'name-required' => 'The name field is required.',
        'name-string' => 'The name must be a string.',
        'name-max' => 'The name may not be greater than 100 characters.',
        'description-string' => 'The description must be a string.',
        'description-max' => 'The description may not be greater than 255 characters.',
    ],
    'modal' => [
        'delete' => [
            'title' => 'Delete Cvedixrt Solution',
            'message' => 'Are you sure you want to delete this Cvedixrt Solution? This action cannot be undone.',
        ],
        'force-delete' => [
            'title' => 'Force Delete Cvedixrt Solution',
            'message' => 'Are you sure you want to permanently delete this Cvedixrt Solution? This action cannot be undone.',
        ],
        'restore' => [
            'title' => 'Restore Cvedixrt Solution',
            'message' => 'Are you sure you want to restore this Cvedixrt Solution?',
        ],
    ],
    'update' => [
        'success' => 'Cvedixrt Solution updated successfully.',
        'error' =>[

        ],
    ],
    'delete' => [
        'success' => 'Cvedixrt Solution deleted successfully.',
        'error' => [
            'in-use' => 'Cannot delete this Cvedixrt Solution because it is still in use.',
            'model-not-found' => 'Delete failed: Model not found.',
            'query-error' => 'Delete failed: Database query error: :message',
            'unexpected-error' => 'Delete failed: Unexpected error: :message',
        ],
    ],
    'force-delete' => [
        'success' => 'Cvedixrt Solution permanently deleted successfully.',
        'error' => [
            'in-use' => 'Cannot force delete this Cvedixrt Solution because it is still in use.',
            'model-not-found' => 'Force delete failed: Model not found.',
            'query-error' => 'Force delete failed: Database query error: :message',
            'unexpected-error' => 'Force delete failed: Unexpected error: :message',
        ],
    ],
    'restore' => [
        'success' => 'Cvedixrt Solution restored successfully.',
        'error' => [
            'model-not-found' => 'Cvedixrt Solution not found or already deleted permanently.',
            'query-error' => 'Database error while restoring Cvedixrt Solution.',
            'connection-error' => 'Database connection error during restore.',
            'not-authorized' => 'Not authorized to restore this Cvedixrt Solution.',
            'validation-failed' => 'Validation failed during Cvedixrt Solution restore.',
            'unexpected-error' => 'Unexpected error during Cvedixrt Solution restore.',
        ],
    ],
];
