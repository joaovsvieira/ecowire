<?php

namespace App\Models;

use App\Models\Scopes\LiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable;

    protected $fillable = [
        'name',
        'slug',
        'price',
    ];

    public static function booted()
    {
        static::addGlobalScope(new LiveScope());
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb200x200')
                ->fit(Manipulations::FIT_CROP, 200, 200);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useFallbackUrl(url('/storage/no_image.png'));
    }

    public function formattedPrice()
    {
        return money($this->price);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function toSearchableArray()
    {
        return array_merge([
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'category_ids' => $this->load('categories')->categories->pluck('id')->toArray(),
        ], $this->variations->groupBy('type')
            ->mapWithKeys(fn ($variation, $key) => [
                $key => $variation->pluck('name')
            ])
            ->toArray()
        );
    }
}
