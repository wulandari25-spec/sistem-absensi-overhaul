<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$latest = Attendance::latest('checked_at')->first();
if ($latest) {
    $latestDate = Carbon::parse($latest->checked_at);
    $now = Carbon::now();
    $days = $latestDate->diffInDays($now);
    if ($days > 0) {
        echo "Latest date: " . $latestDate->toDateTimeString() . "\n";
        echo "Now: " . $now->toDateTimeString() . "\n";
        echo "Shifting by $days days...\n";
        
        $affected = Attendance::query()->update([
            'checked_at' => DB::raw("DATE_ADD(checked_at, INTERVAL $days DAY)"),
            'created_at' => DB::raw("DATE_ADD(created_at, INTERVAL $days DAY)"),
            'updated_at' => DB::raw("DATE_ADD(updated_at, INTERVAL $days DAY)"),
        ]);
        
        echo "Successfully shifted $affected attendance records!\n";
    } else {
        echo "Data is already up to date. Latest: " . $latestDate->toDateTimeString() . "\n";
    }
} else {
    echo "No attendance records found.\n";
}
