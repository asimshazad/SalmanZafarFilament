<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'email_templates';
    protected $fillable = [
        'key',
        'email_subject',
        'from_email',
        'from_name',
        'cc_senders',
        'content',
        'status',
    ];

    protected $casts = [
        'cc_senders' => 'array',
        'status' => 'boolean',
    ];
}
