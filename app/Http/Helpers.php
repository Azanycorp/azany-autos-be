<?php

use App\Models\Mailing;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use ImageKit\ImageKit;

if (! function_exists('mailSend')) {
    /**
     * @param array<string, mixed> $payloadData
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

if (! function_exists('userAuth')) {
    function userAuth(): ?User
    {
        return auth()->user();
    }
}

if (! function_exists('uploadMultipleVehicleImages')) {
    function uploadMultipleVehicleImages($request, $file, $folder, $vehicle): void
    {
        if ($request->hasFile($file)) {
            foreach ($request->file($file) as $image) {
                $upload = uploadImage($image, $folder);

                $vehicle->vehicleImages()->create([
                    'image_path' => $upload
                ]);
            }
        }
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage($file, $folder = 'uploads')
    {
        try {
            $imageKit = imageKitClient();
            $fileName = time() . '_' . $file->getClientOriginalName();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));

            $uploadFile = $imageKit->uploadFile([
                'file' => "data:image/*;base64,{$base64}",
                'fileName' => $fileName,
                'folder' => $folder,
                'useUniqueFileName' => true,
                'isPublished' => true,
                'isPrivateFile' => false,
            ]);

            if (!empty($uploadFile->result->url)) {
                return $uploadFile->result->url;
            }
        } catch (\Exception $e) {
            Log::error('ImageKit Upload Failed: ' . $e->getMessage());
        }

        try {
            $uploadedImage = Cloudinary::uploadApi()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            if (!empty($uploadedImage['secure_url'])) {
                return $uploadedImage['secure_url'];
            }
        } catch (\Exception $e) {
            Log::error('Cloudinary Upload Failed: ' . $e->getMessage());
        }

        return [
            'provider' => null,
            'url' => null,
            'error' => 'Image upload failed on all providers.',
        ];
    }
}

if (!function_exists('imageKitClient')) {
    function imageKitClient()
    {
        return new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }
}
