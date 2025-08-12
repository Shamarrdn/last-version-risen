<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      if (!auth()->user()->hasRole(['admin', 'superadmin'])) {
        return redirect('/dashboard');
      }
      return $next($request);
    });
  }

  public function index()
  {
    $categories = Category::withCount('products')
      ->latest()
      ->paginate(10);

    return view('admin.categories.index', compact('categories'));
  }

  public function create()
  {
    return view('admin.categories.create');
  }

  public function show(Category $category)
  {
    $category->loadCount('products');
    $category->load('products');

    return view('admin.categories.show', compact('category'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories',
      'description' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    // Handle image upload
    if ($request->hasFile('image')) {
      $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
      $request->file('image')->storeAs('categories', $imageName, 'public');
      $validated['image'] = $imageName;
    }

    Category::create($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم إضافة التصنيف بنجاح');
  }

  public function edit(Category $category)
  {
    return view('admin.categories.edit', compact('category'));
  }

  public function update(Request $request, Category $category)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
      'description' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    // Handle image upload
    if ($request->hasFile('image')) {
      // Delete old image if exists
      if ($category->image && Storage::disk('public')->exists('categories/' . $category->image)) {
        Storage::disk('public')->delete('categories/' . $category->image);
      }

      $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
      $request->file('image')->storeAs('categories', $imageName, 'public');
      $validated['image'] = $imageName;
    }

    $category->update($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم تحديث التصنيف بنجاح');
  }

  public function destroy(Category $category)
  {
    if ($category->products()->exists()) {
      return back()->with('error', 'لا يمكن حذف التصنيف لوجود منتجات مرتبطة به');
    }

    // Delete image if exists
    if ($category->image && Storage::disk('public')->exists('categories/' . $category->image)) {
      Storage::disk('public')->delete('categories/' . $category->image);
    }

    $category->delete();

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم حذف التصنيف بنجاح');
  }
}
