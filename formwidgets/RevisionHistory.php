<?php

namespace Samuell\Revisions\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use System\Models\Revision;

use Flash;

/**
 * RevisionHistory Form Widget
 */
class RevisionHistory extends FormWidgetBase
{
    protected $defaultAlias = 'abcreate_eshop_revision_history';

    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('revisionhistory');
    }

    public function prepareVars()
    {
        $this->vars['history'] = $this->model->revision_history->reverse();
        $this->vars['getFieldName'] = function ($fieldName) {
            $fields = $this->parentForm->getFields();
            if (array_key_exists($fieldName, $fields)) {
                $field = $fields[$fieldName];
                return $field->label ?? $field->tab ?? $fieldName;
            }
            return $fieldName;
        };
    }

    public function loadAssets()
    {
        $this->addCss('css/revisionhistory.css', 'samuell.revisionhistory');
        $this->addJs('js/revisionhistory.js', 'samuell.revisionhistory');
    }

    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }

    public function onRevertHistory()
    {
        $modelClass = $this->getClass();
        $section = $modelClass::find($this->model->id);

        $revision = Revision::find(input('revision_id'));

        $section->{$revision->field} = $revision->old_value;
        $section->save();

        Flash::success('Changes have been restored, changes are not visible without refreshing the page.');

        // TODO REFRESH PAGE
    }

    private function getClass()
    {
        return get_class($this->model);
    }
}
