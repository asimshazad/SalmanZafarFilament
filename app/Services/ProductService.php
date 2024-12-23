<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * Handle creating event for Product.
     *
     * @param Product $product
     * @throws ValidationException
     */
    public function handleCreating(Product $product)
    {
        $slug = Str::slug($product->name);

        $this->validateSlug($slug);

        $product->slug = $slug;
        $product->created_by_id = auth()->id();
    }

    /**
     * Handle updating event for Product.
     *
     * @param Product $product
     * @throws ValidationException
     */
    public function handleUpdating(Product $product)
    {
        if ($product->isDirty('name')) {
            $slug = Str::slug($product->name);

            $this->validateSlug($slug, $product->id);

            $product->slug = $slug;
        }
    }

    /**
     * Validate slug uniqueness.
     *
     * @param string $slug
     * @param int|null $excludeId
     * @throws ValidationException
     */
    protected function validateSlug(string $slug, int $excludeId = null)
    {
        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'The slug must be unique.',
            ]);
        }
    }
}
