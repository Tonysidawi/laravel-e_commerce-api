<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\SetImageAsMainRequest;
use App\Http\Resources\Image\ImageResource;
use App\Models\Image;

class ImageController extends Controller
{
    public function store(ImageRequest $request)
    {
        $image = $request->makeImage();

        return $this->success(new ImageResource($image), 'Image uploaded successfully');
    }

    public function destroy(DeleteImageRequest $request, Image $image)
    {
        $model = $image->imageable;

        $image->deleteImage();

        $model?->setMainImage();

        return $this->success([], 'Image deleted successfully');
    }

    public function setAsMain(SetImageAsMainRequest $request, Image $image)
    {
        $image->setAsMain();

        return $this->success([], 'Image set as main successfully');
    }
}
