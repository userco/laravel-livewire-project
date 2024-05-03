<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LocationTestTypeAppointmentDisabled extends Model
{
    use HasFactory;
    public $table = 'ltt_appointments_disabled';

    protected $fillable = [
        'location_id',
        'test_type_id',
        'day_of_week',
        'from',
        'to',
    ];

    protected static string $logName = 'system';

    protected static array $logAttributes = [
        'location_id',
        'test_type_id',
        'day_of_week',
        'from',
        'to',
    ];

    protected static bool $logOnlyDirty = true;

    protected static bool $submitEmptyLogs = false;

    public function getRangeAttribute(): string
    {
        return "{$this->from} - {$this->to}";
    }
}
