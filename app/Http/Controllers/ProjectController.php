<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectsRequest;
use App\Http\Requests\UpdateProjectsRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Auth;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Project::class, 'project');
    }
    public function index(Request $request) {
        $projects = QueryBuilder::for(Project::class)
            ->allowedIncludes(["tasks"])
            ->paginate();
        return new ProjectCollection($projects);
    }
    public function show(Project $project) {
        return (new ProjectResource($project))
            ->load('tasks')
            ->load('members');
    }
    public function store(StoreProjectsRequest $request) {
        $validate = $request->validated();
        $project = Auth::user()->projects()->create($validate);
        return new ProjectResource($project);
    }
    public function update(UpdateProjectsRequest $request, Project $project) {
        $validate = $request->validated();
        $project->update($validate);
        return new ProjectResource($project);
    }
    public function destroy(Request $request, Project $project) {
        $project->delete();
        return response()->json([ 'message'=> 'deleted successfully!' ], 202);
    }
}
