<?php
if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        $format = explode("/", $date);
        $day = array_shift($format);
        $year = array_pop($format);
        $month = implode(" ", $format);
        $dateFormat = $year . "-" . $month . "-" . $day;
        return $dateFormat;
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice($price)
    {
        $str_price_format = Str::replace([' ', '₫', '.'], '', $price);
        return $str_price_format;
    }
}

if (!function_exists('versionResource')) {
    function versionResource($path)
    {
        return  asset($path . "?v=" . config("app.resourceVersion"));
    }
}

if (!function_exists('saveFileSource')) {
    function saveFileSource($file)
    {
        $fileName = $file->getClientOriginalName();
        $getName = current(explode('.', $fileName));
        $fileNameConvert = Str::slug($getName) . '.' . $file->getClientOriginalExtension();
        $folder = uniqid();
        Storage::putFileAs('tmp/' . $folder, $file, $fileNameConvert);
        $response = ['folder' => $folder, 'fileName' => $fileNameConvert];
        return $response;
    }
}

if (!function_exists('saveImagesCK')) {
    function saveImagesCK($file)
    {
        $fileName = $file->getClientOriginalName();
        $getName = current(explode('.', $fileName));
        $fileNameConvert = Str::slug($getName) . '.' . $file->getClientOriginalExtension();
        Storage::putFileAs('public/content/', $file, $fileNameConvert);
        $url = asset('storage/content/' . $fileNameConvert);
        $response = ['url' => $url, 'fileName' => $fileNameConvert];
        return $response;
    }
}

if (!function_exists('deleteImageFileDrive')) {
    function deleteImageFileDrive($path)
    {
        Storage::cloud()->delete($path);
        return true;
    }
}

if (!function_exists('moveFileSource')) {
    function moveFileSource($folder, $folderMove, $fileName)
    {
        Storage::move('tmp/' . $folder, 'public/' . $folderMove . '/' . $folder);
        return $folderMove . '/' . $folder . '/' . $fileName;
    }
}

if (!function_exists('removeFileSource')) {
    function removeFileSource($folder, $target)
    {
        if ($target) {
            Storage::deleteDirectory('public/' . $folder);
        } else {
            Storage::deleteDirectory('tmp/' . $folder);
        }
    }
}

if (!function_exists('getFolderForDestroyFile')) {
    function getFolderForDestroyFile($folder)
    {
        $folderFormat = explode('/', $folder);
        return $folderFormat[0] . '/' . $folderFormat[1];
    }
}
