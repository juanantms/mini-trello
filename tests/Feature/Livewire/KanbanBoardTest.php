<?php

declare(strict_types=1);

use App\Http\Livewire\KanbanBoard;
use App\Models\Tarea;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// KanbanBoard Component Tests

test('component can render', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->assertStatus(200);
});

test('component loads user tasks', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'titulo' => 'Test Task',
        'estado' => 'pendiente',
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->assertSee('Test Task')
        ->assertSet('tareasPendientes.0.titulo', 'Test Task');
});

test('user can open create task modal', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('abrirModal')
        ->assertSet('showModal', true)
        ->assertSet('modoEdicion', false);
});

test('user can create a task through modal', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', 'New Task')
        ->set('descripcion', 'New Description')
        ->set('estado', 'pendiente')
        ->call('guardarTarea')
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('tareas', [
        'titulo' => 'New Task',
        'descripcion' => 'New Description',
        'estado' => 'pendiente',
        'user_id' => $user->id,
    ]);
});

test('task creation requires titulo', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', '')
        ->set('descripcion', 'Description')
        ->set('estado', 'pendiente')
        ->call('guardarTarea')
        ->assertHasErrors(['titulo']);
});

test('task titulo cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', str_repeat('a', 256))
        ->set('estado', 'pendiente')
        ->call('guardarTarea')
        ->assertHasErrors(['titulo']);
});

test('task estado must be valid', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', 'Test')
        ->set('estado', 'invalid_status')
        ->call('guardarTarea')
        ->assertHasErrors(['estado']);
});

test('user can edit their own task', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'titulo' => 'Original Title',
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('editarTarea', $tarea->id)
        ->assertSet('showModal', true)
        ->assertSet('modoEdicion', true)
        ->assertSet('titulo', 'Original Title')
        ->set('titulo', 'Updated Title')
        ->call('guardarTarea');

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
        'titulo' => 'Updated Title',
    ]);
});

test('user cannot edit another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user2->id,
    ]);

    Livewire::actingAs($user1)
        ->test(KanbanBoard::class)
        ->call('editarTarea', $tarea->id)
        ->assertSet('showModal', false);
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('confirmarEliminacion', $tarea->id)
        ->assertSet('showDeleteModal', true)
        ->call('eliminarTarea');

    $this->assertDatabaseMissing('tareas', [
        'id' => $tarea->id,
    ]);
});

test('user cannot delete another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user2->id,
    ]);

    Livewire::actingAs($user1)
        ->test(KanbanBoard::class)
        ->set('tareaIdToDelete', $tarea->id)
        ->call('eliminarTarea');

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
    ]);
});

test('user can move task to different status', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'estado' => 'pendiente',
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('moverTarea', $tarea->id, 'en_progreso');

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
        'estado' => 'en_progreso',
    ]);
});

test('task cannot be moved to invalid status', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'estado' => 'pendiente',
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('moverTarea', $tarea->id, 'invalid_status');

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
        'estado' => 'pendiente',
    ]);
});

test('task operations are logged in audit trail', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', 'Audited Task')
        ->set('estado', 'pendiente')
        ->call('guardarTarea');

    $tarea = Tarea::where('titulo', 'Audited Task')->first();

    $this->assertDatabaseHas('audit_trails', [
        'user_id' => $user->id,
        'action' => 'created',
        'model_type' => Tarea::class,
        'model_id' => $tarea->id,
    ]);
});

test('drag and drop functionality works', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'estado' => 'pendiente',
    ]);

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->call('startDrag', $tarea->id)
        ->assertSet('draggedTareaId', $tarea->id)
        ->call('dropTarea', 'en_progreso')
        ->assertSet('draggedTareaId', null);

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
        'estado' => 'en_progreso',
    ]);
});

test('modal closes after task creation', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', 'Test Task')
        ->set('estado', 'pendiente')
        ->call('guardarTarea')
        ->assertSet('showModal', false);
});

test('form resets after modal close', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(KanbanBoard::class)
        ->set('titulo', 'Test Task')
        ->set('descripcion', 'Test Description')
        ->call('cerrarModal')
        ->assertSet('titulo', '')
        ->assertSet('descripcion', '')
        ->assertSet('showModal', false);
});
