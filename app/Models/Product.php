<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'category_id',
        'name',
        'description',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   public static function getDisk():Filesystem|FilesystemAdapter
   {
      return Storage::disk('products');
   }


   public  function imageUrl(): ?string
   {
    return $this->image ? self::getDisk()->url($this->image) : null;
   }

    
}
