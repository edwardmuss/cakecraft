<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view templates')->only(['index', 'show']);
        $this->middleware('permission:create templates')->only(['create', 'store']);
        $this->middleware('permission:update templates')->only(['edit', 'update']);
        $this->middleware('permission:delete templates')->only(['destroy']);
    }

    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $templates = Template::where('branch_id', $branchId)->orderby('id', 'DESC')->get();

        return view('pages.templates.index', compact('templates', 'accountId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template = new Template();
        $template->name = $request->name;
        $template->content = $request->content;
        $template->branch_id = $request->branch_id;
        $template->save();

        return response()->json(['success' => 'Template added successfully.']);
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->name = $request->name;
        $template->content = $request->content;
        $template->save();

        return response()->json(['success' => 'Template updated successfully.']);
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return response()->json(['success' => 'Template deleted successfully.']);
    }
}
