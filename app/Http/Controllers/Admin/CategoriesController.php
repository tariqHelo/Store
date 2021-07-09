<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use stdClass;
use Traversable;
use Illuminate\Support\Str;
use App\Http\Requests\CategoryRequest;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*
        SELECT categories.*, parents.name as parent_name FROM
        categories LEFT JOIN categories as parents
        ON parents.id = categories.parent_id
        WHERE ststus = 'active'
        ORDER BY created_at DESC, name ASC
        */
        // return collection of Category model object
        $entries = Category::leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name'
            ])
            //->where('categories.status', '=', 'active')
            ->orderBy('categories.created_at', 'DESC')
            ->orderBy('categories.name', 'ASC')
            ->get();

        // return collection of stdObj object
        // $entries = DB::table('categories')
        //     ->where('status', '=', 'active')
        //     ->orderBy('created_at', 'DESC')
        //     ->orderBy('name', 'ASC')
        //     ->get();

        $success = session()->get('success');
        /*$categories = [];
        if ($categories instanceof Traversable) {
            echo 'Yes';
            return;
        }*/

        return view('admin.categories.index', [
            'categories' => $entries,
            'title' => 'Categories List',
            'success' => $success,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Category::all();
        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Request Merge
        $request->merge([
            'slug' => Str::slug($request->post('name')),
            'status' => 'active',
        ]);

        // return array of all form fields
        // $request->all();
        // dd($request->all());

        // // return single field value
        // $request->description;
        // $request->input('description');
        // $request->get('description');
        // $request->post('description');
        // $request->query('description'); // ?description=value

        // Method #1
        // $category = new Category();
        // $category->name = $request->post('name');
        // $category->slug = Str::slug($request->post('name'));
        // $category->parent_id = $request->post('parent_id');
        // $category->description = $request->post('description');
        // $category->status = $request->post('status', 'active');
        // $category->save();        

        // Method #2: Mass assignment
        $category = Category::create($request->all());

        // Method #3: Mass assignment
        // $category = new Category([
        //     'name' => $request->post('name'),
        //     'slug' => Str::slug($request->post('name')),
        //     'parent_id' => $request->post('parent_id'),
        //     'description' => $request->post('description'),
        //     'status' => $request->post('status', 'active'),
        // ]);
        //$category->save();

        return redirect()->route('categories.index')
        ->with('success', 'Category created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        $parents = Category::where('id' , '<>' , $category->id)->get();

        return view('admin.categories.edit')
        ->withCategory($category)
        ->withParents($parents);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        /*$rules = [
            'name' => 'required|string|max:255|min:3|unique:categories',
            'parent_id' => 'nullable|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'status' => 'required|in:active,draft',
            'image' => 'image|max:512000|dimensions:min_width=300,min_height=300',
        ];
        $clean = $request->validate($rules);*/
        
        $request->merge([
            'slug' => Str::slug($request->name)
        ]);

        // Mass assignemnt
        //Category::where('id', '=', $id)->update( $request->all() );

        //
        $category = Category::find($id);
        
        // Method #1
        /*$category->name = $request->post('name');
        $category->parent_id = $request->post('parent_id');
        $category->description = $request->post('description');
        $category->status = $request->post('status');
        $category->save();*/

        # Method #2: Mass assignemnt
        $category->update( $request->all() );

        # Method #3: Mass assignment
        //$category->fill( $request->all() )->save();

        // PRG
        return redirect()->route('categories.index')
            ->with('success', 'Category updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);

        return redirect()->route('categories.index')
          ->with('success', 'Category deleted');
    }
}
