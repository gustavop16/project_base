<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VesselRequest;
use App\Http\Resources\VesselResource;
use App\Models\Vessel;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    public function __construct(private Vessel $vessel_model) {}

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // ?all=1 é usado pelo formulário de criação de certificado (todos os navios)
        if ($user->hasRole('admin') || $request->boolean('all')) {
            $vessels = $this->vessel_model->get();
        } else {
            $vessels = $this->vessel_model->where('created_by', $user->id)->get();
        }

        return VesselResource::collection($vessels);
    }

    public function store(VesselRequest $request)
    {
        $vessel = $this->vessel_model->create(
            array_merge($request->validated(), ['created_by' => auth()->id()])
        );
        return new VesselResource($vessel);
    }

    public function show(Vessel $vessel)
    {
        return new VesselResource($vessel);
    }

    public function update(VesselRequest $request, Vessel $vessel)
    {
        $this->authorizeVesselOwnership($vessel);

        $vessel->update($request->validated());
        return new VesselResource($vessel);
    }

    public function destroy(Vessel $vessel)
    {
        $this->authorizeVesselOwnership($vessel);

        $vessel->delete();
        return response()->json(['message' => 'ok'], 200);
    }

    private function authorizeVesselOwnership(Vessel $vessel): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user->hasRole('admin') && $vessel->created_by !== $user->id) {
            abort(403, 'Acesso negado a este navio.');
        }
    }
}
