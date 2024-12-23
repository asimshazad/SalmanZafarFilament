<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/** Artisan routes added  */
Route::get('generate', function (){
    Artisan::call('storage:link');
    echo 'Storage link created';
});

Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    echo 'config:cache';
});
Route::get('/route-cache', function() {
    Artisan::call('route:cache');
    echo 'route:cache';
});
Route::get('/view-cache', function() {
    Artisan::call('view:cache');
    echo 'view:cache';
});

Route::get('/cache-clear', function() {
    Artisan::call('cache:clear');
    echo 'cache:clear';
});

Route::get('/route-clear', function() {
    Artisan::call('route:clear');
    echo 'route:clear';
});
Route::get('/config-clear', function() {
    Artisan::call('config:clear');
    echo 'config:clear';
});
Route::get('/view-clear', function() {
    Artisan::call('view:clear');
    echo 'view:clear';
});
Route::get('/clear-compiled', function() {
    Artisan::call('clear-compiled');
    echo 'clear-compiled';
});
Route::get('/optimize-clear', function() {
    Artisan::call('optimize:clear');
    echo 'Optimized cleared';
});

// Clear spatie permissions
Route::get('/permission-clear', function() {
    Artisan::call('cache:forget spatie.permission.cache');
    Artisan::call('optimize:clear');
    echo 'View cache cleared';
});

Route::get('/migrate', function() {
    Artisan::call('migrate');
    return 'Tables created successfully';
});

// Route::get('/migrate-fresh', function() {
//     Artisan::call('migrate:fresh');
//     echo 'Tables re-created successfully';
// });
// Route::get('/db-seed', function() {
//     Artisan::call('db:seed');
//     echo 'Seeder data added to tables';
// });

// Route::get('/db-seed/{className}', function ($className){
//     Artisan::call('db:seed --class=' . $className);
//     echo $className . ' data added to tables';
// });

Route::get('test', function()
{
    Mail::raw('This is the content of mail body', function($message) {
        $message -> from('it@globaledulink.co.uk', 'John Smith');
        $message -> to('hassan982g@gmail.com');
        $message -> subject('Test Email');
    });
    dd('Sent');
});

/** Artisan routes end  */