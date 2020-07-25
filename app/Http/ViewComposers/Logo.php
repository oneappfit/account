<?php

namespace App\Http\ViewComposers;

use App\Models\Common\Media;
use Illuminate\View\View;
use File;
use Image;
use Storage;

class Logo
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $logo = '';

        $media = Media::find(setting('company.logo'));

        if (!empty($media)) {
            $path = Storage::path($media->getDiskPath());

            if (!is_file($path)) {
                return $logo;
            }
        } else {
            $path = base_path('public/img/company.png');
        }

        $image = Image::cache(function($image) use ($path) {
            $width = setting('invoice.logo_size', 350);
            $height = setting ('invoice.logo_size', 97);
            
            $image->make($path)->resize($width, $height)->encode();
        });

        if (empty($image)) {
            return $logo;
        }

        $extension = File::extension($path);

        $logo = 'data:image/' . $extension . ';base64,' . base64_encode($image);

        $view->with(['logo' => $logo]);
    }
}
