@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 mt-5">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" class="form-control">{{ old('description', $task->description ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent task:</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">None</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->title }} | {{ optional($task->user)->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="todo">To Do</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <input type="number" name="priority" id="priority" class="form-control" min="1" max="5" value="3">
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
