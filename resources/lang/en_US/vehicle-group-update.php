<?php return [
    'meta-title' => 'Bookmark-Group > Update',
    'btn-delete' => 'Delete',
    'btn-force-delete' => 'Force Delete',
    'btn-restore' => 'Restore',
    'btn-save' => 'Save',
    'btn-cancel' => 'Cancel',
    'error' => [
        'not-found' => 'Bookmark Group not found.',
        'name-required' => 'The name field is required.',
        'name-string' => 'The name must be a string.',
        'name-max' => 'The name may not be greater than 100 characters.',
        'description-string' => 'The description must be a string.',
        'description-max' => 'The description may not be greater than 255 characters.',
    ],
    'modal' => [
        'delete' => [
            'title' => 'Delete Bookmark Group',
            'message' => 'Are you sure you want to delete this Bookmark Group? This action cannot be undone.',
        ],
        'force-delete' => [
            'title' => 'Force Delete Bookmark Group',
            'message' => 'Are you sure you want to permanently delete this Bookmark Group? This action cannot be undone.',
        ],
        'restore' => [
            'title' => 'Restore Bookmark Group',
            'message' => 'Are you sure you want to restore this Bookmark Group?',
        ],
    ],
    'update' => [
        'success' => 'Bookmark Group updated successfully.',
        'error' =>[

        ],
    ],
    'delete' => [
        'success' => 'Bookmark Group deleted successfully.',
        'error' => [
            'in-use' => 'Cannot delete this Bookmark Group because it is still in use.',
            'model-not-found' => 'Delete failed: Model not found.',
            'query-error' => 'Delete failed: Database query error: :message',
            'unexpected-error' => 'Delete failed: Unexpected error: :message',
        ],
    ],
    'force-delete' => [
        'success' => 'Bookmark Group permanently deleted successfully.',
        'error' => [
            'in-use' => 'Cannot force delete this Bookmark Group because it is still in use.',
            'model-not-found' => 'Force delete failed: Model not found.',
            'query-error' => 'Force delete failed: Database query error: :message',
            'unexpected-error' => 'Force delete failed: Unexpected error: :message',
        ],
    ],
    'restore' => [
        'success' => 'Bookmark Group restored successfully.',
        'error' => [
            'model-not-found' => 'Bookmark Group not found or already deleted permanently.',
            'query-error' => 'Database error while restoring Bookmark Group.',
            'connection-error' => 'Database connection error during restore.',
            'not-authorized' => 'Not authorized to restore this Bookmark Group.',
            'validation-failed' => 'Validation failed during Bookmark Group restore.',
            'unexpected-error' => 'Unexpected error during Bookmark Group restore.',
        ],
    ],
];
