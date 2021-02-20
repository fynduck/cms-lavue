<?php

namespace Modules\CustomForm\Http\Controllers;

use Http\Client\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\CustomForm\Emails\SendCustomFormEmail;
use Modules\CustomForm\Entities\Form;
use Modules\CustomForm\Entities\FormSent;
use Modules\CustomForm\Entities\FormShow;
use Modules\CustomForm\Services\CustomFormService;
use Modules\CustomForm\Transformers\FormResource;
use Modules\Settings\Entities\Settings;

class FrontController extends Controller
{
    protected $customFormService;

    public function __construct(CustomFormService $customFormService)
    {
        $this->customFormService = $customFormService;
    }

    public function getForm($page_type, $item_id, $form_id = null)
    {
        $query = FormShow::where('type', $page_type)->where('item_id', $item_id);
        if ($form_id) {
            $query->where('form_id', $form_id);
        }

        $onPageForm = $query->first();
        if (!$onPageForm) {
            return [];
        }

        $additional = [
            'trans' => [
                'send'             => trans('global.send'),
                'file_no_selected' => trans('global.file_no_selected')
            ]
        ];

        return (new FormResource($onPageForm->getForm))->additional($additional);
    }

    public function saveCallBack(Request $request)
    {
        $form = Form::find($request->get('id'));
        if (!$form || !$form->getFields) {
            return response()->json(['message' => ['form not found']], 404);
        }

        $validator = Validator::make($request->get('fields'), $this->customFormService->generateFieldValidate($form->getFields));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        FormSent::create(
            [
                'form_id'   => $form->id,
                'form_data' => json_encode($request->all())
            ]
        );

        $emailData = $this->customFormService->generateDataEmail($form, $request->get('fields'));

        if ($form->send_emails) {
            $emails = explode(',', $form->send_emails);
        } else {
            $settings = Settings::getByKey(['email_rss']);
            $emails = explode(',', $settings['email_rss']);
        }

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new SendCustomFormEmail($emailData));
            } catch (Exception $exception) {
                return $exception->getMessage();
            }
        }

        $response = [
            'message' => trans('global.message-sends')
        ];

        return response()->json($response);
    }
}
