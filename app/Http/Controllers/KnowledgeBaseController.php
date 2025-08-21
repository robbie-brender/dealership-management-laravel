<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of knowledge bases.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $dealershipId = $request->user()->dealership_id;
        
        $knowledgeBases = KnowledgeBase::where('dealership_id', $dealershipId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('KnowledgeBases/Index', [
            'knowledgeBases' => $knowledgeBases,
        ]);
    }
    
    /**
     * Store a newly created knowledge base in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_type' => 'required|in:file,link,manual',
            'source_url' => 'nullable|url|required_if:source_type,link',
            'content' => 'nullable|string|required_if:source_type,manual',
            'files.*' => 'nullable|file|mimes:pdf,docx,txt,csv,xls,xlsx,doc,json|max:51200', // 50MB max
        ]);
        
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return back()->withErrors($validator->errors());
        }
        
        $dealershipId = $request->user()->dealership_id;
        $userId = $request->user()->id;
        
        // Create the knowledge base entry
        $knowledgeBase = new KnowledgeBase();
        $knowledgeBase->name = $request->name;
        $knowledgeBase->description = $request->description;
        $knowledgeBase->source_type = $request->source_type;
        $knowledgeBase->dealership_id = $dealershipId;
        $knowledgeBase->user_id = $userId;
        
        // Handle different source types
        if ($request->source_type === 'link') {
            $knowledgeBase->source_url = $request->source_url;
        } elseif ($request->source_type === 'manual') {
            $knowledgeBase->content = $request->content;
        }
        
        $knowledgeBase->save();
        
        // Handle file uploads if present
        if ($request->hasFile('files')) {
            $filePaths = [];
            
            foreach ($request->file('files') as $file) {
                $path = $file->store('knowledge-bases/' . $knowledgeBase->id, 's3');
                $filePaths[] = $path;
                
                // Create a child knowledge base entry for each file
                if (count($request->file('files')) > 1) {
                    $childKnowledgeBase = new KnowledgeBase();
                    $childKnowledgeBase->name = $knowledgeBase->name . ' - ' . $file->getClientOriginalName();
                    $childKnowledgeBase->description = $knowledgeBase->description;
                    $childKnowledgeBase->source_type = 'file';
                    $childKnowledgeBase->file_path = $path;
                    $childKnowledgeBase->dealership_id = $dealershipId;
                    $childKnowledgeBase->user_id = $userId;
                    $childKnowledgeBase->save();
                } else {
                    // If only one file, update the main knowledge base
                    $knowledgeBase->file_path = $path;
                    $knowledgeBase->save();
                }
            }
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Knowledge base created successfully',
                'knowledge_base' => $knowledgeBase
            ]);
        }
        
        // For Inertia requests, redirect back with success message
        return back()->with([
            'success' => true,
            'message' => 'Knowledge base created successfully'
        ]);
    }
    
    /**
     * Display the specified knowledge base.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, $id)
    {
        $dealershipId = $request->user()->dealership_id;
        
        $knowledgeBase = KnowledgeBase::where('dealership_id', $dealershipId)
            ->findOrFail($id);
        
        return Inertia::render('KnowledgeBases/Show', [
            'knowledgeBase' => $knowledgeBase,
        ]);
    }
    
    /**
     * Generate a pre-signed URL for downloading a file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadFile(Request $request, $id)
    {
        $dealershipId = $request->user()->dealership_id;
        
        $knowledgeBase = KnowledgeBase::where('dealership_id', $dealershipId)
            ->findOrFail($id);
        
        if (!$knowledgeBase->file_path) {
            return response()->json([
                'success' => false,
                'message' => 'No file associated with this knowledge base'
            ], 404);
        }
        
        // Generate a temporary URL for downloading the file
        $url = Storage::disk('s3')->temporaryUrl(
            $knowledgeBase->file_path,
            now()->addMinutes(5)
        );
        
        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
