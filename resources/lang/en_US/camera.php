<?php

return [
    'name' => 'Name',
    'camera-setting' => 'Camera Settings',

    'camera-setting-meta-title' => 'Camera Settings',
    'description' => 'Description',
    'model' => 'Model',
    'serial' => 'Serial',
    'uri' => 'URI',
    'location' => 'Location',
    'resolution' => 'Resolution',
    'actions' => 'Actions',
    'update' => 'Update',
    'create' => 'Camera Create',
    'no_cameras' => 'No cameras available.',
    'not_supported' => 'Camera support is not enabled for this device.',
    'update-success' => 'Camera updated successfully.',
    'create-success' => 'Camera created successfully.',
    'camera_details' => 'Camera Details',
    'video_stream' => 'Original live',
    'error' => [
        'not_found' => 'Camera with ID :id not found.',
        'unauthorized' => 'You are not authorized to update this camera.',
        'invalid_maximum' => 'The maximum number of cameras must be at least 1.',
        'creation_failed' => 'Failed to create cameras: :message',
        'exceeds_maximum' => 'Cannot create more cameras. Maximum limit is :maximum.',
        'name_required' => 'Camera name is required.',
        'invalid_uri' => 'The URI format is invalid.',
        'uri_required' => 'The URI is required.',
        'serial_required' => 'The serial number is required.',
        'already_exists' => 'A camera with this name or serial number already exists.',
        'serial_exists' => "Serial ':serial' already exists for another device.",
    ],
    // Delete modal messages
    'delete-modal' => [
        'title' => 'Delete Camera: :name',
        'message' => 'Are you sure you want to delete the camera ":name"? This action cannot be undone.',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
    ],
    'delete' => 'Delete',

];
