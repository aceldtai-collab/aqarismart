<?php

namespace App\Http\Controllers;

use App\Models\AttributeField;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class AttributeFieldController extends Controller
{
    public function index()
    {
        $fields = AttributeField::with('subcategory')
            ->orderBy('subcategory_id')
            ->orderBy('group')
            ->orderBy('sort')
            ->get()
            ->groupBy('subcategory.name');

        return view('attribute-fields.index', compact('fields'));
    }

    public function create()
    {
        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];

        return view('attribute-fields.create', compact('subcategories', 'types', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'key' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'label_ar' => 'nullable|string|max:255',
            'type' => 'required|in:bool,int,decimal,string,enum,multi_enum,date,json',
            'group' => 'required|string|max:255',
            'sort' => 'required|integer|min:0',
            'options' => 'nullable|array',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        AttributeField::create([
            'subcategory_id' => $request->subcategory_id,
            'key' => $request->key,
            'label' => $request->label_en,
            'label_translations' => [
                'en' => $request->label_en,
                'ar' => $request->label_ar,
            ],
            'type' => $request->type,
            'required' => $request->boolean('required'),
            'searchable' => $request->boolean('searchable'),
            'facetable' => $request->boolean('facetable'),
            'promoted' => $request->boolean('promoted'),
            'group' => $request->group,
            'sort' => $request->sort,
            'options' => $request->options,
            'min' => $request->min,
            'max' => $request->max,
            'unit' => $request->unit,
        ]);

        return redirect()->route('attribute-fields.index')->with('success', 'Field created successfully');
    }

    public function show(AttributeField $attributeField)
    {
        return redirect()->route('attribute-fields.edit', $attributeField);
    }

    public function edit(AttributeField $attribute_field)
    {
        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];
        $attributeField = $attribute_field;
        return view('attribute-fields.edit', compact('attributeField', 'subcategories', 'types', 'groups'));
    }

    public function update(Request $request, AttributeField $attribute_field)
    {
        
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'key' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'label_ar' => 'nullable|string|max:255',
            'type' => 'required|in:bool,int,decimal,string,enum,multi_enum,date,json',
            'group' => 'required|string|max:255',
            'sort' => 'required|integer|min:0',
            'options' => 'nullable|array',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $attributeField->update([
            'subcategory_id' => $request->subcategory_id,
            'key' => $request->key,
            'label' => $request->label_en,
            'label_translations' => [
                'en' => $request->label_en,
                'ar' => $request->label_ar,
            ],
            'type' => $request->type,
            'required' => $request->boolean('required'),
            'searchable' => $request->boolean('searchable'),
            'facetable' => $request->boolean('facetable'),
            'promoted' => $request->boolean('promoted'),
            'group' => $request->group,
            'sort' => $request->sort,
            'options' => $request->options,
            'min' => $request->min,
            'max' => $request->max,
            'unit' => $request->unit,
        ]);

        return redirect()->route('attribute-fields.index')->with('success', 'Field updated successfully');
    }

    public function destroy(AttributeField $attributeField)
    {
        $attributeField->delete();
        return redirect()->route('attribute-fields.index')->with('success', 'Field deleted successfully');
    }

    public function bySubcategory($subcategoryId, $id)
    {
        $fields = AttributeField::where('subcategory_id', $id)->get();
        return response()->json($fields);
    }
}