<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;

/**
 * SystemLogs Component
 * 
 * Livewire component for displaying and filtering system audit logs.
 * Provides filtering by action, user, and search functionality.
 */
class SystemLogs extends Component
{
    use WithPagination;

    public string $filterAction = '';
    public string $filterUser = '';
    public string $search = '';

    /**
     * Query string parameters.
     *
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'filterAction' => ['except' => ''],
        'filterUser' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    /**
     * Reset pagination when search is updated.
     *
     * @return void
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when action filter is updated.
     *
     * @return void
     */
    public function updatingFilterAction(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when user filter is updated.
     *
     * @return void
     */
    public function updatingFilterUser(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters and reset pagination.
     *
     * @return void
     */
    public function limpiarFiltros(): void
    {
        $this->filterAction = '';
        $this->filterUser = '';
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Render the component with filtered logs.
     *
     * @return View
     */
    public function render(): View
    {
        $query = AuditTrail::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($this->filterAction !== '') {
            $query->where('action', $this->filterAction);
        }

        // Filter by user
        if ($this->filterUser !== '') {
            $query->where('user_id', (int) $this->filterUser);
        }

        // Search in model_type and action
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('model_type', 'like', '%' . $this->search . '%')
                  ->orWhere('action', 'like', '%' . $this->search . '%');
            });
        }

        /** @var LengthAwarePaginator $logs */
        $logs = $query->paginate(20);
        
        // Get unique users for filter
        /** @var Collection<int, User> $usuarios */
        $usuarios = User::orderBy('name')->get();
        
        // Get available actions
        /** @var SupportCollection<int, string> $acciones */
        $acciones = AuditTrail::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.system-logs', [
            'logs' => $logs,
            'usuarios' => $usuarios,
            'acciones' => $acciones,
        ]);
    }
}
