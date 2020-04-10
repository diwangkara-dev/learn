<?php

namespace App\Http\Controllers\Api;

use App\ModuleLesson;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;

class ModuleLessonController extends Controller
{
    public function read(Request $request) {
        $entity = $request->get('entity');
        $includes = $request->get('includes');
        $trashed = $request->get('trashed');

        try {
            if ($entity || $includes) {
                $moduleLessons = ModuleLesson::find($entity ?? explode(',', $includes));
            }
    
            else if ($trashed) {
                $moduleLessons = ModuleLesson::onlyTrashed()->paginate(30);
            }
    
            else {
                $moduleLessons = ModuleLesson::paginate(30);
            }
    
            return response()->json($moduleLessons, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong when reading a module lesson',
                'data'    => []
            ], 500);
        }
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
                'data' => $request->all(),
            ], 400);
        }

        try {
            $moduleLesson = ModuleLesson::create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'content' => $request->get('content'),
            ]);
    
            return response()->json([
                'error'   => false,
                'message' => 'Successfully creating a module lesson',
                'data'    => $moduleLesson
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong when creating a module lesson',
                'data'    => []
            ], 500);
        }
    }
    
    public function update(Request $request, $entity) {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:200',
            'description' => 'string|max:255',
            'content' => 'string',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
                'data' => $request->all(),
            ], 400);
        }

        try {
            $moduleLessonTrashed = ModuleLesson::onlyTrashed()->where('id', $entity)->count();

            if ($moduleLessonTrashed > 0) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Module lesson already deleted',
                    'data'    => [
                        'entity' => $entity
                    ]
                ], 400);
            }
    
            $moduleLessonData = ModuleLesson::find($entity);

            if ($request->get('title')) {
                $moduleLessonData->title = $request->get('title');
            }
            if ($request->get('description')) {
                $moduleLessonData->description = $request->get('description');
            }
            if ($request->get('content')) {
                $moduleLessonData->content = $request->get('content');
            }

            $moduleLessonData->save();
    
            return response()->json([
                'error'   => false,
                'message' => 'Successfully updating a module lesson',
                'data'    => $moduleLessonData
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong when updating a module lesson',
                'data'    => []
            ], 500);
        }
    }

    public function delete(Request $request, $entity) {
        try {
            $moduleLessonData = ModuleLesson::find($entity);

            if (!$moduleLessonData) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Entity data is not defined',
                    'data'    => []
                ], 400);
            }
            
            $moduleLessonData->delete();

            return response()->json([
                'error'   => false,
                'message' => 'Module lesson already deleted',
                'data'    => [
                    'entity' => $entity
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong when deleting a module lesson',
                'data'    => []
            ], 500);
        }
    }
}
