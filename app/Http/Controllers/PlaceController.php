<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Place;
use Illuminate\Support\Facades\Storage;


class PlaceController extends Controller
{
    // ذخیره یک مکان جدید
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|max:2048',
        ]);

        $place = new Place();
        $place->title = $validatedData['title'];
        $place->description = $validatedData['description'];
        $place->lat = $validatedData['lat'];
        $place->lng = $validatedData['lng'];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('places', 'public');
            $place->image = $imagePath;
        }

        $place->save();

        return response()->json([
            'message' => 'مکان با موفقیت ثبت شد',
            'place' => $place
        ], 201);
    }

    // حذف یک مکان
    public function destroy($id)
    {
        $place = Place::findOrFail($id);

        // حذف تصویر از سرور
        if ($place->image) {
            Storage::disk('public')->delete($place->image);
        }

        $place->delete();

        return response()->json([
            'message' => 'مکان با موفقیت حذف شد'
        ]);
    }
}
