<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceCategory extends Model
{
    protected $table = 'woocommerce_categories';

    protected $fillable = ['woocommerce_id', 'name', 'slug', 'parent_woocommerce_id'];
}
