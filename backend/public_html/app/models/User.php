<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @author Sam Hoque
 */
class User extends Model {
    protected $primaryKey = 'id';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'pass_hash'
    ];
}