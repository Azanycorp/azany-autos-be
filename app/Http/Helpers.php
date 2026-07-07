<?php

use App\Models\Mailing;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ImageKit\ImageKit;

if (! function_exists('mailSend')) {
    /**
     * @param  array<string, mixed>  $payloadData
     */
    function mailSend(string $type, User $recipient, string $subject, string $mailClass, array $payloadData = []): void
    {
        $data = [
            'type' => $type,
            'email' => $recipient->email,
            'subject' => $subject,
            'body' => '',
            'mailable' => $mailClass,
            'scheduled_at' => now(),
            'payload' => array_merge($payloadData),
        ];

        Mailing::saveData($data);
    }
}

if (! function_exists('generateUserVerificationCode')) {
    function generateUserVerificationCode(): int
    {
        return mt_rand(1000, 9999);
    }
}

if (! function_exists('calculateAuctionDuration')) {
    /**
     * Calculate the end date of an auction based on the number of days.
     *
     * @param int|string $days
     * @return CarbonInterface
     */
    function calculateAuctionDuration(int|string $days): CarbonInterface
    {
        return now()->addDays((int) $days);
    }
}

if (! function_exists('userAuth')) {
    function userAuth(): ?User
    {
        return auth()->user();
    }
}

// if (! function_exists('uploadMultipleVehicleImages')) {
//     function uploadMultipleVehicleImages(Request $request, string $file, string $folder, Vehicle $vehicle): void
//     {
//         if ($request->hasFile($file)) {
//             foreach ($request->file($file) as $image) {
//                 $upload = uploadImage($image, $folder);

//                 $vehicle->vehicleImages()->create([
//                     'image_path' => $upload,
//                 ]);
//             }
//         }
//     }
// }

if (! function_exists('uploadMultipleImages')) {
    /**
     * Upload multiple images and attach them dynamically to a model's relationship.
     *
     * @param Request $request The incoming HTTP request
     * @param string $inputKey The name of the input field (e.g., 'vehicle_images')
     * @param string $folder The cloud storage folder destination
     * @param Model $model The Eloquent model instance (Vehicle, Product, etc.)
     * @param string $relationship The model relationship name (e.g., 'vehicleImages', 'images')
     * @param string $columnName The database column name to save the path into (defaults to 'image_path')
     * @return void
     */
    function uploadMultipleImages(
        Request $request,
        string $inputKey,
        string $folder,
        Model $model,
        string $relationship,
        string $columnName = 'image_path'
    ): void {
        if ($request->hasFile($inputKey)) {
            foreach ($request->file($inputKey) as $image) {
                $upload = uploadImage($image, $folder);

                $model->{$relationship}()->create([
                    $columnName => $upload,
                ]);
            }
        }
    }
}

if (! function_exists('uploadImage')) {
    /**
     * Upload an image to a cloud provider with fallbacks.
     *
     * @param UploadedFile $file  <-- Updated typehint inside PHPDoc
     * @param string $folder
     * @return array{url: string|null, public_id: string|null, error: string|null}
     */
    function uploadImage(UploadedFile $file, string $folder = 'uploads'): array|string
    {
        try {
            $imageKit = imageKitClient();
            $fileName = time().'_'.$file->getClientOriginalName();
            $realPath = $file->getRealPath();
            $fileContents = $realPath ? file_get_contents($realPath) : false;

            if ($fileContents === false) {
                throw new Exception("Unable to read file contents.");
            }

            $base64 = base64_encode($fileContents);

            $uploadFile = $imageKit->uploadFile([
                'file' => "data:image/*;base64,{$base64}",
                'fileName' => $fileName,
                'folder' => $folder,
                'useUniqueFileName' => true,
                'isPublished' => true,
                'isPrivateFile' => false,
            ]);
            /** @var object{url?: string, fileId?: string} $uploadResult */
            $uploadResult = $uploadFile->result;

            if (isset($uploadResult->url) && filled($uploadResult->url)) {
                return $uploadResult->url;
            }
        } catch (Exception $e) {
            Log::error('ImageKit Upload Failed: '.$e->getMessage());
        }

        try {
            $uploadedImage = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder,
            ]);

            if (filled($uploadedImage->getSecurePath())) {
                return $uploadedImage->getSecurePath();
            }
        } catch (Exception $e) {
            Log::error('Cloudinary Upload Failed: '.$e->getMessage());
        }

        return 'Image upload failed on all providers.';
    }
}

if (! function_exists('imageKitClient')) {
    function imageKitClient(): ImageKit
    {
        return new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }
}
