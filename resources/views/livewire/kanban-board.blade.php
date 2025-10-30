<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Tablero Kanban</h1>
        <button wire:click="abrirModal" type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Tarea
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pendiente
                </h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    {{ count($tareasPendientes) }}
                </span>
            </div>
            
            <div x-data="{ isOver: false }" @dragover.prevent="isOver = true" @dragleave="isOver = false" @drop.prevent="isOver = false; $wire.dropTarea('pendiente')" :class="isOver ? 'bg-blue-100 border-2 border-dashed border-blue-400' : ''" class="min-h-[500px] space-y-3 transition-all duration-300 rounded-lg p-2">
                @forelse($tareasPendientes as $tarea)
                    <div draggable="true" @dragstart="$wire.startDrag({{ $tarea['id'] }}); $el.classList.add('opacity-50')" @dragend="$wire.endDrag(); $el.classList.remove('opacity-50')" class="bg-white rounded-lg shadow-md p-4 cursor-move hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 border-l-4 border-yellow-500">
                        <h4 class="font-semibold text-gray-800 mb-2">{{ $tarea['titulo'] }}</h4>
                        @if(!empty($tarea['descripcion']))
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($tarea['descripcion'], 80) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>{{ \Carbon\Carbon::parse($tarea['created_at'])->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="editarTarea({{ $tarea['id'] }})" type="button" class="flex-1 text-center px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                Editar
                            </button>
                            <button wire:click="confirmarEliminacion({{ $tarea['id'] }})" type="button" class="flex-1 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">No hay tareas pendientes</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                    </svg>
                    En Progreso
                </h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    {{ count($tareasEnProgreso) }}
                </span>
            </div>
            
            <div x-data="{ isOver: false }" @dragover.prevent="isOver = true" @dragleave="isOver = false" @drop.prevent="isOver = false; $wire.dropTarea('en_progreso')" :class="isOver ? 'bg-blue-100 border-2 border-dashed border-blue-400' : ''" class="min-h-[500px] space-y-3 transition-all duration-300 rounded-lg p-2">
                @forelse($tareasEnProgreso as $tarea)
                    <div draggable="true" @dragstart="$wire.startDrag({{ $tarea['id'] }}); $el.classList.add('opacity-50')" @dragend="$wire.endDrag(); $el.classList.remove('opacity-50')" class="bg-white rounded-lg shadow-md p-4 cursor-move hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 border-l-4 border-blue-500">
                        <h4 class="font-semibold text-gray-800 mb-2">{{ $tarea['titulo'] }}</h4>
                        @if(!empty($tarea['descripcion']))
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($tarea['descripcion'], 80) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>{{ \Carbon\Carbon::parse($tarea['created_at'])->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="editarTarea({{ $tarea['id'] }})" type="button" class="flex-1 text-center px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                Editar
                            </button>
                            <button wire:click="confirmarEliminacion({{ $tarea['id'] }})" type="button" class="flex-1 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">No hay tareas en progreso</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Completada
                </h3>
                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                    {{ count($tareasCompletadas) }}
                </span>
            </div>
            
            <div x-data="{ isOver: false }" @dragover.prevent="isOver = true" @dragleave="isOver = false" @drop.prevent="isOver = false; $wire.dropTarea('completada')" :class="isOver ? 'bg-blue-100 border-2 border-dashed border-blue-400' : ''" class="min-h-[500px] space-y-3 transition-all duration-300 rounded-lg p-2">
                @forelse($tareasCompletadas as $tarea)
                    <div draggable="true" @dragstart="$wire.startDrag({{ $tarea['id'] }}); $el.classList.add('opacity-50')" @dragend="$wire.endDrag(); $el.classList.remove('opacity-50')" class="bg-white rounded-lg shadow-md p-4 cursor-move hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 border-l-4 border-green-500">
                        <h4 class="font-semibold text-gray-800 mb-2">{{ $tarea['titulo'] }}</h4>
                        @if(!empty($tarea['descripcion']))
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($tarea['descripcion'], 80) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>{{ \Carbon\Carbon::parse($tarea['created_at'])->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="editarTarea({{ $tarea['id'] }})" type="button" class="flex-1 text-center px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                Editar
                            </button>
                            <button wire:click="confirmarEliminacion({{ $tarea['id'] }})" type="button" class="flex-1 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">No hay tareas completadas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    @if($showDeleteModal)
        <x-modal name="delete-confirm" :show="$showDeleteModal" maxWidth="lg">
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            Eliminar Tarea
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            ¿Estás seguro de que quieres eliminar esta tarea? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="cancelarEliminacion" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="button" wire:click="eliminarTarea" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </div>
            </div>
        </x-modal>
    @endif

    <!-- Modal de Crear/Editar Tarea -->
    @if($showModal)
        <x-modal name="task-form" :show="$showModal" maxWidth="lg">
            <form wire:submit.prevent="guardarTarea" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $modoEdicion ? 'Editar Tarea' : 'Nueva Tarea' }}
                    </h3>
                    <button type="button" wire:click="cerrarModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo" wire:model="titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('titulo') border-red-500 @enderror" placeholder="Ingrese el título de la tarea" maxlength="255">
                        @error('titulo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Mínimo 3 caracteres, máximo 255</p>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea id="descripcion" wire:model="descripcion" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror" placeholder="Ingrese la descripción de la tarea" maxlength="1000"></textarea>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Máximo 1000 caracteres</p>
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="estado" wire:model="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estado') border-red-500 @enderror">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="cerrarModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $modoEdicion ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </form>
        </x-modal>
    @endif
</div>
