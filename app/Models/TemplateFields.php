<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateFields extends Model
{
    protected $table = "template_fields";
    public $timestamps = false;

    public static function updateOrderBy($template_id, $field, $order_by)
    {
        \DB::statement("UPDATE template_fields SET order_by = $order_by WHERE template_id = $template_id AND field = '$field'");
        return 1;
    }

}
