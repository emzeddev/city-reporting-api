<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Routing\Controllers\HasMiddleware;
use \App\Http\Middleware\AuthTokenMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportsController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(AuthTokenMiddleware::class)
        ];
    }

    public function addReport(Request $request) {
       // اعتبارسنجی داده‌ها
       $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|max:2048', // آپلود تصویر اختیاری
            'status' => 'nullable|in:pending,in_progress,completed'
        ]);

        // if($validatedData->fails()) {
        //     return response()->json(['error' => 'Validation failed'], 422);
        // }

         // ذخیره گزارش
        $report = new Report();
        $report->title = $validatedData['title'];
        $report->description = $validatedData['description'];
        $report->category = $validatedData['category'];
        $report->latitude = $validatedData['lat'];
        $report->longitude	 = $validatedData['lng'];
        $report->status = $validatedData['status'] ?? 'pending'; // مقدار پیش‌فرض

        $report->user_id = auth()->user()->id;


        // ذخیره تصویر در صورت وجود
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
            $report->image = $imagePath;
        }

        $report->save();

        return response()->json([
            'message' => 'گزارش با موفقیت ثبت شد',
            'report' => $report
        ], 201);
    }

    public function getReportsList(Request $request) {
        return response()->json([
            "status" => "success",
            "data" => Report::orderBy("id" , "DESC")->get()
        ]);
    }
}
