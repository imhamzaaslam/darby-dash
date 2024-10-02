<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\SettingRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\SettingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SettingController extends Controller
{
    public function __construct(
        protected SettingRepositoryInterface $settingRepository
    ) {}

    /**
     * Display a listing of Notification Settings.
     * @return JsonResponse
     */
    public function getNotificationSettings(): JsonResponse
    {
        $settings = $this->settingRepository->getNotificationSettings();
        return response()->json(['data' => $settings]);
    }

    /**
     * Update Notification Setting
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationSetting(Request $request, int $id): JsonResponse
    {
        $data = $request->all();
        $setting = $this->settingRepository->getSetting($id);

        if (!$setting) {
            return response()->json(['message' => 'No setting found'], 404);
        }

        $this->settingRepository->updateSetting($setting, $data);

        return response()->json(['message' => 'Updated Successfully']);
    }
}
