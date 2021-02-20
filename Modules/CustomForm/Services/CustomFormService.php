<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019-02-25
 * Time: 11:02
 */

namespace Modules\CustomForm\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomForm\Entities\FieldOption;
use Modules\CustomForm\Entities\Form;
use Modules\CustomForm\Entities\FormField;
use Modules\CustomForm\Entities\FormShow;

class CustomFormService
{
    public function saveForm(Request $request, int $form_id = 0)
    {
        $form = Form::updateOrCreate(
            [
                'id' => $form_id
            ],
            [
                'form_name'   => $request->get('form_name'),
                'action'      => $request->get('action'),
                'method'      => $request->get('method'),
                'file'        => $request->get('file'),
                'form_class'  => $request->get('form_class'),
                'form_id'     => $request->get('form_id'),
                'send_emails' => implode(';', $request->get('send_emails')),
            ]
        );

        if (!$form) {
            return response()->json(['message' => 'form not save']);
        }

        foreach ($request->get('fields') as $key => $item) {
            $field = FormField::updateOrCreate(
                [
                    'id' => isset($item['id']) ? $item['id'] : 0
                ],
                [
                    'form_id'     => $form->id,
                    'type'        => $item['type'],
                    'block_class' => $item['block_class'],
                    'name'        => mb_strtolower(Str::slug($item['name'])),
                    'label'       => $item['label'],
                    'placeholder' => $item['placeholder'],
                    'field_class' => $item['field_class'],
                    'field_id'    => $item['field_id'],
                    'validate'    => $item['validate'] ? implode('|', $item['validate']) : null,
                    'priority'    => $key
                ]
            );

            if (!$field) {
                return response()->json(['message' => 'form field not save']);
            }

            foreach ($item['options'] as $key => $option) {
                $field_option = FieldOption::updateOrCreate(
                    [
                        'id' => $option['id'] ?? 0
                    ],
                    [
                        'field_id'     => $field->id,
                        'value'        => $option['value'],
                        'title'        => $option['title'],
                        'option_class' => $option['option_class'],
                        'option_id'    => $option['option_id'],
                        'priority'     => $key
                    ]
                );

                if (!$field_option) {
                    return response()->json(['message' => 'form field option not save']);
                }
            }
        }

        FormShow::where('form_id', $form->id)->delete();
        if ($request->get('show_on')) {
            foreach ($request->get('show_on') as $item) {
                $explode = explode('_', $item);
                if (isset($explode[1])) {
                    FormShow::create(
                        [
                            'form_id' => $form->id,
                            'item_id' => $explode[1],
                            'type'    => $explode[0]
                        ]
                    );
                }
            }
        }
    }

    /**
     * Generate data for show form on page
     * @param $page_form
     * @return array
     */
    public function generateShowForm(FormShow $page_form)
    {
        $form = $page_form->getForm;

        $formPage = [
            'id'          => $form->id,
            'form_name'   => $form->form_name,
            'file'        => $form->file,
            'form_class'  => $form->form_class,
            'form_id'     => $form->form_id,
            'action'      => $form->action,
            'method'      => $form->method,
            'send_emails' => $form->send_emails
        ];

        $formPage['fields'] = [];
        foreach ($form->getFields as $getField) {
            $formPage['fields'][] = [
                'id'          => $getField->id,
                'type'        => $getField->type,
                'block_class' => $getField->block_class,
                'field_class' => $getField->field_class,
                'field_id'    => $getField->field_id,
                'name'        => $getField->name,
                'placeholder' => $getField->placeholder,
                'validate'    => $getField->validate,
                'options'     => $getField->getOptions,
            ];
        }

        return $formPage;
    }

    /**
     * Generate field for create form data save
     * @param $form
     * @return array
     */
    public function generateFieldsForm(array $form)
    {
        $fields = $form['fields'];
        $formFields = [
            'id' => $form['id']
        ];
        foreach ($fields as $field) {
            if ($field['type'] == 'checkbox' && !\Str::contains($field['validate'], 'accepted')) {
                $formFields[$field['name']] = [];
            } elseif ($field['type'] == 'file') {
                $formFields[$field['name']] = $formFields['file_name'] = '';
            } else {
                $formFields[$field['name']] = '';
            }
        }

        return $formFields;
    }

    /**
     * Generate validator for form
     * @param $fields
     * @return array
     */
    public function generateFieldValidate($fields)
    {
        $validateFields = [];
        foreach ($fields as $field) {
            if ($field->validate) {
                $validateFields[$field->name] = $field->validate;
            }
        }

        return $validateFields;
    }

    public function generateDataEmail($form, $formData)
    {
        $emailData = [
            'title' => $form->form_name
        ];
        foreach ($form->getFields as $field) {
            if ($formData[$field->name]) {
                $emailData[$field->name]['title'] = $field->placeholder ? $field->placeholder : $field->name;

                $value = $formData[$field->name];
                if ($field->getOptions->count()) {
                    $value = $field->getOptions()->where('value', $formData[$field->name])->value('title');
                }

                $emailData[$field->name]['value'] = $value;
                $emailData[$field->name]['type'] = $field->type;
                $emailData[$field->name]['name'] = $field->type == 'file' ? (isset($formData['file_name']) ? $formData['file_name'] : false) : false;
            }
        }

        return $emailData;
    }
}
