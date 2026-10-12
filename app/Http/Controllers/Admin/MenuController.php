<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $query = Menu::with(['category', 'variants'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $menus = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['nullable', 'string', 'max:100'],
            'variants.*.extra_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'category_id.required' => 'Pilih kategori menu.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_available' => $request->boolean('is_available', true),
            'image' => $imagePath,
        ]);

        // Simpan varian jika diisi
        if (! empty($request->variants)) {
            foreach ($request->variants as $variant) {
                if (! empty($variant['name'])) {
                    $menu->variants()->create([
                        'name' => trim($variant['name']),
                        'extra_price' => $variant['extra_price'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$menu->name}' berhasil ditambahkan.");
    }

    public function edit(Menu $menu): View
    {
        $menu->load(['category', 'variants']);
        $categories = Category::orderBy('name')->get();

        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.name' => ['nullable', 'string', 'max:100'],
            'variants.*.extra_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menus', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = null;
        }

        $menu->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
        ]);

        // Sinkronisasi varian
        $existingVariantIds = [];
        if (! empty($request->variants)) {
            foreach ($request->variants as $variantData) {
                if (! empty($variantData['name'])) {
                    if (! empty($variantData['id'])) {
                        $variant = MenuVariant::where('menu_id', $menu->id)->find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'name' => trim($variantData['name']),
                                'extra_price' => $variantData['extra_price'] ?? 0,
                            ]);
                            $existingVariantIds[] = $variant->id;
                        }
                    } else {
                        $newVariant = $menu->variants()->create([
                            'name' => trim($variantData['name']),
                            'extra_price' => $variantData['extra_price'] ?? 0,
                        ]);
                        $existingVariantIds[] = $newVariant->id;
                    }
                }
            }
        }

        // Hapus varian yang dihapus oleh user pada form
        $menu->variants()->whereNotIn('id', $existingVariantIds)->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $name = $menu->name;

        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu '{$name}' berhasil dihapus.");
    }
}
