<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Display a listing of the Menu.
     */
    public function index()
    {
        $menus = Menu::all();

        $formattedMenus = $menus->map(function ($menuItem) {
            return [
                'id' => $menuItem->id,
                'title' => $menuItem->title,
                'route' => $menuItem->route,
                'url' => $menuItem->url,
                'created_at' => $menuItem->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $menuItem->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return inertia('Menu', [
            'menus' => $formattedMenus,
        ]);
    }


    /**
     * Store a newly created Menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'required|string|unique:menus|max:255',
            'url' => 'nullable|string|max:255',
        ]);

        $menu = Menu::create($validated);

        activity()
            ->performedOn($menu)
            ->withProperties(['ip' => $request->ip()])
            ->log('Menu item created');

        return redirect()->route('menu.index')
            ->with('success', 'Menu created successfully.');
    }

    /**
     * Update the Menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
        ]);

        $oldValues = [
            'title' => $menu->title,
            'route' => $menu->route,
            'url' => $menu->url,
        ];

        $menu->update($validated);

        activity()
            ->performedOn($menu)
            ->withProperties([
                'old' => $oldValues,
                'new' => $validated,
                'ip' => $request->ip()
            ])
            ->log('Menu item updated');

        return redirect()->route('menu.index')
            ->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menuDetails = [
            'id' => $menu->id,
            'title' => $menu->title,
            'route' => $menu->route,
            'url' => $menu->url,
        ];


        activity()
            ->performedOn($menu)
            ->withProperties([
                'menu_details' => $menuDetails,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ])
            ->log('Menu item deleted');

        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu deleted successfully.');
    }
}
