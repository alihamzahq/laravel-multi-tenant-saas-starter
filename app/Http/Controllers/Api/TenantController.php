<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;

/**
 * Central API Tenant Controller.
 *
 * Handles CRUD operations for tenant management via API.
 */
class TenantController extends Controller
{
    use ApiResponse;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private TenantService $tenantService
    ) {}

    /**
     * Display a listing of tenants.
     */
    public function index(): JsonResponse
    {
        $tenants = Tenant::with('domains')
            ->latest()
            ->paginate(15);

        return $this->paginated(
            TenantResource::collection($tenants),
            'Tenants retrieved successfully'
        );
    }

    /**
     * Store a newly created tenant.
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantService->createTenant($request->validated());

        return $this->created(
            new TenantResource($tenant),
            'Tenant created successfully'
        );
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->load('domains');

        return $this->success(
            new TenantResource($tenant),
            'Tenant retrieved successfully'
        );
    }

    /**
     * Update the specified tenant.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): JsonResponse
    {
        $tenant = $this->tenantService->updateTenant($tenant, $request->validated());

        return $this->success(
            new TenantResource($tenant),
            'Tenant updated successfully'
        );
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $this->tenantService->deleteTenant($tenant);

        return $this->noContent('Tenant deleted successfully');
    }

    /**
     * Toggle tenant active status.
     */
    public function toggleStatus(Tenant $tenant): JsonResponse
    {
        $tenant = $this->tenantService->toggleStatus($tenant);

        return $this->success(
            new TenantResource($tenant),
            'Tenant status updated successfully'
        );
    }
}
