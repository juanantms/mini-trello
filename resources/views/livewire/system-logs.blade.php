<div>
    <div class="mb-6">
        <p class="text-gray-600">Registro de todas las actividades del sistema</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label for="filterAction" class="block text-sm font-medium text-gray-700 mb-2">
                    Filtrar por Acción
                </label>
                <select wire:model="filterAction" id="filterAction" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas las acciones</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion }}">{{ ucfirst($accion) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterUser" class="block text-sm font-medium text-gray-700 mb-2">
                    Filtrar por Usuario
                </label>
                <select wire:model="filterUser" id="filterUser" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar
                </label>
                <input type="text" wire:model.debounce.500ms="search" id="search" placeholder="Buscar en logs..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-end">
                <button wire:click="limpiarFiltros" type="button" class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha/Hora
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acción
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Modelo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Detalles
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->user->name ?? 'Sistema' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $log->user->email ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->action) {
                                        'login' => 'bg-green-100 text-green-800',
                                        'logout' => 'bg-gray-100 text-gray-800',
                                        'created' => 'bg-blue-100 text-blue-800',
                                        'updated' => 'bg-yellow-100 text-yellow-800',
                                        'deleted' => 'bg-red-100 text-red-800',
                                        'moved' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $actionText = match($log->action) {
                                        'login' => 'Inicio de Sesión',
                                        'logout' => 'Cierre de Sesión',
                                        'created' => 'Creación',
                                        'updated' => 'Actualización',
                                        'deleted' => 'Eliminación',
                                        'moved' => 'Movimiento',
                                        default => ucfirst($log->action)
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                    {{ $actionText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $modelName = $log->model_type ? class_basename($log->model_type) : 'N/A';
                                    $modelTranslation = match($modelName) {
                                        'Tarea' => 'Tarea',
                                        'User' => 'Usuario',
                                        default => $modelName
                                    };
                                @endphp
                                {{ $modelTranslation }}
                                @if($log->model_id)
                                    <span class="text-xs text-gray-400">#{{ $log->model_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($log->new_data)
                                        @if($log->action === 'created')
                                            <div class="space-y-1">
                                                @if(isset($log->new_data['titulo']))
                                                    <div><span class="font-semibold">Título:</span> {{ $log->new_data['titulo'] }}</div>
                                                @endif
                                                @if(isset($log->new_data['estado']))
                                                    <div><span class="font-semibold">Estado:</span> {{ $log->new_data['estado'] }}</div>
                                                @endif
                                            </div>
                                        @elseif($log->action === 'updated' || $log->action === 'moved')
                                            <div class="space-y-1">
                                                @foreach($log->new_data as $key => $value)
                                                    @if(isset($log->old_data[$key]) && $log->old_data[$key] !== $value)
                                                        <div class="text-xs">
                                                            <span class="font-semibold">{{ ucfirst($key) }}:</span>
                                                            <span class="text-red-600 line-through">{{ $log->old_data[$key] }}</span>
                                                            <span class="mx-1">→</span>
                                                            <span class="text-green-600">{{ $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif($log->action === 'deleted')
                                            @if(isset($log->old_data['titulo']))
                                                <div><span class="font-semibold">Título:</span> {{ $log->old_data['titulo'] }}</div>
                                            @endif
                                        @endif
                                    @elseif($log->action === 'login' || $log->action === 'logout')
                                        <span class="text-gray-500 italic">{{ $log->action === 'login' ? 'Usuario autenticado' : 'Sesión cerrada' }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Sin detalles</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>No hay logs que mostrar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
