<?php

namespace App\Models;

use App\Services\Prices\TestTypePriceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use UnexpectedValueException;

class TestType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'public_name',
        // 'price',
        'default_price',
        'test_type',
        'invoice_label',
        'email_label_en',
        'email_label_de',
        'operator_label',
        'detection_method',
        'sort',
        'image',
        'description_bullet_points', // string[]
        'color_code',
        'baercode_test_type',
        'cwa_mode',
        'group',
    ];


}
