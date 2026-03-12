<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeField;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttributeFieldController extends Controller
{
    public function index(): View
    {
        $fields = AttributeField::with('subcategory')
            ->global()
            ->orderBy('subcategory_id')
            ->orderBy('group')
            ->orderBy('sort')
            ->get()
            ->groupBy('subcategory.name');

        return view('admin.attribute-fields.index', compact('fields'));
    }

    public function create(): View
    {
        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];

        return view('admin.attribute-fields.create', compact('subcategories', 'types', 'groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'key' => ['required', 'string', 'max:255'],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:bool,int,decimal,string,enum,multi_enum,date,json'],
            'group' => ['required', 'string', 'max:255'],
            'sort' => ['required', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'min' => ['nullable', 'numeric'],
            'max' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        AttributeField::create([
            'subcategory_id' => $data['subcategory_id'],
            'key' => $data['key'],
            'label' => $data['label_en'],
            'label_translations' => [
                'en' => $data['label_en'],
                'ar' => $data['label_ar'] ?? null,
            ],
            'type' => $data['type'],
            'required' => $request->boolean('required'),
            'searchable' => $request->boolean('searchable'),
            'facetable' => $request->boolean('facetable'),
            'promoted' => $request->boolean('promoted'),
            'group' => $data['group'],
            'sort' => $data['sort'],
            'options' => $data['options'] ?? null,
            'min' => $data['min'] ?? null,
            'max' => $data['max'] ?? null,
            'unit' => $data['unit'] ?? null,
        ]);

        return redirect()->route('admin.attribute-fields.index')->with('status', 'Field created');
    }

    public function edit(AttributeField $attributeField): View
    {
        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];

        return view('admin.attribute-fields.edit', compact('attributeField', 'subcategories', 'types', 'groups'));
    }

    public function update(Request $request, AttributeField $attributeField): RedirectResponse
    {
        $data = $request->validate([
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'key' => ['required', 'string', 'max:255'],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:bool,int,decimal,string,enum,multi_enum,date,json'],
            'group' => ['required', 'string', 'max:255'],
            'sort' => ['required', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'min' => ['nullable', 'numeric'],
            'max' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $attributeField->update([
            'subcategory_id' => $data['subcategory_id'],
            'key' => $data['key'],
            'label' => $data['label_en'],
            'label_translations' => [
                'en' => $data['label_en'],
                'ar' => $data['label_ar'] ?? null,
            ],
            'type' => $data['type'],
            'required' => $request->boolean('required'),
            'searchable' => $request->boolean('searchable'),
            'facetable' => $request->boolean('facetable'),
            'promoted' => $request->boolean('promoted'),
            'group' => $data['group'],
            'sort' => $data['sort'],
            'options' => $data['options'] ?? null,
            'min' => $data['min'] ?? null,
            'max' => $data['max'] ?? null,
            'unit' => $data['unit'] ?? null,
        ]);

        return redirect()->route('admin.attribute-fields.index')->with('status', 'Field updated');
    }

    public function destroy(AttributeField $attributeField): RedirectResponse
    {
        $attributeField->delete();

        return redirect()->route('admin.attribute-fields.index')->with('status', 'Field deleted');
    }
}

