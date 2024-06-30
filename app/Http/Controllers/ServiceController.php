<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::find($id);
        return response()->json($service);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
            'description' => 'nullable|string',
            'duration' => 'required|integer',
            'is_available' => 'required|boolean',
            'price_per_unit' => 'required|numeric',
            'unit_type' => 'required|string',
            'image' => 'nullable|image|max:2048' // Ảnh không bắt buộc, chỉ chấp nhận file ảnh, kích thước tối đa 2MB
        ]);
    
        $data = $request->only(['service_name', 'description', 'duration', 'is_available', 'price_per_unit', 'unit_type']);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/services');
            $data['image_url'] = Storage::url($path);
        }
    
        $service = Service::create($data);
        return response()->json($service, 201);
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string',
            'description' => 'nullable|string',
            'duration' => 'required|integer',
            'is_available' => 'required|boolean',
            'price_per_unit' => 'required|numeric',
            'unit_type' => 'required|string',
            'image' => 'nullable|image|max:2048' // Ảnh không bắt buộc, chỉ chấp nhận file ảnh, kích thước tối đa 2MB
        ]);
    
        $service = Service::find($id);
        $data = $request->only(['service_name', 'description', 'duration', 'is_available', 'price_per_unit', 'unit_type']);
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/services');
            $data['image_url'] = Storage::url($path);
        }
    
        $service->update($data);
        return response()->json($service, 200);
    }

    public function destroy($id)
    {
        Service::destroy($id);
        return response()->json(null, 204);
    }
}
