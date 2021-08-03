<?php

/*
    |--------------------------------------------------------------------------
    | Default Application Constants
    |--------------------------------------------------------------------------
    |
    */

return [

    'PAGINATION_PAGE_SIZE' => 100,
    'EXPORT_PAGE_SIZE' => 50000,
    'IS_EXPORT_IN_CHUNK' => false,
    'GOOGLE_API_KEY' => 'AIzaSyDlM_qg9kpU6Ho0o5v-JR9dKolEpPvLcT8',
    'STORAGE_UNIT_IMAGE_PATH' => '/uploads/storage_unit/',
    'GENERAL_IMAGE_PATH' => 'image/',
    'PRODUCT_IMAGE_PATH' => '/uploads/product/',
    'GALLERY_IMAGE_PATH' => '/uploads/gallery/',
    'USER_IMAGE_PATH' => '/uploads/user/',
    'USER_WISHLIST_LIMIT' => 3,
    'GOLD_MEMBER_AMOUNT' => '50',
    'MEDIA_IMAGE_PATH' => '/uploads/media/',
    'MEDIA_FILE_PATH' => 'app/uploads/user/',
    'APP_SALT' => 'this is salt string %$#as&*12.xzs! for abc',
    'UNAUTH_ROUTES' => [],
    'DIR_ADMIN' => 'admin',
    'BILLING' => [
        'PROBATION_DAY' => 23
    ],
    'LEAD_DEFAULT_COLUMNS' => ['lead_name', 'lead_type','lead_status','address','foreclosure_date','admin_notes', 'city', 'county', 'state', 'zip_code', 'is_expired'],
    'SPECIAL_CHARACTERS' => [
        'IGNORE' =>['/', '|', '%'],
        'REPLACE' =>['_'],
    ],
    'LEAD_IGNORE_COLUMNS' => ['lead_status','lead_type', 'is_expired'],
    'LEAD_TITLE_DISPLAY' => 'Homeowner Name',
    'SUB_ADMIN_QUOTA' => 3,
    //title, lead_name','lead_type','lead_status', 'address', 'city', 'county', 'state', 'zip_code'


];
