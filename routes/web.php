<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'Config cache cleared';
});

Route::get('/run-queue', function () {
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $connection = env('QUEUE_CONNECTION', 'redis');
    $output = "Queue Connection: {$connection}\n";

    // Loop up to 10 iterations to ensure both parent campaign jobs and generated child email jobs are fully processed
    for ($i = 1; $i <= 10; $i++) {
        Artisan::call('queue:work', [
            'connection' => $connection,
            '--queue' => 'business-campaigns,business-emails,default',
            '--stop-when-empty' => true,
        ]);
        $stepOutput = trim(Artisan::output());
        if (!empty($stepOutput)) {
            $output .= "--- Iteration {$i} ---\n" . $stepOutput . "\n";
        } else {
            break;
        }
    }

    $campaigns = \App\Models\BusinessEmailCampaign::latest()->take(5)->get();
    $output .= "\n--- Latest 5 Campaigns Status ---\n";
    foreach ($campaigns as $c) {
        $output .= "ID #{$c->id} | Name: {$c->name} | Status: {$c->status} | Total: {$c->total_count} | Skipped: {$c->skipped_count} | Queued: {$c->queued_count} | Sent: {$c->sent_count} | Delivered: {$c->delivered_count} | Failed: {$c->failed_count}\n";
    }

    return '<pre>' . htmlspecialchars($output) . '</pre>';
});

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/disclaimer', function () {
    return view('disclaimer');
})->name('disclaimer');

Route::get('/privacy-policy', function () {
    return view('privacyPolicy');
})->name('privacyPolicy');


Route::get('/refund-policy', function () {
    return view('refundPolicy');
})->name('refundPolicy');


Route::get('/term-condition', function () {
    return view('termConditions');
})->name('termCondition');

Route::get('/contact-us', function () {
    return view('info.contactUs');
})->name('contactUs');

Route::get('/about-us', function () {
    return view('info.aboutUs');
});

Route::get('/charges', function () {
    return view('info.charges');
});

Route::get('/msme-scheme', function () {
    return view('info.scheme');
});

Route::get('/checkout', function () {
    return view('info.checkout');
});

// Route::get('/checkout', function () {
//     return view('info.checkout');
// });

Route::get('/checkout/{id}/{msme_type}', [App\Http\Controllers\HomeController::class, 'checkout'])->name('checkout');

Route::get('/msme-scheme', [App\Http\Controllers\HomeController::class, 'msmeScheme'])->name('msmeScheme');

Route::get('/news', function () {
    return view('info.news');
})->name('news');

// Route::get('/registration', function () {
//     return view('info.registration');
// });

// Route::get('/clear-cache', function() {
//     Artisan::call('migrate --path=database/migrations/2024_04_02_112234_create_registers_table.php');
// });

Route::post('/checkCoupon', [App\Http\Controllers\HomeController::class, 'checkCoupon'])->name('checkCoupon');

Route::post('/save-contact-us', [App\Http\Controllers\HomeController::class, 'saveContactUs'])->name('saveContactUs');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');

Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::get('/user-profile', [App\Http\Controllers\AuthController::class, 'userProfile'])->name('userProfile');

Route::get('/edit-profile', [App\Http\Controllers\AuthController::class, 'editProfile'])->name('editProfile');

Route::post('/save-register', [App\Http\Controllers\AuthController::class, 'saveRegister'])->name('saveRegister');

Route::post('/update-register', [App\Http\Controllers\AuthController::class, 'updateRegister'])->name('updateRegister');

Route::post('/save-login', [App\Http\Controllers\AuthController::class, 'saveLogin'])->name('saveLogin');

Route::post('/get-pincode-data', [App\Http\Controllers\HomeController::class, 'getPincodeData'])->name('getPincodeData');

Route::get('/category/{productservice}', [App\Http\Controllers\CategoryController::class, 'categoryListingPage'])->name('categoryListingPage');

Route::get('/user-connections', [App\Http\Controllers\HomeController::class, 'userConnections'])->name('userConnections');
Route::get('/request-connection/{productservice}', [App\Http\Controllers\HomeController::class, 'requestConnection'])->name('requestConnection');

Route::get('/accept-connection/{id}/{discount?}', [App\Http\Controllers\HomeController::class, 'acceptConnection'])->name('acceptConnection');


Route::get('/decline-connection/{productservice}', [App\Http\Controllers\HomeController::class, 'declineConnection'])->name('declineConnection');

Route::post('/payment', [App\Http\Controllers\HomeController::class, 'payment'])->name('payment');


Route::get('/notifications', [App\Http\Controllers\HomeController::class, 'notifications'])->name('notifications');

Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'forgotPassword'])->name('forgotPassword');

Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password/{token}', [App\Http\Controllers\AuthController::class, 'forgotPasswordToken'])->name('forgotPasswordToken');

Route::post('/submit-forgot-password', [App\Http\Controllers\AuthController::class, 'submitforgotPassword'])->name('submitforgotPassword');

Route::post('/submit-forgot-password-token', [App\Http\Controllers\AuthController::class, 'submitforgotPasswordToken'])->name('submitforgotPasswordToken');
