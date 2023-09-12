@foreach($tasks as $task)
    <li class="list-group-item">
        <div class="d-flex justify-content-between">
            <div>
                {{ $task->title }} ({{ $task->completed ? '✅' : '⏰' }})
                <p>Created by: {{ optional($task->user)->name }}</p>
                <a href="{{ route('tasks.create', ['parent_id' => $task->id]) }}" class="btn btn-secondary btn-sm">Add subtask</a>
            </div>
            <div>
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
