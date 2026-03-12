<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categoryId = $request->integer('category_id') ?: null;
        $query = Subcategory::with('category')->orderBy('sort_order')->orderBy('name');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        $subcategories = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();
        return view('admin.subcategories.index', compact('subcategories', 'categories', 'categoryId'));
    }

    public function create(): View
    {
        $subcategory = new Subcategory(['is_active' => true, 'sort_order' => 0]);
        $categories = Category::orderBy('name')->get();
        return view('admin.subcategories.create', compact('subcategory', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required','array'],
            'name.en' => ['required','string','max:255'],
            'name.ar' => ['nullable','string','max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable','array'],
            'description.en' => ['nullable','string'],
            'description.ar' => ['nullable','string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($data['slug'])) {
            $base = $data['name']['en'] ?? array_values($data['name'])[0] ?? '';
            $data['slug'] = Str::slug($base);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Ensure uniqueness per category
        if (Subcategory::where('category_id', $data['category_id'])->where('slug', $data['slug'])->exists()) {
            return back()->withErrors(['slug' => 'Slug already used in this category.'])->withInput();
        }

        Subcategory::create($data);
        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory created');
    }

    public function edit(Subcategory $subcategory): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required','array'],
            'name.en' => ['required','string','max:255'],
            'name.ar' => ['nullable','string','max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable','array'],
            'description.en' => ['nullable','string'],
            'description.ar' => ['nullable','string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($data['slug'])) {
            $base = $data['name']['en'] ?? array_values($data['name'])[0] ?? '';
            $data['slug'] = Str::slug($base);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if (Subcategory::where('category_id', $data['category_id'])->where('slug', $data['slug'])->where('id', '!=', $subcategory->id)->exists()) {
            return back()->withErrors(['slug' => 'Slug already used in this category.'])->withInput();
        }

        $subcategory->update($data);
        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory updated');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $subcategory->delete();
        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory deleted');
    }
}
