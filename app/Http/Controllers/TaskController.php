<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $customMessages = [
            'title.not_regex' => 'Field `title` have invalid characters.',
        ];

        $request->validate([
            'status' => 'nullable|in:todo,done',
            'priority' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|not_regex:/[№%@!%^&*,]/',
        ], $customMessages);

        $query = Task::query()->whereNull('parent_id')->with('childrenRecursive');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . strtolower($request->input('title')) . '%');
        }

        $tasks = $query->get();

        $currentUser = auth()->user();

        return view('tasks.index', compact('tasks', 'currentUser'));
    }

    public function create()
    {
        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            $tasks = [];
        }

        return view('tasks.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:todo,done',
            'priority' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        Task::create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();
        if (!$user || $user->id !== $task->user_id) {
            return redirect('tasks')->with('error', 'You do not have permission to edit this task.');
        }

        if ($request->status === 'done' && $task->children()->where('status', '!=', 'done')->exists()) {
            return redirect()->back()->with('error', 'This task has unresolved subtasks.');
        }

        $request->validate([
            'status' => 'required|in:todo,done',
            'priority' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ]);

        $data = $request->all();

        if ($request->input('status') === 'done') {
            $data['completed'] = true;
            $data['completed_at'] = now();
        } elseif ($request->input('status') === 'todo') {
            $data['completed'] = false;
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();

        if (!$user || $user->id !== $task->user_id) {
            return redirect()->route('tasks.index')
                ->with('error', 'You do not have permission to delete this task.');
        }

        if ($task->completed) {
            return redirect()->route('tasks.index')
                ->with('error', 'Completed tasks cannot be deleted.');
        }

        $hasIncompleteSubtasks = Task::where('parent_id', $task->id)->where('completed', false)->exists();
        if ($hasIncompleteSubtasks) {
            return redirect()->route('tasks.index')
                ->with('error', 'You cannot delete a task that has incomplete subtasks.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }
}
