<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteImageRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\Image\ImageResource;
use App\Models\Images;

class ImageController extends Controller
{
    public function store(ImageRequest $request)
    {
        $image = Images::upload($request->getModel(), $request->file('images'));

        return $this->success(new ImageResource($image), 'Image uploaded successfully');
    }

    public function destroy(DeleteImageRequest $request, Images $image)
    {
        $model = $image->imageable;

        $image->deleteImage();

        $model?->setMainImage();

        return $this->success([], 'Image deleted successfully');
    }
}
