<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use MeiliSearch\Endpoints\Indexes;

class ProductBrowser extends Component
{
    public $category;
    public $queryFilters = [];
    public $priceRange = [
        'min' => 100,
        'max' => 100
    ];

    public function mount()
    {
        $this->queryFilters = $this->category->products->pluck('variations')
            ->flatten()
            ->groupBy('type')
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => []])
            ->toArray();

        $this->priceRange['max'] = $this->category->products->max('price');
    }

    public function render()
    {
        $search = Product::search('', function (Indexes $meilisearch, string $query, array $options) {
            $filters = collect($this->queryFilters)->filter(fn ($filter) => !empty($filter))
                ->recursive()
                ->map(function ($value, $key) {
                    return $value->map(fn ($value) => $key . ' = "' . $value . '"');
                })
                ->flatten()
                ->join(' AND ');

           $options['facets'] = ['size', 'color'];

           $options['filter'] = null;

           if ($filters) {
               $options['filter'] = $filters;
           }

           if ($this->priceRange['max']) {
               $options['filter'] .= (isset($options['filter']) ? ' AND ' : '') . 'price <= ' . $this->priceRange['max'];
           }

            return $meilisearch->search($query, $options);
        })->raw();

        $products = $this->category->products->find(collect($search['hits'])->pluck('id'));

        $maxPrice = $this->category->products->max('price');

        $this->priceRange['max'] = $this->priceRange['max'] ?: $maxPrice;

        return view('livewire.product-browser', [
            'products' => $products,
            'filters' => $search['facetDistribution'],
            'maxPrice' => $maxPrice,
        ]);
    }
}
