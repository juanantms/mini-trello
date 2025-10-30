<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Tarea;
use App\Models\AuditTrail;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * KanbanBoard Component
 * 
 * Main Livewire component for the Kanban board interface.
 * Handles task creation, editing, deletion, and drag-and-drop functionality.
 */
class KanbanBoard extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $tareasPendientes = [];
    
    /** @var array<int, array<string, mixed>> */
    public array $tareasEnProgreso = [];
    
    /** @var array<int, array<string, mixed>> */
    public array $tareasCompletadas = [];
    
    // Modal properties
    public bool $showModal = false;
    public ?int $tareaId = null;
    public string $titulo = '';
    public string $descripcion = '';
    public string $estado = 'pendiente';
    public bool $modoEdicion = false;
    
    // Delete confirmation modal
    public bool $showDeleteModal = false;
    public ?int $tareaIdToDelete = null;
    
    // Drag & Drop state
    public ?int $draggedTareaId = null;

    /**
     * Validation rules for task form.
     *
     * @var array<string, string>
     */
    protected $rules = [
        'titulo' => 'required|string|min:3|max:255',
        'descripcion' => 'nullable|string|max:1000',
        'estado' => 'required|in:pendiente,en_progreso,completada',
    ];

    /**
     * Custom validation messages.
     *
     * @var array<string, string>
     */
    protected $messages = [
        'titulo.required' => 'El título es obligatorio.',
        'titulo.min' => 'El título debe tener al menos 3 caracteres.',
        'titulo.max' => 'El título no puede tener más de 255 caracteres.',
        'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        'estado.required' => 'El estado es obligatorio.',
        'estado.in' => 'El estado debe ser: pendiente, en_progreso o completada.',
    ];

    /**
     * Real-time validation for titulo field.
     *
     * @return void
     */
    public function updatedTitulo(): void
    {
        $this->validateOnly('titulo');
    }

    /**
     * Real-time validation for descripcion field.
     *
     * @return void
     */
    public function updatedDescripcion(): void
    {
        $this->validateOnly('descripcion');
    }

    /**
     * Real-time validation for estado field.
     *
     * @return void
     */
    public function updatedEstado(): void
    {
        $this->validateOnly('estado');
    }

    /**
     * Initialize component and load tasks.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->cargarTareas();
    }

    /**
     * Load and organize tasks by status.
     *
     * @return void
     */
    public function cargarTareas(): void
    {
        $tareas = Auth::user()->tareas;

        $this->tareasPendientes = $tareas->where('estado', Tarea::STATUS_PENDING)->values()->toArray();
        $this->tareasEnProgreso = $tareas->where('estado', Tarea::STATUS_IN_PROGRESS)->values()->toArray();
        $this->tareasCompletadas = $tareas->where('estado', Tarea::STATUS_COMPLETED)->values()->toArray();
    }

    /**
     * Open modal for creating a new task.
     *
     * @return void
     */
    public function abrirModal(): void
    {
        $this->resetForm();
        $this->modoEdicion = false;
        $this->showModal = true;
    }

    /**
     * Open modal for editing an existing task.
     *
     * @param int $tareaId
     * @return void
     */
    public function editarTarea(int $tareaId): void
    {
        $tarea = Tarea::findOrFail($tareaId);
        
        // Authorization check
        if (Auth::id() !== $tarea->user_id) {
            session()->flash('error', 'No tienes permiso para editar esta tarea.');
            return;
        }

        $this->tareaId = $tarea->id;
        $this->titulo = $tarea->titulo;
        $this->descripcion = $tarea->descripcion ?? '';
        $this->estado = $tarea->estado;
        $this->modoEdicion = true;
        $this->showModal = true;
    }

    /**
     * Save task (create or update).
     *
     * @return void
     */
    public function guardarTarea(): void
    {
        $this->validate();

        if ($this->modoEdicion) {
            $this->updateTarea();
        } else {
            $this->createTarea();
        }

        $this->cerrarModal();
        $this->cargarTareas();
    }

    /**
     * Create a new task.
     *
     * @return void
     */
    private function createTarea(): void
    {
        $tarea = Tarea::create([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'user_id' => Auth::id(),
        ]);

        // Log the creation
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => AuditTrail::ACTION_CREATED,
            'model_type' => Tarea::class,
            'model_id' => $tarea->id,
            'old_data' => null,
            'new_data' => $tarea->toArray(),
        ]);

        $this->dispatchBrowserEvent('notify', [
            'message' => 'Tarea creada correctamente.',
            'type' => 'success'
        ]);
    }

    /**
     * Update an existing task.
     *
     * @return void
     */
    private function updateTarea(): void
    {
        $tarea = Tarea::findOrFail($this->tareaId);
        
        // Authorization check
        if (Auth::id() !== $tarea->user_id) {
            $this->dispatchBrowserEvent('notify', [
                'message' => 'No tienes permiso para editar esta tarea.',
                'type' => 'error'
            ]);
            return;
        }

        $oldData = $tarea->toArray();

        $tarea->update([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
        ]);

        // Log the update
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => AuditTrail::ACTION_UPDATED,
            'model_type' => Tarea::class,
            'model_id' => $tarea->id,
            'old_data' => $oldData,
            'new_data' => $tarea->fresh()->toArray(),
        ]);

        $this->dispatchBrowserEvent('notify', [
            'message' => 'Tarea actualizada correctamente.',
            'type' => 'success'
        ]);
    }

    /**
     * Show confirmation modal for task deletion.
     *
     * @param int $tareaId
     * @return void
     */
    public function confirmarEliminacion(int $tareaId): void
    {
        $this->tareaIdToDelete = $tareaId;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel task deletion.
     *
     * @return void
     */
    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->tareaIdToDelete = null;
    }

    /**
     * Delete a task.
     *
     * @return void
     */
    public function eliminarTarea(): void
    {
        if (!$this->tareaIdToDelete) {
            return;
        }

        $tarea = Tarea::findOrFail($this->tareaIdToDelete);

        // Authorization check
        if (Auth::id() !== $tarea->user_id) {
            $this->dispatchBrowserEvent('notify', [
                'message' => 'No tienes permiso para eliminar esta tarea.',
                'type' => 'error'
            ]);
            $this->showDeleteModal = false;
            $this->tareaIdToDelete = null;
            return;
        }

        $oldData = $tarea->toArray();

        // Log the deletion before deleting
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => AuditTrail::ACTION_DELETED,
            'model_type' => Tarea::class,
            'model_id' => $tarea->id,
            'old_data' => $oldData,
            'new_data' => null,
        ]);

        $tarea->delete();

        $this->showDeleteModal = false;
        $this->tareaIdToDelete = null;
        $this->cargarTareas();
        
        $this->dispatchBrowserEvent('notify', [
            'message' => 'Tarea eliminada correctamente.',
            'type' => 'success'
        ]);
    }

    /**
     * Close modal and reset form.
     *
     * @return void
     */
    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset form fields to default values.
     *
     * @return void
     */
    private function resetForm(): void
    {
        $this->tareaId = null;
        $this->titulo = '';
        $this->descripcion = '';
        $this->estado = Tarea::STATUS_PENDING;
    }

    /**
     * Move task to a new status.
     *
     * @param int $tareaId
     * @param string $nuevoEstado
     * @return void
     */
    public function moverTarea(int $tareaId, string $nuevoEstado): void
    {
        $tarea = Tarea::findOrFail($tareaId);

        // Authorization check
        if (Auth::id() !== $tarea->user_id) {
            $this->dispatchBrowserEvent('notify', [
                'message' => 'No tienes permiso para modificar esta tarea.',
                'type' => 'error'
            ]);
            return;
        }

        // Validate the new status
        if (!in_array($nuevoEstado, Tarea::getValidStatuses(), true)) {
            $this->dispatchBrowserEvent('notify', [
                'message' => 'Estado inválido.',
                'type' => 'error'
            ]);
            return;
        }

        $oldData = $tarea->toArray();

        // Update the status
        $tarea->update(['estado' => $nuevoEstado]);

        // Log in AuditTrail
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => AuditTrail::ACTION_UPDATED,
            'model_type' => Tarea::class,
            'model_id' => $tarea->id,
            'old_data' => $oldData,
            'new_data' => $tarea->fresh()->toArray(),
        ]);

        // Reload tasks
        $this->cargarTareas();

        $this->dispatchBrowserEvent('notify', [
            'message' => 'Tarea movida correctamente.',
            'type' => 'success'
        ]);
    }

    /**
     * Start dragging a task.
     *
     * @param int $tareaId
     * @return void
     */
    public function startDrag(int $tareaId): void
    {
        $this->draggedTareaId = $tareaId;
    }

    /**
     * End dragging a task.
     *
     * @return void
     */
    public function endDrag(): void
    {
        $this->draggedTareaId = null;
    }

    /**
     * Drop task into a new status column.
     *
     * @param string $nuevoEstado
     * @return void
     */
    public function dropTarea(string $nuevoEstado): void
    {
        if (!$this->draggedTareaId) {
            return;
        }

        $this->moverTarea($this->draggedTareaId, $nuevoEstado);
        $this->draggedTareaId = null;
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.kanban-board');
    }
}
