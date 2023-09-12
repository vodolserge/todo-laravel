@foreach($subtasks as $subtask)
    <tr>
        <th scope="row">{{ $subtask->id }}</th>
        <td>— {{ $subtask->title }}</td>
        <td>{{ $subtask->status }}</td>
        <td>{{ $subtask->priority }}</td>
        <td>
            <a href="{{ route('tasks.edit', $subtask) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('tasks.destroy', $subtask) }}" method="post" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            @if($subtask->status === 'todo')
                <form action="{{ route('tasks.markAsDone', $subtask) }}" method="post" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Mark as Done</button>
                </form>
            @endif
        </td>
    </tr>
    @if($subtask->children->isNotEmpty())
        @include('tasks.subtasks', ['subtasks' => $subtask->children])
    @endif
@endforeach
