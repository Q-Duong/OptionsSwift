<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Storage;
use File;

class DocumentController extends Controller
{
    public function upload_file(){
        $filename="DS";
        Storage::cloud()->put('test.txt', 'Storage 1');
        dd('created');
    }

    public function list_document(){
		$dir = '/';
		$recursive = true;
		//$content = collect(Storage::cloud()->listContents($dir,$recursive));
        // $content= Storage::cloud()->get('1Eqs15xGyKssCmZWEoCW4VIxdsduF0H-f');

        $content = collect(Storage::cloud()->listContents());
        $dir = $content->where('name', '=', 'BKQ6.xlsx')->first();
        // if (! $dir) {
        //     // Create parent dir
        //     Storage::makeDirectory($path.'/'.$directory);
        // }
		return $dir;
	}
}
