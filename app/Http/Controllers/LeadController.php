<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use \App\Exceptions\LeadNotFoundException;
use \App\Exceptions\InvalidDataException;
use \App\Exceptions\DuplicateLeadException;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Lead::query();
            if ($request->has('from') && $request->has('to')) {
                $from = date('Y-m-d 00:00:00', strtotime($request->input('from')));
                $to = date('Y-m-d 23:59:59', strtotime($request->input('to')));
                $query->whereBetween('created_at', [$from, $to]);
            }
            $leads = $query->get();
            return response()->json(['data' => $leads]);
        } catch (\Exception $e) {
            throw new InvalidDataException($e->getMessage(), 500, 'LeadController');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email',
                'leadSource' => 'nullable|string|max:100',
                'leadStatus' => 'required|string|max:50',
            ]);

            if (Lead::where('email', $validated['email'])->exists()) {
                throw new DuplicateLeadException('Duplicate lead: email already exists', 409, 'LeadController');
            }

            $lead = Lead::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'lead_source' => $validated['leadSource'] ?? null,
                'lead_status' => $validated['leadStatus'],
            ]);

            return response()->json([
                'message' => 'Lead created successfully',
                'data' => $lead
            ], 201);
        } catch (DuplicateLeadException $e) {
            return $e->render();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new InvalidDataException($e->getMessage(), 422, 'LeadController');
        } catch (\Exception $e) {
            throw new InvalidDataException($e->getMessage(), 500, 'LeadController');
        }
    }

    public function show($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                throw new LeadNotFoundException('Lead not found', 404, 'LeadController');
            }
            return response()->json($lead);
        } catch (LeadNotFoundException $e) {
            return $e->render();
        } catch (\Exception $e) {
            throw new InvalidDataException($e->getMessage(), 500, 'LeadController');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                throw new LeadNotFoundException('Lead not found', 404, 'LeadController');
            }
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:leads,email,' . $id,
                'leadSource' => 'sometimes|nullable|string|max:100',
                'leadStatus' => 'sometimes|required|string|max:50',
            ]);
            $lead->update([
                'name' => $validated['name'] ?? $lead->name,
                'email' => $validated['email'] ?? $lead->email,
                'lead_source' => $validated['leadSource'] ?? $lead->lead_source,
                'lead_status' => $validated['leadStatus'] ?? $lead->lead_status,
            ]);
            return response()->json([
                'message' => 'Lead updated successfully',
                'data' => $lead
            ]);
        } catch (LeadNotFoundException $e) {
            return $e->render();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new InvalidDataException($e->getMessage(), 422, 'LeadController');
        } catch (\Exception $e) {
            throw new InvalidDataException($e->getMessage(), 500, 'LeadController');
        }
    }

    public function destroy($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                throw new LeadNotFoundException('Lead not found', 404, 'LeadController');
            }
            $lead->delete();
            return response()->json([
                'message' => 'Lead deleted successfully'
            ], 200);
        } catch (LeadNotFoundException $e) {
            return $e->render();
        } catch (\Exception $e) {
            throw new InvalidDataException($e->getMessage(), 500, 'LeadController');
        }
    }

}
