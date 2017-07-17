<?php
namespace App\Modules\Forms;

class ErrorStore implements \AdamWathan\Form\ErrorStore\ErrorStoreInterface
{
    public function hasError($key)
    {
        if (! $this->hasErrors()) {
            return false;
        }

        $key = $this->transformKey($key);

        return (! empty($_SESSION['errors'][$key]));
    }

    public function getError($key)
    {
        if (! $this->hasError($key)) {
            return null;
        }

        $key = $this->transformKey($key);

        return $_SESSION['errors'][$key];
    }

    protected function hasErrors()
    {
        return (! empty($_SESSION['errors']));
    }

    protected function getErrors()
    {
        return $this->hasErrors() ? $_SESSION['errors'] : null;
    }

    protected function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}
