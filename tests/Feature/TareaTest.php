<?php

declare(strict_types=1);

use App\Models\Tarea;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Task Management Tests

test('user can create a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tarea = Tarea::create([
        'titulo' => 'Test Task',
        'descripcion' => 'Test Description',
        'estado' => 'pendiente',
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('tareas', [
        'titulo' => 'Test Task',
        'descripcion' => 'Test Description',
        'estado' => 'pendiente',
        'user_id' => $user->id,
    ]);
});

test('task creation requires titulo', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->expectException(\Illuminate\Database\QueryException::class);
    Tarea::create([
        'descripcion' => 'Test Description',
        'estado' => 'pendiente',
        'user_id' => $user->id,
    ]);
});

test('task titulo cannot exceed 255 characters', function () {
    $user = User::factory()->create();
    $longTitle = str_repeat('a', 256);

    $tarea = Tarea::factory()->make([
        'titulo' => $longTitle,
        'user_id' => $user->id,
    ]);

    $validator = validator($tarea->toArray(), [
        'titulo' => 'required|string|max:255',
    ]);

    $this->assertTrue($validator->fails());
});

test('user can update their own task', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'titulo' => 'Original Title',
    ]);

    $tarea->update([
        'titulo' => 'Updated Title',
    ]);

    $this->assertDatabaseHas('tareas', [
        'id' => $tarea->id,
        'titulo' => 'Updated Title',
    ]);
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
    ]);

    $tareaId = $tarea->id;
    $tarea->delete();

    $this->assertDatabaseMissing('tareas', [
        'id' => $tareaId,
    ]);
});

test('task belongs to a user', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($tarea->user)->toBeInstanceOf(User::class);
    expect($tarea->user->id)->toBe($user->id);
});

test('task can have valid statuses', function () {
    $user = User::factory()->create();

    $statuses = ['pendiente', 'en_progreso', 'completada'];

    foreach ($statuses as $status) {
        $tarea = Tarea::factory()->create([
            'user_id' => $user->id,
            'estado' => $status,
        ]);

    $this->assertEquals($status, $tarea->estado);
    }
});

test('user can only view their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $tarea1 = Tarea::factory()->create(['user_id' => $user1->id]);
    $tarea2 = Tarea::factory()->create(['user_id' => $user2->id]);

    $this->assertTrue($user1->can('view', $tarea1));
    $this->assertFalse($user1->can('view', $tarea2));
});

test('user can only update their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $tarea1 = Tarea::factory()->create(['user_id' => $user1->id]);
    $tarea2 = Tarea::factory()->create(['user_id' => $user2->id]);

    $this->assertTrue($user1->can('update', $tarea1));
    $this->assertFalse($user1->can('update', $tarea2));
});

test('user can only delete their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $tarea1 = Tarea::factory()->create(['user_id' => $user1->id]);
    $tarea2 = Tarea::factory()->create(['user_id' => $user2->id]);

    $this->assertTrue($user1->can('delete', $tarea1));
    $this->assertFalse($user1->can('delete', $tarea2));
});

test('task status can be changed', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'estado' => 'pendiente',
    ]);

    $tarea->update(['estado' => 'en_progreso']);
    $this->assertEquals('en_progreso', $tarea->fresh()->estado);

    $tarea->update(['estado' => 'completada']);
    $this->assertEquals('completada', $tarea->fresh()->estado);
});

// Audit Trail Tests

test('task creation is logged in audit trail', function () {
    $user = User::factory()->create();

    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
    ]);

    AuditTrail::create([
        'user_id' => $user->id,
        'action' => AuditTrail::ACTION_CREATED,
        'model_type' => Tarea::class,
        'model_id' => $tarea->id,
        'old_data' => null,
        'new_data' => $tarea->toArray(),
    ]);

    $this->assertDatabaseHas('audit_trails', [
        'user_id' => $user->id,
        'action' => 'created',
        'model_type' => Tarea::class,
        'model_id' => $tarea->id,
    ]);
});

test('task update is logged in audit trail', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
        'titulo' => 'Original Title',
    ]);

    $oldData = $tarea->toArray();
    $tarea->update(['titulo' => 'Updated Title']);

    AuditTrail::create([
        'user_id' => $user->id,
        'action' => AuditTrail::ACTION_UPDATED,
        'model_type' => Tarea::class,
        'model_id' => $tarea->id,
        'old_data' => $oldData,
        'new_data' => $tarea->fresh()->toArray(),
    ]);

    $this->assertDatabaseHas('audit_trails', [
        'user_id' => $user->id,
        'action' => 'updated',
        'model_type' => Tarea::class,
        'model_id' => $tarea->id,
    ]);

    $auditLog = AuditTrail::where('model_id', $tarea->id)
        ->where('action', 'updated')
        ->first();

    expect($auditLog->old_data['titulo'])->toBe('Original Title');
    expect($auditLog->new_data['titulo'])->toBe('Updated Title');
});

test('task deletion is logged in audit trail', function () {
    $user = User::factory()->create();
    $tarea = Tarea::factory()->create([
        'user_id' => $user->id,
    ]);

    $tareaId = $tarea->id;
    $oldData = $tarea->toArray();

    AuditTrail::create([
        'user_id' => $user->id,
        'action' => AuditTrail::ACTION_DELETED,
        'model_type' => Tarea::class,
        'model_id' => $tareaId,
        'old_data' => $oldData,
        'new_data' => null,
    ]);

    $tarea->delete();

    $this->assertDatabaseHas('audit_trails', [
        'user_id' => $user->id,
        'action' => 'deleted',
        'model_type' => Tarea::class,
        'model_id' => $tareaId,
    ]);
});
