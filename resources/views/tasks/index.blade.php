@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="d-flex flex-row justify-content-between bd-highlight mb-3 mt-3">
                    <div class="p-2 bd-highlight">
                        @if(Auth::check())
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger">Logout</button>
                            </form>
                        @endif
                    </div>
                    <div class="p-2 bd-highlight align-items-start"><a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">Create task</a></div>
                    <div class="p-2 bd-highlight">
                        @if(auth()->check())
                            <div class="alert-success p-2 rounded" role="banner">
                                <strong>User: {{ Str::limit(auth()->user()->name, 20) }}</strong>
                            </div>
                        @endif
                    </div>
                </div>


                <form action="{{ route('tasks.index') }}" method="GET" class="mb-4 mt-5">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="title" class="form-label">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', request('title')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status:</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Select</option>
                                <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>ToDo</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority:</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="">Select</option>
                                <option value="1" {{ request('priority') === '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ request('priority') === '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('priority') === '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ request('priority') === '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ request('priority') === '5' ? 'selected' : '' }}>5</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mt-lg-n3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-9">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-message">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-message">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                @endif

                @error('title')
                    <div class="alert alert-danger" id="flash-message">
                        {{ $message }}
                    </div>
                @enderror

                <ul class="list-group">
                    @foreach($tasks as $task)
                        <li class="list-group-item mb-2 border-secondary border rounded">
                            <div class="d-flex justify-content-between">
                                <div class="task-block mb-4">
                                    <h5 class="mb-2">{{ $task->title }} ({{ $task->completed ? '✅' : '⏰' }})</h5>
                                    <p>Created by: {{ optional($task->user)->name }}
                                    <p class="mb-0 text-muted" style="white-space: pre-line; overflow-wrap: break-word;">
                                        {{ Str::limit($task->description, 30) }}
                                    </p>
                                    <a href="{{ route('tasks.create', ['parent_id' => $task->id]) }}" class="btn btn-secondary btn-sm">Add subtask</a>
                                </div>
                                <div class="">
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                            @if($task->childrenRecursive->count())
                                <ul class="list-group mt-2">
                                    @include('tasks.sub_task_list', ['tasks' => $task->childrenRecursive])
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function(){
            let alert = document.querySelector("#flash-message");
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }
        }, 3000);
    </script>
@endsection
