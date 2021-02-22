<?php

namespace Modules\CustomForm\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomForm\Entities\FieldOption;
use Modules\CustomForm\Entities\Form;
use Modules\CustomForm\Entities\FormField;
use Modules\CustomForm\Entities\FormShow;
use Modules\CustomForm\Http\Requests\CreateFormRequest;
use Modules\CustomForm\Services\CustomFormService;
use Modules\CustomForm\Transformers\CustomFormCompletedResource;
use Modules\CustomForm\Transformers\CustomFormListResource;
use Modules\CustomForm\Transformers\CustomFormResource;
use Modules\CustomForm\Transformers\FormResource;

class CustomFormController extends AdminController
{
    protected $actions = [];

    protected $methods = [];

    public function __construct()
    {
        $this->middleware('admin');

        $this->actions = [
            str_replace('/api', '', route('send-call-back', [], false)) => 'CustomForm.feedback'
        ];

        $this->methods = [
            'post' => 'POST',
            'get'  => 'GET',
        ];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $forms = Form::filter($request)->paginate(30);

        return CustomFormListResource::collection($forms);
    }

    /**
     * Store a newly created resource in storage.
     * @param CreateFormRequest $request
     * @param CustomFormService $customFormService
     * @return bool
     */
    public function store(CreateFormRequest $request, CustomFormService $customFormService)
    {
        $customFormService->saveForm($request);

        return true;
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return CustomFormResource
     */
    public function show($id)
    {
        $response = [
            'actions'     => $this->actions,
            'methods'     => $this->methods,
            'types_field' => FormField::types(),
            'validations' => FormField::validations(),
        ];
        $form = Form::find($id);

        if (!$form) {
            $form = new Form();
        } else {
            $form->with(['getFields', 'getFields.getOptions', 'getShow']);
        }

        return (new CustomFormResource($form))->additional($response);
    }

    /**
     * Update the specified resource in storage.
     * @param CreateFormRequest $request
     * @param CustomFormService $customFormService
     * @param $form_id
     * @return bool
     */
    public function update(CreateFormRequest $request, CustomFormService $customFormService, $form_id)
    {
        $customFormService->saveForm($request, $form_id);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        if (!request()->get('field_id') && !request()->get('option_id')) {
            Form::destroy($id);
        } else {
            if (request()->get('field_id')) {
                FormField::destroy(request()->get('field_id'));
            }

            if (request()->get('option_id')) {
                FieldOption::destroy(request()->get('option_id'));
            }
        }

        return true;
    }

    /**
     * @param $form_id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function completed($form_id)
    {
        $form = Form::findOrFail($form_id);

        return CustomFormCompletedResource::collection($form->completed()->paginate(10));
    }

    public function cloneForm(Request $request, $form_id)
    {
        $form = Form::findOrFail($form_id);

        $data = $form->toArray();
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        if ($request->get('form_name')) {
            $data['form_name'] = $request->get('form_name');
        }

        $newForm = Form::create($data);

        $formShow = FormShow::where('form_id', $form->id)
            ->get(['item_id', 'type'])
            ->each(
                function ($item) use ($newForm) {
                    $item->form_id = $newForm->id;
                }
            )->toArray();

        FormShow::insert($formShow);

        $formFields = FormField::where('form_id', $form->id)->get(
            [
                'type',
                'block_class',
                'name',
                'label',
                'placeholder',
                'field_class',
                'field_id',
                'validate',
            ]
        );

        foreach ($formFields as $formField) {
            $dataField = $formField->toArray();
            $dataField['form_id'] = $newForm->id;
            $field = FormField::create($dataField);

            $fieldOptions = FieldOption::where('field_id', $formField->id)->get(
                [
                    'value',
                    'title',
                    'option_class',
                    'option_id'
                ]
            )->each(
                function ($item) use ($field) {
                    $item->field_id = $field->id;
                }
            )->toArray();

            FieldOption::insert($fieldOptions);
        }

        return true;
    }
}
