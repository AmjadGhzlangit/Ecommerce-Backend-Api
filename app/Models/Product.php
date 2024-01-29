<?php

namespace App\Models;

use BinaryCats\Sku\Concerns\SkuOptions;
use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, HasSlug,HasSku;

    protected $fillable =
    [
        'id',
        'category_id',
        'name',
        'description',
        'image',
        'sku',
        'qty',
        'price',
        'slug',
        'currency',

    ];

    public static function getDisk(): Filesystem|FilesystemAdapter
    {
        return Storage::disk('products');
    }

    public function skuOptions(): SkuOptions
    {
        return SkuOptions::make()
            //->from(['label', 'another_field'])
            //->target('arbitrary_sku_field_name')
            ->using('_')
            ->forceUnique(false)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingSeparator('-');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function imageUrl(): ?string
    {
        return $this->image ? self::getDisk()->url($this->image) : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
