<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequestForm;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\TempFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class BlogController extends Controller
{
    private $folder;

    public function __construct()
    {
        $this->folder = 'blog';
    }

    // public function index()
    // {
    //     $getAllBlog = Blog::orderBy('id', 'DESC')->paginate(10);
    //     return view('pages.admin.blog.index', compact('getAllBlog'));
    // }

    public function create()
    {
        return view('pages.admin.blog.create');
    }

    public function upload_image_ck(Request $request)
    {
        if ($request->hasFile('upload')) {
            $get_image = $request->file('upload');
            $get_name_image = $get_image->getClientOriginalName();
            $name_image = current(explode('.', $get_name_image));
            $new_image =  $name_image . rand(0, 99) . '.' . $get_image->getClientOriginalExtension();
            $get_image->move(public_path('uploads/content/'), $new_image);
            $url = asset('uploads/content/' . $new_image);
            return response()->json(['fileName' => $new_image, 'uploaded' => 1, 'url' => $url]);
        }
    }

    public function store(BlogRequestForm $request)
    {
        DB::beginTransaction();
        try {
            $blog = new Blog();
            $blog->blog_title = $request->blog_title;
            $blog->blog_slug = Str::slug($request->blog_title);
            $blog->blog_content = $request->blog_content;
            $file = TempFile::firstWhere('folder', $request->blog_image);
            if ($file) {
                $blog->blog_image = moveFileSource($file->folder, $this->folder, $file->filename);
                $file->delete();
            }
            $blog->save();


            // $check = Post::where('post_title', $name)->exists();
            // if ($check) {
            //     return Redirect()->back()->with('error', 'Bài đã tồn tại, Vui lòng kiểm tra lại.')->withInput();
            // }
            DB::commit();
            return response()->json(array('success' => true, 'message' => __('alert.blog.successfulNotification')));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'route' => '500'));
        }

        // if ($get_image) {
        //     $get_name_image = $get_image->getClientOriginalName();
        //     $name_image = current(explode('.', $get_name_image));
        //     $new_image =  $name_image . rand(0, 99) . '.' . $get_image->getClientOriginalExtension();
        //     $get_image->move(public_path('uploads/post/'), $new_image);
        //     $post->post_image = $new_image;
        //     $post->save();
        //     return Redirect::route('post.index')->with('success', 'Thêm bài viết thành công');
        // } else {
        //     return Redirect()->back()->with('error', 'Vui lòng thêm hình ảnh');
        // }
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('pages.admin.blog.edit', compact('blog'));
    }

    public function update(PostRequestForm $request, $id)
    {
        $blog = Blog::findOrFail($request->id);
        $blog->blog_title = $request->blog_title;
        $blog->blog_slug = Str::slug($request->blog_title);
        $blog->blog_text = $request->blog_text;
        $file = TempFile::firstWhere('folder', $request->blog_image);
        if ($file) {
            if ($blog->blog_image) {
                removeFileSource(getFolderForDestroyFile($blog->blog_image), true);
            }
            $blog->blog_image = moveFileSource($file->folder, $this->folder, $file->filename);
            $file->delete();
        }
        $blog->save();
        return Redirect::route('post.index')->with('success', 'Cập nhật bài viết thành công');
    }

    // public function destroy($id)
    // {
    //     $post = Post::findOrFail($id);
    //     $post_image = $post->post_image;
    //     if ($post_image) {
    //         unlink(public_path('uploads/post/') . $post_image);
    //     }
    //     $post->delete();
    //     return Redirect()->back()->with('success', 'Xóa bài viết thành công');
    // }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $blog = Blog::findOrFail($request->id);
            if ($blog->blog_image) {
                removeFileSource(getFolderForDestroyFile($blog->blog_image), true);
            }
            $blog->delete();
            DB::commit();
            return response()->json(array('message' => __('alert.blog.successfulNotification'), 'success' => true));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('success' => false, 'route' => '500'));
        }
    }

    //Client
    public function index()
    {
        return view('pages.client.blog.index');
    }

    public function detail($blog_slug)
    {
        $blog = Blog::where('blog_slug', $blog_slug)->first();
        return view('pages.client.blog.detail', compact('blog'));
    }
}
