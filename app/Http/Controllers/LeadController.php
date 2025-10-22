<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $leads = $query->paginate(10);
        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'lead_status' => 'required|string|max:50',
            'lead_source' => 'nullable|string|max:100',
        ]);

        $lead = Lead::create($validated);
        
        return response()->json([
            'message' => 'Lead created successfully',
            'data' => $lead
        ], 201);
    }

    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead);
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:leads,email,' . $id,
            'lead_status' => 'sometimes|required|string|max:50',
            'lead_source' => 'sometimes|nullable|string|max:100',
        ]);

        $lead->update($validated);
        
        return response()->json([
            'message' => 'Lead updated successfully',
            'data' => $lead
        ]);
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();
        
        return response()->json([
            'message' => 'Lead deleted successfully'
        ], 200);
    }
    
}
