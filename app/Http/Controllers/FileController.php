<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Conference;
use App\Models\EnReport;
use App\Models\Hart;
use App\Models\Hrtta;
use App\Models\Payment;
use App\Models\Register;
use App\Models\Report;
use App\Models\TempFile;
use App\Models\Vart;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function process(Request $request)
    {
        //Blog
        if ($request->hasFile('blog_image')) {
            $folder = saveFileSource($request->file('blog_image'));
        }

        TempFile::create([
            'folder' => $folder['folder'],
            'filename' => $folder['fileName'],
        ]);

        return response($folder['folder'], 200)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function upload_image_ck(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = saveImagesCK($request->file('upload'));
            return response()->json(['fileName' => $file['fileName'], 'uploaded' => 1, 'url' => $file['url']]);
        }
    }

    public function revert(Request $request)
    {
        $tempFile = TempFile::where('folder', $request->getContent())->first();
        if ($tempFile) {
            removeFileSource($request->getContent(), false);
            deleteImageFileDrive($request->getContent());
            $tempFile->delete();
            return response('Success delete', 200);
        }
        return response('Failed delete', 500);
    }

    public function destroy(Request $request)
    {
        //  ($request->type == 'en_report_file') {
        //     $en_report = EnReport::findOrFail($request->id);
        //     $en_report->en_report_file = null;
        //     $en_report->save();
        // }
        $registerTypes = ['register_image', 'register_image_card', 'payment_image'];
        if (in_array($request->type, $registerTypes)) {
            $register = Register::findOrFail($request->id);
            switch ($request->type) {
                case ('register_image'):
                    $register->register_image = null;
                    $register->save();
                    break;
                case ('register_image_card'):
                    $register->register_image_card = null;
                    $register->save();
                    break;
                case ('payment_image'):
                    deleteImageFileDrive($request->path);
                    $payment = Payment::findOrFail($register->payment_id);
                    $payment->payment_image = null;
                    $payment->save();
                    break;
            }
        }
        return response()->json(array('message' =>  __('alert.conference.successMessage_delete')));
    }

    public function destroyContent(Request $request)
    {
        switch ($request->target) {
            case ('vart'):
                $vart = Vart::findOrFail($request->id);
                removeFileSource(getFolderForDestroyFile($vart->vart_image), true);
                $vart->vart_image = null;
                $vart->save();
                break;
            case ('hart'):
                $hart = Hart::findOrFail($request->id);
                removeFileSource(getFolderForDestroyFile($hart->hart_image), true);
                $hart->hart_image = null;
                $hart->save();
                break;
            case ('hrtta'):
                $hrtta = Hrtta::findOrFail($request->id);
                removeFileSource(getFolderForDestroyFile($hrtta->hrtta_image), true);
                $hrtta->hrtta_image = null;
                $hrtta->save();
                break;
            case ('blogCategory'):
                $blogCategory = BlogCategory::findOrFail($request->id);
                removeFileSource(getFolderForDestroyFile($blogCategory->blog_category_image), true);
                $blogCategory->blog_category_image = null;
                $blogCategory->save();
                break;
            case ('blog'):
                $blog = Blog::findOrFail($request->id);
                removeFileSource(getFolderForDestroyFile($blog->blog_image), true);
                $blog->blog_image = null;
                $blog->save();
                break;
            case ('conference'):
                $conference = Conference::findOrFail($request->id);
                if ($request->locale == 'en') {
                    removeFileSource(getFolderForDestroyFile($conference->conference_image_en), true);
                    $conference->conference_image_en = null;
                } else {
                    removeFileSource(getFolderForDestroyFile($conference->conference_image), true);
                    $conference->conference_image = null;
                }
                $conference->save();
                break;
        }

        return response('Success delete', 200);
    }
}
