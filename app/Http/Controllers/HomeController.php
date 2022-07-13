<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Application|Factory|View
     */
    public function __invoke(): View|Factory|Application
    {
        $categories = Category::tree()->get()->toTree();

        return view('home', [
            'categories' => $categories,
        ]);
    }
}
