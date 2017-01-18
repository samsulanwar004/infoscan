<?php
    namespace App;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Model;

    class Merchant extends Model {
        use SoftDeletes;
        protected $table = 'report_templates';
        protected $fillable = [
            'name',
            'content'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($offer) {
            $offer->users()->delete();
        });
    }
}
