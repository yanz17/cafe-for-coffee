<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class OrderItemController extends Controller
{
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(\App\Models\Menu::class);
    }

    /**
     * Proxy to Eloquent\Model::belongsTo so these relationship methods don't raise "unknown method" errors.
     *
     * Note: relationship methods should normally be defined on Eloquent models (e.g. App\Models\OrderItem).
     * This proxy is a compatibility shim to avoid the undefined method error in the controller file.
     *
     * @param  string|object  $related
     * @param  string|null    $foreignKey
     * @param  string|null    $ownerKey
     * @param  string|null    $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null): BelongsTo
    {
        $instance = new class extends EloquentModel {
        };
        return $instance->belongsTo($related, $foreignKey, $ownerKey, $relation);
    }
}
