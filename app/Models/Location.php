<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'country',
        'city',
        'zip',
        'temperature',
        'refrigerator_temperature',
        'address',
        'google_maps_url',
        'google_review_link',
        'min_wait_time',
        'max_wait_time',
        'organization_id',
        'max_tests_per_slot',
        'sameday_pcr_appointments_end_time',
        'express_pcr_max_tests_per_slot',
        'public',
        'is_visible_for_operators',
        'has_signopad',
        'has_one_laptop',
        'check_sample_given',
        'check_health_information',
        'has_qr_scanner',
        'is_awol_alert_enabled',
        'uuid',
        'loyalty_program',
        'appointments_mode',
        'slug',
        'lat',
        'lon',
        'loyalty_program',
        'default_health_authority_code',
        'health_insurance_association_code', // TODO: 01/2022 remove, not needed anymore
    ];

    protected $appends = [
        'opening_hour',
        'closing_hour',
    ];

}
