<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UpdateSetting;
use App\RealWorld\Transformers\SettingTransformer;
use App\Services\SettingService;
use App\Setting;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class SettingController extends ApiController
{
    /**
     * SettingController constructor.
     *
     * @param  SettingTransformer  $transformer
     */
    public function __construct(SettingTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
        $this->middleware('user.isAdmin');
    }

    public function index()
    {
        $settings = (new SettingService())->getAllSettings();

        return $this->respondWithTransformer($settings);
    }

    public function update(UpdateSetting $request,Setting $setting)
    {
        try {
            (new SettingService())->updateSettingValue($setting,$request->input('setting.value'));
        } catch (Exception $exception) {
            Log::error("Error on updating setting with this error :".$exception->getMessage());
            $this->respondInternalError();
        }

        $this->respondSuccess();
    }
}
